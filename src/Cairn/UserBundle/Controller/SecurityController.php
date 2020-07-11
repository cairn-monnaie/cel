<?php 

// src/Cairn/UserBundle/Controller/SecurityController.php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cairn\UserCyclosBundle\Entity\LoginManager;
use Cairn\UserCyclosBundle\Entity\BankingManager;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Deposit;
use Cairn\UserBundle\Entity\HelloassoConversion;
use Cairn\UserBundle\Entity\NotificationData;
use Cairn\UserBundle\Entity\BaseNotification;
use Cairn\UserBundle\Entity\PaymentNotification;
use Cairn\UserBundle\Entity\RegistrationNotification;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

/**
 * This class contains actions related to other applications as webhooks 
 */
class SecurityController extends BaseController
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


    public function getTokensAction(Request $request)
    {
        $apiService = $this->get('cairn_user.api');
        $session = $request->getSession();

        if($request->isMethod('POST')){

            $params = json_decode( htmlspecialchars($request->getContent(),ENT_NOQUOTES),true );

            $grantRequest = new Request($params);

            try{
                $oauth_token_data = $this->get('fos_oauth_server.server')->grantAccessToken($grantRequest);

                //send user id
                $em = $this->getDoctrine()->getManager();
                $userRepo = $em->getRepository('CairnUserBundle:User');
                $currentUser = $userRepo->findOneByUsername($params['username']);

                if(! $currentUser->isEnabled()){
                     $errors = ['user_account_disabled'=>[$currentUser->getUsername()]];
                     return $this->getErrorsResponse($errors,[], Response::HTTP_OK);
                }
            }catch(\Exception $e){
                $errors = ['invalid_authentification'=>[]];
                return $this->getErrorsResponse($errors, [], Response::HTTP_OK);
            }

            $array_oauth = json_decode($oauth_token_data->getContent(), true);

            $array_oauth['user_id'] =  $currentUser->getID();
            $array_oauth['first_login'] =  $currentUser->isFirstLogin();

            return $this->getRenderResponse('', [], $array_oauth, Response::HTTP_OK);

        }else{
            throw new NotFoundHttpException('POST Method required !');
        }
    }

    
    /**
     * Creates symfony equivalent for changes, deposits and withdrawals made in BDC app
     *
     */
    public function synchronizeAppsOperationsAction(Request $request, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $apiService = $this->get('cairn_user.api');
   
        $messageNotificator = $this->get('cairn_user.message_notificator');

        if($request->isMethod('POST')){
            $data = json_decode($request->getContent(),true);

            $networkInfo = $this->get('cairn_user_cyclos_network_info');
            $networkName = $this->getParameter('cyclos_currency_cairn');

            $anonymous = $this->getParameter('cyclos_anonymous_user');
            $credentials = array('username'=>$anonymous,'password'=>$anonymous);
            $networkInfo->switchToNetwork($networkName,'login', $credentials);

            if(array_key_exists('paymentID',$data)){ // there is a transfer to synchronize
                $cyclosTransfer = $this->get('cairn_user_cyclos_banking_info')->getTransferByID($data['paymentID']);

                //the validation process already ensures that such a transaction does not already exist in Symfony because the attribute
                //paymentID is unique. But we give another try on the paymentID of the returned transaction, just in case it is different
                $res = $em->getRepository('CairnUserBundle:Operation')->findOneBy(array('paymentID'=>$cyclosTransfer->id));
                if($res){
                    throw new SuspiciousOperationException('Payment already registered');
                }

                //Finally, we check that cyclos transfer data correspond to the POST request
                $amount = ($data['amount'] == $cyclosTransfer->currencyAmount->amount);
                $fromAccountNumber = ($data['fromAccountNumber'] == $cyclosTransfer->from->number);
                $toAccountNumber = ($data['toAccountNumber'] == $cyclosTransfer->to->number);

                if($amount && $fromAccountNumber && $toAccountNumber){
                    $operation = new Operation();
                    $operation->setDebitorName($this->get('cairn_user_cyclos_user_info')->getOwnerName($cyclosTransfer->from->owner));
                    $operation->setCreditorName($this->get('cairn_user_cyclos_user_info')->getOwnerName($cyclosTransfer->to->owner));
                    $operation->setAmount($data['amount']);
                    $operation->setPaymentID($data['paymentID']);

                    switch ($type){
                    case "conversion":
                        $operation->setCreditor($em->getRepository('CairnUserBundle:User')->findOneByMainICC($data['toAccountNumber']));
                        $operation->setType(Operation::TYPE_CONVERSION_BDC);
                        break;
                    case "deposit":
                        $operation->setCreditor($em->getRepository('CairnUserBundle:User')->findOneByMainICC($data['toAccountNumber']));
                        $operation->setType(Operation::TYPE_DEPOSIT);
                        break;
                    case "withdrawal":
                        $operation->setDebitor($em->getRepository('CairnUserBundle:User')->findOneByMainICC($data['fromAccountNumber']));
                        $operation->setType(Operation::TYPE_WITHDRAWAL);
                        break;
                    case "recurring":
                        $recurringPaymentData = $this->get('cairn_user_cyclos_banking_info')->getRecurringTransactionDataByID($data['transactionID']);

                        $operation->setDebitor($em->getRepository('CairnUserBundle:User')->findOneByMainICC($data['fromAccountNumber']));
                        $operation->setCreditor($em->getRepository('CairnUserBundle:User')->findOneByMainICC($data['toAccountNumber']));

                        $operation->setExecutionDate(new \Datetime($cyclosTransfer->date));
                        $operation->setSubmissionDate(new \Datetime($recurringPaymentData->transaction->date));


                        $operation->setRecurringID($data['transactionID']);
                        $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);

                        break;
                    case "scheduled":
                        $existingOperation = $em->getRepository('CairnUserBundle:Operation')->findOneBy(array('paymentID'=>$data['transactionID'],'type'=>Operation::TYPE_TRANSACTION_SCHEDULED));

                        if($existingOperation){
                            $operation = $existingOperation;
                        }

                        $operation->setExecutionDate(new \Datetime($cyclosTransfer->date));
                        $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);

                        break;

                    default:
                        throw new SuspiciousOperationException('Unexpected operation type');
                    }

                    $operation->setFromAccountNumber($data['fromAccountNumber']);
                    $operation->setToAccountNumber($data['toAccountNumber']);
                    
                    //there is not 'reason' property in Cyclos. Then, we use the only one available (description) to set up the 
                    //operation reason on Symfony side
                    $operation->setReason($data['description']);


                    $em->persist($operation);

                    //send notifications
                    $messageNotificator->sendPaymentNotifications($operation);

                    $em->flush();

                    return $this->getRenderResponse(
                        '',
                        [],
                        $operation,
                        Response::HTTP_CREATED
                    );
                }
            }else{ //there is a failed payment to notify

                $cyclosTransaction = $this->get('cairn_user_cyclos_banking_info')->getTransactionDataByID($data['transactionID'])->transaction;

                $debitor = $em->getRepository('CairnUserBundle:User')->findOneByUsername($cyclosTransaction->fromOwner->shortDisplay);
                if( ($type == "recurring") | ($type == "scheduled")){
                    if($data['status'] == 'FAILED'){
                        //send email to debitor user
                        $body = $this->get('templating')->render('CairnUserBundle:Emails:failed_transaction.html.twig',
                            array('cyclosTransaction'=>$cyclosTransaction));

                        $subject = 'Echec de votre virement programmé';
                        $from = $messageNotificator->getNoReplyEmail();
                        $to = $debitor->getEmail();
                        $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                        if($type == 'scheduled'){
                            $operation = $em->getRepository('CairnUserBundle:Operation')->findOneBy(array('paymentID'=>$data['transactionID']));
                            if(! $operation){
                                throw new SuspiciousOperationException('Scheduled payment not found on Symfony side');
                            }

                            $operation->setType(Operation::TYPE_SCHEDULED_FAILED);
                            $em->flush();
                        }

                        return $this->getRenderResponse(
                            '',
                            [],
                            [],
                            Response::HTTP_OK,
                            ['notif_sent'=>['email']]
                        );
                    }else{
                        return $this->getErrorsResponse([],[],Response::HTTP_BAD_REQUEST);
                    }
                }else{
                    return $this->getErrorsResponse(['invalid_field_value'=>['type']],[],Response::HTTP_BAD_REQUEST);
                }
            }
        }else{
            return $this->getErrorsResponse(['invalid_field_value'=>['method']],[],Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }


}
