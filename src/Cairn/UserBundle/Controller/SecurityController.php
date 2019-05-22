<?php 

// src/Cairn/UserBundle/Controller/ApiController.php

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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

/**
 * This class contains actions related to other applications as webhooks 
 */
class SecurityController extends Controller
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


    public function notificationSubscriptionAction(Request $request)
    {
        if($request->isMethod('POST')){

            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository('CairnUserBundle:User');

            $params = json_decode($request->getContent(),true);

            $networkInfo = $this->get('cairn_user_cyclos_network_info');
            $networkName = $this->getParameter('cyclos_currency_cairn');

            $token = $this->container->get('cairn_user.security')->vigenereDecode($params['token']);
            $networkInfo->switchToNetwork($networkName,'session_token', $token);

            try{
                $userVO = $this->get('cairn_user_cyclos_user_info')->getCurrentUser();
            }catch(\Exception $e){
                $response = new Response('Invalid authentication');
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $response->headers->set('Content-Type', 'application/json'); 
                $response->headers->set('Accept', 'application/json'); 
               
                return $response;
               
            }

            //validate access token
            if($userVO->shortDisplay != $params['username']){
                $response = new Response('Access denied');
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $response->headers->set('Content-Type', 'application/json'); 
                $response->headers->set('Accept', 'application/json'); 
               
                return $response;
            }


            //validate endpoint exists
            if(! array_key_exists('endpoint',$params['subscription'])){
                $response = new Response('Subscription must have an endpoint');
                $response->setStatusCode(Response::BAD_REQUEST);
                $response->headers->set('Content-Type', 'application/json'); 
                $response->headers->set('Accept', 'application/json'); 
               
                return $response;
            }

            $subscription = $params['subscription'];

            //validate keys because we need payload support
            if(! array_key_exists('keys',$params['subscription'])){
                $response = new Response('Subscription must have encryption keys');
                $response->setStatusCode(Response::BAD_REQUEST);
                $response->headers->set('Content-Type', 'application/json'); 
                $response->headers->set('Accept', 'application/json'); 
               
                return $response;
            }else{
                if( (! array_key_exists('p256dh',$subscription['keys'])) || (! array_key_exists('auth',$subscription['keys']))){
                    $response = new Response('Subscription must have valid encryption keys');
                    $response->setStatusCode(Response::BAD_REQUEST);
                    $response->headers->set('Content-Type', 'application/json'); 
                    $response->headers->set('Accept', 'application/json'); 
                   
                    return $response;
                }
            }


            $user = $userRepo->findOneByUsername($params['username']);

            $user->getSmsData()->addWebPushEndpoint($params['subscription']);

            $em->flush();

            $this->get('cairn_user.message_notificator')->sendNotification($user,'Notifications de paiement SMS [e]-Cairn','Ce navigateur est désormais enregistré comme destinataire des notifications de paiement par SMS');

            $response = new Response('OK');
            $response->setStatusCode(Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json'); 
            $response->headers->set('Accept', 'application/json'); 

            return $response;
        }else{
            $response = new Response('POST method required');
            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
            $response->headers->set('Content-Type', 'application/json'); 
            $response->headers->set('Accept', 'application/json'); 

            return $response;

        }
    }

    /**
     * Creates symfony equivalent for changes, deposits and withdrawals made in BDC app
     *
     */
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
                    $operation->setType(Operation::TYPE_CONVERSION_BDC);
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

    /**
     * Helloasso webhook used to notify our app at each payment received by the association in order to credit adherent (electronic change)
     *
     * Once a Helloassopayment is received, our app is notified through this webhook with relevant data (amount / payer info)
     * Then, we credit the identified adherent account with the same amount to achieve the electronic change.
     * If the received payment amount is greater than the currently available money safe balance, a deposit is created with the difference
     * Plus, the association is warned by email that the money safe is empty, and the adherent is notified about the asynchronous deposit
     * by email
     *
     * Example : 
     *  1) the adherent executes a helloasso payment of 1000 units but the money safe balance is 400 units.
     *  2) the adherent is credited of 400 units (cyclos-side)
     *  3) a deposit of 600 units is persisted in db to remind that there is still 600 units to credit
     *  4) email sent to association and to adherent
     */
    public function helloassoNotificationAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $helloassoRepo = $em->getRepository('CairnUserBundle:HelloassoConversion');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $messageNotificator = $this->get('cairn_user.message_notificator');

        if($request->isMethod('POST')){
             $data = $request->getContent();

             preg_match('#^id=(\d+)#',$data,$match_id);

             //look for helloassopayment with same id in helloasso data
//             $api_payment = $this->get('cairn_user_helloasso')->get('payments/'.$data['id']);
             $campaign_payments = $this->get('cairn_user.helloasso')->get('campaigns/000001033322/payments');

             $api_payment = NULL;
             foreach($campaign_payments->resources as $payment){
                 if($payment->id == $match_id[1]){
                    $api_payment = $payment;
                 }
             }

             if(! $api_payment){
                 $response = new Response('No payment found');
                 $response->headers->set('Content-Type', 'application/json');
                 $response->setStatusCode(Response::HTTP_NOT_FOUND);
                 return $response;
             }

             //look for helloassopayment with same id in db
             $existingPayment = $helloassoRepo->findOneByPaymentID($api_payment->id);
             if($existingPayment){
                $response = new Response('Helloasso payment already handled');
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_FORBIDDEN);
                return $response;
             }

             //create HelloAsso payment
             $helloasso = new HelloassoConversion();

             $helloasso->setPaymentID($api_payment->id);
             $helloasso->setDate(new \Datetime($api_payment->date));
             $helloasso->setAmount($api_payment->amount);
             $helloasso->setEmail($api_payment->payer_email);
             $helloasso->setCreditorName($api_payment->payer_last_name.' '.$api_payment->payer_first_name);

             $em->persist($helloasso);

             //do cyclos account credit

             $creditorUser = $userRepo->findOneByEmail($helloasso->getEmail());
             if(! $creditorUser){
                 $subject = 'Crédit de compte [e]-Cairn et virement Helloasso';
                 $from = $messageNotificator->getNoReplyEmail();
                 $to = $helloasso->getEmail();
                 $body = $this->renderView('CairnUserBundle:Emails:helloasso.html.twig',array('helloasso'=>$helloasso,'reason'=>'unfindable'));

                 $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                 $response = new Response('creditor user not found');
                 $response->headers->set('Content-Type', 'application/json');
                 $response->setStatusCode(Response::HTTP_NOT_FOUND);

                 $em->flush();
                 return $response;
             }

             //cyclos part
             try{
                 $bankingService = $this->get('cairn_user_cyclos_banking_info');

                 $username = $this->getParameter('cyclos_anonymous_user');                     
                 $credentials = array('username'=>$username,'password'=>$username); 
                 $network = $this->getParameter('cyclos_currency_cairn');                     

                 $this->get('cairn_user_cyclos_network_info')->switchToNetwork($network,'login',$credentials);

                 $paymentData = $bankingService->getPaymentData('SYSTEM',$creditorUser->getCyclosID(),NULL);
                 foreach($paymentData->paymentTypes as $paymentType){
                     if(preg_match('#credit_du_compte#', $paymentType->internalName)){
                         $creditTransferType = $paymentType;
                     }
                 }

                 //get account balance of e-cairns
                 $anonymousVO = $this->get('cairn_user_cyclos_user_info')->getCurrentUser();
                 $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($anonymousVO->id,NULL);

                 foreach($accounts as $account){
                    if(preg_match('#compte_de_debit_cairn_numerique#', $account->type->internalName)){
                        $debitAccount = $account;
                    }
                 }

                 $reason = 'Change numérique via virement Helloasso'; 

                 $availableAmount = $debitAccount->status->balance;

                 if($availableAmount >= 0){
                 $diff = $availableAmount - $helloasso->getAmount();
                 }else{
                    $diff = -$helloasso->getAmount();
                 }

                 if($diff <= 0 ){
                     $amountToCredit = $helloasso->getAmount() + $diff;

                     //notify gestion that ecairns stock is empty
                     $subject = 'Coffre [e]-Cairn vide !';
                     $from = $messageNotificator->getNoReplyEmail();
                     $to = $this->getParameter('cairn_email_management');
                     $body = $this->renderView('CairnUserBundle:Emails:helloasso.html.twig',array('helloasso'=>$helloasso,'reason'=>'empty_safe'));

                     $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                     if($diff < 0){
                         $asynchronousDeposit = new Deposit($creditorUser);
                         $asynchronousDeposit->setAmount(-$diff);
                         //notify user that a deposit is created
                         $subject = 'Acompte [e]-Cairn suite au virement Helloasso';
                         $from = $messageNotificator->getNoReplyEmail();
                         $to = $helloasso->getEmail();
                         $body = $this->renderView('CairnUserBundle:Emails:helloasso.html.twig',array('helloasso'=>$helloasso,'reason'=>'asynchronous_deposit','diff'=> -$diff));

                         $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                         $em->persist($asynchronousDeposit);
                     }

                     if($amountToCredit > 0){
                         $res = $this->bankingManager->makeSinglePreview($paymentData,$amountToCredit,$reason,$creditTransferType,new \Dateitme());
                     }else{

                         $em->flush();

                         $response = new Response('No possible credit for now');
                         $response->headers->set('Content-Type', 'application/json');
                         $response->setStatusCode(Response::HTTP_OK);

                         return $response;
                     }
                 }else{
                     $res = $this->bankingManager->makeSinglePreview($paymentData,$helloasso->getAmount(),$reason,$creditTransferType,new \Datetime());
                 }

                 //preview allows to make sure payment would be executed according to provided data
                 $paymentVO = $this->bankingManager->makePayment($res->payment);
             }catch(Exception $e){
                 $subject = 'ERREUR lors de virement Helloasso';
                 $from = $messageNotificator->getNoReplyEmail();
                 $to = $this->getParameter('cairn_email_management');
                 $body = $this->renderView('CairnUserBundle:Emails:helloasso.html.twig',array('helloasso'=>$helloasso,'reason'=>'cyclos_payment'));

                 $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                 throw $e;
             }

             //once payment is done, write symfony equivalent

             $operation = new Operation();
             $operation->setType(Operation::TYPE_CONVERSION_HELLOASSO);
             $operation->setReason($reason);
             $operation->setPaymentID($paymentVO->id);
             $operation->setFromAccountNumber($res->fromAccount->number);
             $operation->setToAccountNumber($res->toAccount->number);
             $operation->setAmount($res->totalAmount->amount);
             $operation->setDebitorName($this->get('cairn_user_cyclos_user_info')->getOwnerName($res->fromAccount->owner));
             $operation->setCreditor($creditorUser);

             $em->persist($operation);

             $em->flush();

             $response = new Response('helloasso payment handled');
             $response->headers->set('Content-Type', 'application/json');
             $response->setStatusCode(Response::HTTP_OK);

             return $response;

        }
    }
}
