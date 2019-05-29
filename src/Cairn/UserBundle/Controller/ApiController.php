<?php 

// src/Cairn/UserBundle/Controller/ApiController.php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cairn\UserCyclosBundle\Entity\BankingManager;

use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\OnlinePayment;


/**
 * This class contains actions related to other applications as webhooks 
 */
class ApiController extends Controller
{
    /**
     * Deals with all account actions to operate on Cyclos-side
     *@var BankingManager $bankingManager
     */
    private $bankingManager;

    public function __construct()
    {
        $this->bankingManager = new BankingManager();
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
            $response = new Response('Invalid JSON');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            return $response;
        }


        $postParameters = json_decode($request->getContent(),true);

        $postAccountNumber = $postParameters['account_number'];


        if($creditorUser->getMainICC() != $postAccountNumber ){
            $response = new Response('User not found with account number ' .$postAccountNumber);
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        if(! $creditorUser->hasRole('ROLE_PRO')){
            $response = new Response('Access denied');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        if(! $creditorUser->getApiClient()){
            $response = new Response('User has no data to perform online payment');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_PRECONDITION_FAILED);
            return $response;
        }

        if(! $creditorUser->getApiClient()->getWebhook()){
            $response = new Response('User has no webhook to perform online payment');
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

        $onlinePayment->setUrlSuccess($postParameters['return_url_success']);
        $onlinePayment->setUrlFailure($postParameters['return_url_failure']);
        $onlinePayment->setAmount($postParameters['amount']);
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
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

}
