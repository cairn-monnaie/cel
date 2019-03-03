<?php 

// src/Cairn/UserBundle/Controller/ApiController.php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cairn\UserCyclosBundle\Entity\LoginManager;
use Cairn\UserBundle\Entity\Operation;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class SecurityController extends Controller
{
    public function getTokensAction(Request $request)
    {
        if($request->isMethod('POST')){

            $params = $request->request->all();

            $grantRequest = new Request(array(
                'client_id'  => $params['client_id'],
                'client_secret' => $params['client_secret'],
                'grant_type' => $params['grant_type'],
                'username' => $params['username'],
                'password' => $params['password']
            ));

            $oauth_token_data = $this->get('fos_oauth_server.server')->grantAccessToken($grantRequest);
            $array_oauth = json_decode($oauth_token_data->getContent(), true);

            // ********* get Cyclos Token ****************
            $networkInfo = $this->get('cairn_user_cyclos_network_info');
            $networkName = $this->getParameter('cyclos_currency_cairn');
            $loginManager = new LoginManager();

            $credentials = array('username'=>$params['username'],'password'=> $params['password']);     
            $networkInfo->switchToNetwork($networkName,'login',$credentials);      

            //set cyclos session timeout
            $dto = new \stdClass();                                                
            $dto->amount = $this->getParameter('session_timeout');               
            $dto->field = 'SECONDS';   

            //effectively log in and get session token
            $loginResult = $loginManager->login($dto);                             
            $array_oauth['cyclos_token'] =  $loginResult->sessionToken;


            $response =  new Response(json_encode($array_oauth));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
            throw new NotFoundHttpException('POST Method required !');
        }
    }

    public function synchronizeOperationAction(Request $request, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $operation = new Operation();

        if($request->isMethod('POST')){
            $data = json_decode($request->getContent(),true);

            $networkInfo = $this->get('cairn_user_cyclos_network_info');
            $networkName = $this->getParameter('cyclos_currency_cairn');
            $networkInfo->switchToNetwork($networkName,'session_token', $data['cyclos_token']);

            //first, we check that the provided paymentID matches an operation in Cyclos
            //for now, this is a 'transfer' because we do not deal with scheduled conversions. It means that, in Cyclos,
            //any transaction has its associated transfer
            $cyclosTransfer = $this->get('cairn_user_cyclos_banking_info')->getTransferByID($data['paymentID']);

            //the validation process already ensures that such a transaction does not already exist in Symfony because the attribute
            //paymentID is unique. But we give another try on the paymentID of the returned transaction, just in case it is different
            $res = $em->getRepository('CairnUserBundle:Operation')->findOneBy(array('paymentID'=>$cyclosTransfer->id));
            if($res){
                throw new SuspiciousOperationException('Payment already registered');
            }

            //Finally, we check that cyclos transfer data correspond to the POST request
            $amount = ($data['amount'] == $cyclosTransfer->currencyAmount->amount);
#            $description = ($data['description'] == $cyclosTransfer->description);
            $fromAccountNumber = ($data['fromAccountNumber'] == $cyclosTransfer->from->number);
            $toAccountNumber = ($data['toAccountNumber'] == $cyclosTransfer->to->number);

            if($amount && $fromAccountNumber && $toAccountNumber){
                $operation->setPaymentID($data['paymentID']);
                $operation->setFromAccountNumber($data['fromAccountNumber']);
                $operation->setToAccountNumber($data['toAccountNumber']);
                $operation->setAmount($data['amount']);

                //there is not 'reason' property in Cyclos. Then, we use the only one available (description) to set up the 
                //operation reason on Symfony side
                $operation->setReason($data['description']);
                $operation->setDebitorName($this->get('cairn_user_cyclos_user_info')->getOwnerName($cyclosTransfer->from->owner));
                $operation->setCreditorName($this->get('cairn_user_cyclos_user_info')->getOwnerName($cyclosTransfer->to->owner));

                switch ($type){
                case "conversion":
                    $operation->setCreditor($em->getRepository('CairnUserBundle:User')->findOneByName($operation->getCreditorName()));
                    $operation->setType(Operation::TYPE_CONVERSION);
                    break;
                case "deposit":
                    $operation->setCreditor($em->getRepository('CairnUserBundle:User')->findOneByName($operation->getCreditorName()));
                    $operation->setType(Operation::TYPE_DEPOSIT);
                    break;
                case "withdrawal":
                    $operation->setDebitor($em->getRepository('CairnUserBundle:User')->findOneByName($operation->getDebitorName()));
                    $operation->setType(Operation::TYPE_WITHDRAWAL);
                    break;
                default:
                    throw new SuspiciousOperationException('Unexpected operation type');
                }

                $em->persist($operation);
                $em->flush();
                $response = new Response('Operation synchronized');
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->headers->set('Content-Type', 'application/json'); 
                $response->headers->set('Accept', 'application/json'); 

                return $response;
            }else{
                $response = new Response('Synchronization failed');
                $response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
                $response->headers->set('Content-Type', 'application/json'); 

                return $response;
            }
        }else{
            $response = new Response('POST method accepted !');
            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
            $response->headers->set('Content-Type', 'application/json'); 

            return $response;
        }
    }


}
