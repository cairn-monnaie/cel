<?php 

// src/Cairn/UserBundle/Controller/ApiController.php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\OnlinePayment;
use Cairn\UserBundle\Entity\User;


/**
 * This class contains actions related to other applications as webhooks and specific API functions 
 */
class ApiController extends Controller
{

    public function phonesAction(Request $request)
    {
        $user = $this->getUser();
        $phones = $user->getPhones(); 
        $phones = is_array($phones) ? $phones : $phones->getValues();

        $res = $this->get('cairn_user.api')->serialize($phones);

        $response = new Response($res);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    public function usersAction(Request $request)
    {
        $currentUser = $this->getUser();
        $currentUserID = $currentUser->getID();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        $ub = $userRepo->createQueryBuilder('u');

        if($currentUser->isAdherent()){
            $userRepo->whereEnabled($ub,true)->whereAdherent($ub)->whereConfirmed($ub);
        }else{
            $userRepo->whereReferent($ub, $currentUserID);
        }

        $users = $ub->getQuery()->getResult();
        $res = $this->get('cairn_user.api')->serialize($users);

        $response = new Response($res);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    public function createOnlinePaymentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $securityService = $this->get('cairn_user.security');
        $apiService = $this->get('cairn_user.api');

        //if no user found linked to the domain name

        $creditorUser = $this->getUser();
        if(! $creditorUser ){
            return $apiService->getErrorResponse(array('User account not found') ,Response::HTTP_FORBIDDEN);
        }

        if(! ($request->headers->get('Content-Type') == 'application/json')){
            return $apiService->getErrorResponse(array('Invalid JSON') ,Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        //no possible code injection
        $postParameters = json_decode( htmlspecialchars($request->getContent(),ENT_NOQUOTES),true );

        $postAccountNumber = $postParameters['account_number'];


        if($creditorUser->getMainICC() != $postAccountNumber ){
            return $apiService->getErrorResponse(array('User not found with provided account number') ,Response::HTTP_NOT_FOUND);
        }

        if(! $creditorUser->hasRole('ROLE_PRO')){
            return $apiService->getErrorResponse(array('Access denied') ,Response::HTTP_UNAUTHORIZED);
        }

        if(! $creditorUser->getApiClient()){
            return $apiService->getErrorResponse(array('User has no data to perform online payment') ,Response::HTTP_PRECONDITION_FAILED);
        }

        if(! $creditorUser->getApiClient()->getWebhook()){
            return $apiService->getErrorResponse(array('No webhook defined to perform online payment') ,Response::HTTP_PRECONDITION_FAILED);
        }

        $oPRepo = $em->getRepository('CairnUserBundle:OnlinePayment');

        $onlinePayment = $oPRepo->findOneByInvoiceID($postParameters['invoice_id']);

        if($onlinePayment){
            $suffix = $onlinePayment->getUrlValidationSuffix();
        }else{
            $onlinePayment = new OnlinePayment();
            $suffix = preg_replace('#[^a-zA-Z0-9]#','@',$securityService->generateToken());
            $onlinePayment->setUrlValidationSuffix($suffix);
            $onlinePayment->setInvoiceID($postParameters['invoice_id']);
        }

        //validate POST content
        if( (! is_numeric($postParameters['amount']))   ){
            return $apiService->getErrorResponse(array('No numeric amount') ,Response::HTTP_BAD_REQUEST);
        }

        $numericalAmount = floatval($postParameters['amount']);
        $numericalAmount = round($numericalAmount,2); 

        if( $numericalAmount < 0.01  ){
            return $apiService->getErrorResponse(array('Amount too low') ,Response::HTTP_BAD_REQUEST);
        }

        if(! preg_match('#^(http|https):\/\/#',$postParameters['return_url_success'])){
            return $apiService->getErrorResponse(array('Invalid return_url_success format value') ,Response::HTTP_BAD_REQUEST);
        }

        if(! preg_match('#^(http|https):\/\/#',$postParameters['return_url_failure'])){
            return $apiService->getErrorResponse(array('Invalid return_url_failure format value') ,Response::HTTP_BAD_REQUEST);
        }

        if( strlen($postParameters['reason']) > 35){                                  
            return $apiService->getErrorResponse(array('Reason too long : 35 characters allowed') ,Response::HTTP_BAD_REQUEST);
        } 

        //finally register new onlinePayment data
        $onlinePayment->setUrlSuccess($postParameters['return_url_success']);
        $onlinePayment->setUrlFailure($postParameters['return_url_failure']);
        $onlinePayment->setAmount($numericalAmount);
        $onlinePayment->setAccountNumber($postParameters['account_number']);
        $onlinePayment->setReason($postParameters['reason']);

        $em->persist($onlinePayment);
        $em->flush();

        $payload = array(
            'invoice_id' => $postParameters['invoice_id'],
            'redirect_url' => $this->generateUrl('cairn_user_online_payment_execute',array('suffix'=>$suffix),UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $response = new Response(json_encode($payload) );
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_CREATED);
        return $response;
    }

}
