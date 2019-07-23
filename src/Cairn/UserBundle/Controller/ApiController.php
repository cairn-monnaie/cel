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

        //if no user found linked to the domain name

        $creditorUser = $this->getUser();
        if(! $creditorUser ){
            $response = new Response('User account not found');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            return $response;
        }

        if(! ($request->headers->get('Content-Type') == 'application/json')){
            $response = new Response(' { "message"=>"Invalid JSON" }');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            return $response;
        }

        //no possible code injection
        $postParameters = json_decode( htmlspecialchars($request->getContent(),ENT_NOQUOTES),true );

        $postAccountNumber = $postParameters['account_number'];


        if($creditorUser->getMainICC() != $postAccountNumber ){
            $response = new Response(' { "message"=>"User not found with provided account number"} ');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        if(! $creditorUser->hasRole('ROLE_PRO')){
            $response = new Response(' { "message"=>"Access denied"} ');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        if(! $creditorUser->getApiClient()){
            $response = new Response(' { "message"=>"User has no data to perform online payment"} ');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_PRECONDITION_FAILED);
            return $response;
        }

        if(! $creditorUser->getApiClient()->getWebhook()){
            $response = new Response(' { "message"=>"No webhook defined to perform online payment"} ');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_PRECONDITION_FAILED);
            return $response;
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
            $response = new Response(' { "message"=>"No numeric amount"} ');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }

        $numericalAmount = floatval($postParameters['amount']);
        $numericalAmount = round($numericalAmount,2); 

        if( $numericalAmount < 0.01  ){
            $response = new Response(' { "message"=>"Amount too low"} ');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }

        if(! preg_match('#^(http|https):\/\/#',$postParameters['return_url_success'])){
            $response = new Response(' { "message"=>"Invalid return_url_success format value" }');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }

        if(! preg_match('#^(http|https):\/\/#',$postParameters['return_url_failure'])){
            $response = new Response(' { "message"=>"Invalid return_url_failure format value" }');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }

        if( strlen($postParameters['reason']) > 35){                                  
            $response = new Response(' { "Reason too long : 35 characters allowed" }');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
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
