<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Sms;
use Cairn\UserBundle\Entity\Phone;

use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\BankingManager;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * This class contains actions related to sms operations 
 */
class SmsController extends Controller
{

    /**
     * Deals with all user management actions to operate on Cyclos-side
     *@var UserManager $userManager
     */
    private $userManager;                                                      

    private $bankingManager;                                                      

    public function __construct()                                              
    {                                                                          
        $this->userManager = new UserManager();
        $this->bankingManager = new BankingManager();
    }   

    public function downloadPosterAction(Request $request)
    {
        $dir = $this->getParameter('kernel.project_dir').'/web/';
        $filename = 'poster_sms.pdf';
        return new BinaryFileResponse($dir.$filename);
    }

    public function smsReceptionAction(Request $request)
    {
        $env = $this->getParameter('kernel.environment');
        if($env == 'test'){
            $phoneNumber = '+'.trim($request->query->get('phone'));
            $res = $this->smsAction($phoneNumber,$request->query->get('content'));
        }elseif($env == 'prod'){
//
//        $this->smsAction('+33788888888','LOGIN');
//            $this->smsAction('+33611223344','2222');
//        $this->smsAction('+33611223344','1111');
//
            //+33744444444', 'SOLDE
        $res =  $this->smsAction('+33612345678','PAYER 10 MALTOBAR');
//
//        $this->smsAction('+33612345678','2222');
        $res = $this->smsAction('+33612345678','1111');
//        $this->smsAction('+33655667788','SOLDE');
//        $this->smsAction('+33655667788','2222');
//        $this->smsAction('+33655667788','1111');
        }elseif($env == 'dev'){

            parse_str( $request->getQueryString(), $query) ;

            if(! htmlspecialchars($query['originator']) == $this->getParameter('notificator_consts')['sms']['originator']){
                $response = new Response(' { "message"=>"Invalid request" }');
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                return $response;
            } 

            $sender_phoneNumber = preg_replace('#^0033#','+33',htmlspecialchars($query['recipient']) );

            $res = $this->smsAction($sender_phoneNumber,$query['message']);
        }

        if(! $res){
            $response = new Response(' { "message"=>"Payment aborted" }');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;

        }else{
            $response = new Response(' { "message"=>"Payment OK !" }');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }


    /**
     * Analyzes received sms, parses it and hydrates the object to return with relevant information
     *
     * The received SMS content is parsed through different regex enumerating allowed sms formats. If none of these formats is matched by 
     * the content, the function tries to have a deeper analysis of the error.
     * The returned object $res contains attributes which permit to know which operation is requested (payment/account balance/validation)
     * and an error attribute if necessary
     *
     * @return stdClass $res
     */
    public function parseSms($content)
    {
        //1) content treatment : escape spaces & white chars at beginning and end
        $content = trim($content);

        //2) replace all characters to uppercase chars
        $content = strtoupper($content);

        //3)Regex analysis
        //TODO : make it more flexible
        //PAYER autoriser plus de décimales au montant et tronquer après
        preg_match('#^(PAYER|PAYEZ|PAYE|PAY)\s*(\d+([,\.]\d+)?)\s*([0-9A-Z]{1}\w+)$#',$content,$matches_payment);
        preg_match('#^SOLDE$#',$content,$matches_balance);
        preg_match('#^\d{4}$#',$content, $matches_code);
        preg_match('#^LOGIN$#',$content, $matches_login);
       
        //3) Prepare error messages
        $res = new \stdClass();
        
        $error = NULL;

        if(! ($matches_payment || $matches_balance || $matches_code || $matches_login)){
            if(! preg_match('#^(PAYER|SOLDE|LOGIN|\d{4})#',$content)){
                $error = 'Envoyer PAYER, SOLDE ou un code à 4 chiffres en cas de validation de paiement';
            }else{
                if(preg_match('#^PAY#',$content)){ //is payment request
//                    if(! preg_match('#^PAY[A-Z]*\d+([,\.]\d+)?[A-Z]{1}#',$content)){//invalid amount format
                    $error = 'Format du montant invalide ou identifiant SMS incorrect';
//                    }else{
//                        $error = 'Un identifiant SMS contient des caractères alphanumériques'."\n";
//                    }
                }elseif(preg_match('#^SOLDE#',$content)){
                    $error = 'Demande de solde invalide '."\n";
                }elseif(preg_match('#^LOGIN#',$content)){
                    $error = 'Demande d\'identifiant SMS invalide '."\n";
                }elseif(preg_match('#^\d#',$content)){
                    $error = 'Saisissez un code à 4 chiffres '."\n";
                }else{
                    $error = '';
                }
            }
        }else{ //one regex match
            $res->content = $content;
            if($matches_payment){
                $res->isPaymentRequest = true;
                $res->isOperationValidation = false;
                $res->isSmsIdentifier = false;
                $res->amount = str_replace(',','.',$matches_payment[2]);
                $res->creditorIdentifier = $matches_payment[4];
            }elseif($matches_balance){
                $res->isPaymentRequest = false;
                $res->isOperationValidation = false;
                $res->isSmsIdentifier = false;
            }elseif($matches_login){//SMS identifier requested
                $res->isPaymentRequest = false;
                $res->isOperationValidation = false;
                $res->isSmsIdentifier = true;
            }else{
                $res->isPaymentRequest = false;
                $res->isOperationValidation = true;
                $res->isSmsIdentifier = false;
                $res->cardKey = $matches_code[0];
            }
        }

        $res->error = $error;
        return $res;

    }

    /**
     * Simple function to persist sms if it exists in order to have the "if" clause only once
     *
     */
    private function persistSMS(Sms $sms = NULL)
    {
        if($sms){
            $this->getDoctrine()->getManager()->persist($sms);
        }
    }

    /**
     * Analyzes the received SMS data (phone number / content) then handles it 
     *
     * The phone number must match an existing and enabled adherent. Then, the SMS is parsed in order to understand the request and handle
     * it. The user is connected to Cyclos through its sms access client to process operaation on Cyclos-side (payment / account balance)
     *
     */
    public function smsAction($debitorPhoneNumber,$content)
    {
        $em = $this->getDoctrine()->getManager();
        $smsRepo = $em->getRepository('CairnUserBundle:Sms');
        $messageNotificator = $this->get('cairn_user.message_notificator');
        $securityService = $this->get('cairn_user.security');

        //TODO here : get data from SMS and parse content

        $debitorUsers = $em->getRepository('CairnUserBundle:User')->findUsersByPhoneNumber($debitorPhoneNumber);
        $isUniquePhoneNumber = (count($debitorUsers) == 1);
        $isProAndPersonPhoneNumber = (count($debitorUsers) == 2);


        //1) we ensure that SMS sender exists
        //if there are two members with same phone number, ROLE_PERSON is used by default
        if($isProAndPersonPhoneNumber){
            $debitorUser = ($debitorUsers[0]->hasRole('ROLE_PERSON')) ? $debitorUsers[0] : $debitorUsers[1];
        }elseif($isUniquePhoneNumber){
            $debitorUser = $debitorUsers[0];
        }else{
            return;
        }      

        //then, we check that today's user activity is not considered as spam
        $nbSpamSms = $smsRepo->getNumberOfSmsToday($debitorPhoneNumber, Sms::STATE_SPAM);
        if($nbSpamSms > 0){ return;}

        //2.1)Then, we ensure that user is active, and then sms actions are enabled for this user
        if(! $debitorUser->isEnabled()){
             $smsUnauthorized = new Sms($debitorPhoneNumber, $content, Sms::STATE_UNAUTHORIZED, NULL);

             $sms = $messageNotificator->sendSMS($debitorPhoneNumber,'SMS NON AUTORISÉ: le compte est actuellement bloqué',$smsUnauthorized);

             $em->persist($smsUnauthorized);
             $this->persistSMS($sms);
             $em->flush();
             return;
        }

        //2.2)Then, we ensure that sms actions are enabled for this user
        $userPhone = $em->getRepository('CairnUserBundle:Phone')->findByUser($debitorUser, $debitorPhoneNumber);
        if(! $userPhone->isPaymentEnabled()){
             $smsUnauthorized = new Sms($debitorPhoneNumber, $content, Sms::STATE_UNAUTHORIZED, NULL);

             $sms = $messageNotificator->sendSMS($debitorPhoneNumber,'SMS NON AUTORISÉ: les paiements SMS depuis ce numéro sont interdits',$smsUnauthorized);

             $em->persist($smsUnauthorized);
             $this->persistSMS($sms);
             $em->flush();

             return;
        }

        //3) Connect the user to Cyclos via access client
        try{
            $networkInfo = $this->get('cairn_user_cyclos_network_info');
            $networkName = $this->getParameter('cyclos_currency_cairn');
            $accessClient = $securityService->getSmsClient($debitorUser);

            if(!$accessClient){
                $subject = 'Accès client Cyclos';
                $from = $this->getParameter('cairn_email_noreply');
                $to = $this->getParameter('cairn_email_technical_services');
                $body = 'L\'utilisateur '.$debitorUser->getUsername().' n\'a aucun accès client Cyclos ';

                $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                $smsError = new Sms($debitorPhoneNumber, $content, Sms::STATE_ERROR, NULL);

                $sms = $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : L\'Association en a été informée.',$smsError);

                $em->persist($smsError);
                $this->persistSMS($sms);
                $em->flush();

                return; 
            }
            $networkInfo->switchToNetwork($networkName,'access_client', $accessClient);

            $debitorUserVO = $this->get('cairn_user_cyclos_user_info')->getCurrentUser();

        }catch(\Exception $e){

            if($e->errorCode == 'INVALID_ACCESS_CLIENT'){
                $smsError = new Sms($debitorPhoneNumber, $content, Sms::STATE_ERROR, NULL);

                $sms = $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : L\'Association en a été informée.',$smsError);

                $em->persist($smsError);
                $this->persistSMS($sms);
                $em->flush();

                //if not spam, sms entity is returned
                if( $sms){
                    $subject = 'Accès client Cyclos';
                    $from = $this->getParameter('cairn_email_noreply');
                    $to = $this->getParameter('cairn_email_technical_services');
                    $body = 'Accès client invalide pour '.$debitorUser->getUsername();

                    $messageNotificator->notifyByEmail($subject,$from,$to,$body);
                }
                return;

            }else{
                $smsError = new Sms($debitorPhoneNumber, $content, Sms::STATE_ERROR, NULL);

                $sms = $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : L\'Association en a été informée.',$smsError);

                $em->persist($smsError);
                $this->persistSMS($sms);
                $em->flush();

                if($sms){
                    throw $e;
                }else{
                    return;
                }
            }
        }

        //4) Parse SMS content
        $parsedSms = $this->parseSms($content);


        if( $parsedSms->error){
            $smsFormat = new Sms($debitorPhoneNumber, $content, Sms::STATE_INVALID, NULL);

            $reason = 'SMS INVALIDE : '."\n".$parsedSms->error;
            $smsError = $messageNotificator->sendSMS($debitorPhoneNumber,$reason,$smsFormat);

            $em->persist($smsFormat);
            $this->persistSMS($smsError);
            $em->flush();
            return;
        }

        if(! ($parsedSms->isPaymentRequest || $parsedSms->isOperationValidation)){//account balance or SMS Identifier
            if($parsedSms->isSmsIdentifier && !$debitorUser->hasRole('ROLE_PRO')){
               return;
            }
            $this->setUpSmsValidation($em, $debitorUser, $content, $userPhone);
            $em->flush();
            return;
        }elseif($parsedSms->isOperationValidation){
            $smsPending = $em->getRepository('CairnUserBundle:Sms')->findOneBy(array('phoneNumber'=>$debitorPhoneNumber,
                                                                          'state'=>Sms::STATE_WAITING_KEY));

            if(!$smsPending){
                $smsUseless = new Sms($debitorPhoneNumber, $parsedSms->cardKey,Sms::STATE_PROCESSED, NULL);
                $em->persist($smsUseless);

                $em->flush();
                return;
            }elseif($smsPending->getSentAt()->diff(new \Datetime())->i > 5){
                $smsPending->setState(Sms::STATE_EXPIRED);

                $smsUseless = new Sms($debitorPhoneNumber, $parsedSms->cardKey,Sms::STATE_PROCESSED, $smsPending->getCardPosition());

                $smsSent = $messageNotificator->sendSMS($debitorPhoneNumber,'Délai de validation expiré',$smsPending);

                $em->persist($smsUseless);
                $this->persistSMS($smsSent);
                $em->flush();

                return;
            }

            //pending SMS (state = WAITING_KEY) must be treated
            $smsValidation = new Sms($debitorPhoneNumber, $parsedSms->cardKey,Sms::STATE_PROCESSED, $smsPending->getCardPosition());
            $em->persist($smsValidation);

            $event = new InputCardKeyEvent($debitorUser,$parsedSms->cardKey,$smsPending->getCardPosition(), NULL);
            $this->get('event_dispatcher')->dispatch(SecurityEvents::INPUT_CARD_KEY,$event);

            $card = $debitorUser->getCard();
            $nbTries = $debitorUser->getCardKeyTries();
            $remainingTries = 3 - $nbTries;

            if( ($nbTries  > 0) && ($remainingTries > 0)){
                $smsSent = $messageNotificator->sendSMS($debitorPhoneNumber,'Échec du code de sécurité : '.$remainingTries.' essai(s) restant(s)', $smsValidation);
                $this->persistSMS($smsSent);
                $em->flush();
                return;
            }elseif($remainingTries == 0){
                $smsSent = $messageNotificator->sendSMS($debitorPhoneNumber,'Échec du code de sécurité : Le compte a été bloqué. Veuillez contacter l\'Association', $smsValidation);

                $smsPending->setState(Sms::STATE_CANCELED);
                $this->persistSMS($smsSent);
                $em->flush();

                return;
            }

            //get initial sms request
            $parsedInitialSms = $this->parseSms($smsPending->getContent());
            if($parsedInitialSms->isSmsIdentifier){
                if($debitorUser->hasRole('ROLE_PRO')){
                    $smsSent = $messageNotificator->sendSMS($debitorPhoneNumber,'Identifiant SMS [e]-Cairn : '.$userPhone->getIdentifier(), $smsPending);
                    $this->persistSMS($smsSent);
                    return true;
                }
            }elseif($parsedInitialSms->isPaymentRequest){
                //at this stage, as it is a validation action, it means that payment data has already been checked and validated
                $res = $this->executePayment($debitorUser, $parsedInitialSms, false, $userPhone);    

                if($res){return true;}else{return NULL;}

            }else{//initial sms was about account balance
                $account = $this->get('cairn_user_cyclos_account_info')->getDefaultAccount($debitorUser->getCyclosID()); 
                $smsBalance=$messageNotificator->sendSMS($debitorPhoneNumber,'Votre solde compte [e]-Cairn : '.$account->status->balance,$smsPending);

                $this->persistSMS($smsBalance);
                return true;
            }
          
            //once pending request has been executed, the corresponding SMS can be set to PROCESSED
            $smsPending->setState(Sms::STATE_PROCESSED);
            $em->flush();
            return true;
        }

        //last option : sms request is a payment to be validated
        $res = $this->executePayment($debitorUser,$parsedSms, true, $userPhone);    
       
        $em->flush();

         if($res){return true;}else{return NULL;}
    }


    /**
     * Analyzes the payment data then executes the payment requested by SMS
     *
     * We check that the payment is not suspicious (too many payments the same day, or too high amount), then we validate the operation
     * with our custom operation validator
     *
     * @param User $debitorUser adherent to be debited
     * @param stdClass $parsedSms object returned after sms is parsed. It contains an amount and a sms identifier
     * @param boolean $toAnalyze if false, the request payment has already been analyzed and validated : just process it
     * @see \Cairn\UserBundle\Validator\OperationValidator
     */
    public function executePayment($debitorUser, $parsedSms, $toAnalyze, Phone $debitorPhone)
    {
        $em = $this->getDoctrine()->getManager();
        $messageNotificator = $this->get('cairn_user.message_notificator');
        $securityService = $this->get('cairn_user.security');

        $debitorPhoneNumber = $debitorPhone->getPhoneNumber();

        $operation = new Operation();
        $operation->setType(Operation::TYPE_SMS_PAYMENT);

        $operationAmount = floatval($parsedSms->amount);
        $operation->setAmount($operationAmount);

        $operation->setDebitor($debitorUser);

        $isSuspicious = $securityService->paymentIsSuspicious($operation);
        if($isSuspicious) {
            $smsPaymentRequest = new Sms($debitorPhoneNumber, $parsedSms->content,Sms::STATE_SUSPICIOUS,NULL );

            //notify user and admin by email
            $subject = 'Paiement SMS suspicieux';
            $body = $this->get('templating')->render('CairnUserBundle:Emails:suspicious_sms.html.twig',array(
                'operation'=>$operation,'toAdmin'=>false));

            $messageNotificator->notifyByEmail($subject, $this->getParameter('cairn_email_noreply'),$debitorUser->getEmail(), $body);


            $body = $this->get('templating')->render('CairnUserBundle:Emails:suspicious_sms.html.twig',array(
                'operation'=>$operation,'toAdmin'=>true));

            $messageNotificator->notifyByEmail($subject, $this->getParameter('cairn_email_noreply'),$this->getParameter('cairn_email_management'), $body);

            //oppose user and send sms
            $smsBlocked = $messageNotificator->sendSMS($debitorPhoneNumber,'SMS bloqués : opération jugée suspicieuse. Veuillez contacter l\'Association',$smsPaymentRequest);

            $debitorPhone->setPaymentEnabled(false);

            $em->persist($smsPaymentRequest); 
            $this->persistSMS($smsBlocked);
            $em->flush();
            return;
        }

        //then we check that creditor user is valid
        $creditorPhone = $em->getRepository('CairnUserBundle:Phone')->findOneByIdentifier(strtoupper($parsedSms->creditorIdentifier));
        if(! $creditorPhone){
            $smsInvalid = new Sms($debitorPhoneNumber, $parsedSms->content, Sms::STATE_INVALID, NULL);
            $smsError = $messageNotificator->sendSMS($debitorPhoneNumber,'Identifiant SMS '.$parsedSms->creditorIdentifier.' ne correspond à aucun professionnel',$smsInvalid);

            $em->persist($smsInvalid);
            $this->persistSMS($smsError);
            return;
        }

        $creditorUser = $creditorPhone->getUser();
        if(! $creditorUser->hasRole('ROLE_PRO')){
            $smsInvalid = new Sms($debitorPhoneNumber, $parsedSms->content, Sms::STATE_INVALID, NULL);
            $em->persist($smsInvalid);

            return;
        }


        $creditorPhoneNumber = $creditorPhone->getPhoneNumber();
        $operation->setCreditor($creditorUser);

        if($toAnalyze){
            $validator = $this->get('validator');
            $listErrors = $validator->validate($operation);

            if( count($listErrors) > 0 ){
                $content = '';
                foreach($listErrors as $error){
                    $content = $content.$error->getMessage()."\n";
                }
                $smsInvalid = new Sms($debitorPhoneNumber, $parsedSms->content, Sms::STATE_INVALID, NULL);

                $smsErrors = $messageNotificator->sendSMS($debitorPhoneNumber, $content, $smsInvalid);

                $em->persist($smsInvalid);
                $this->persistSMS($smsErrors);
                $em->flush();

                return;
            }
        }

        $reason = 'Paiement par SMS';
        $description = 'Paiement effectué le '.$operation->getExecutionDate()->format('d-m-Y').' à '.$operation->getExecutionDate()->format('H:i').' à destination de '.$creditorUser->getName();
        $operation->setReason($reason);
        $operation->setDescription($description);

        $bankingService = $this->get('cairn_user_cyclos_banking_info');

        //make payment on Cyclos-side
        try{
            $paymentData = $bankingService->getPaymentData($debitorUser->getCyclosID(),$creditorUser->getCyclosID(),NULL);
            foreach($paymentData->paymentTypes as $paymentType){
                if(preg_match('#paiement_par_sms#', $paymentType->internalName)){
                    $smsTransferType = $paymentType;
                }
            }

            //preview allows to make sure payment would be executed according to provided data
            $res = $this->bankingManager->makeSinglePreview($paymentData,$operationAmount,$reason,$smsTransferType,$operation->getExecutionDate());
        }catch(\Exception $e){
            if($e instanceof Cyclos\ServiceException){
                /*this is the only criteria that should be checked whether payment data have already been checked or not
                1) imagine an user requests a payment who needs to be validated by key according to our application logic : there is no
                   trace of this payment in database yet
                2) then, instead of validating the payment in 1), he requests another payment which does not require validation key.
                   The payment is proceeded and balance has changed.
                3) he finally inputs card key code to validate first payment. The amount is greater than the new balance, so exception
                   is thrown on Cyclos side
                 */
                if($e->errorCode == 'INSUFFICIENT_BALANCE'){ 
                    $account = $this->get('cairn_user_cyclos_account_info')->getDefaultAccount($debitorUser->getCyclosID());
                    $balance = $account->status->balance; 

                    $smsReceivedError = new Sms($debitorPhoneNumber, $parsedSms->content, Sms::STATE_INVALID, NULL);

                    $smsInvalid=$messageNotificator->sendSMS($debitorPhoneNumber,'Solde insuffisant : Votre solde actuel est de '.$balance,$smsReceivedError);


                    $em->persist($smsReceivedError);
                    $this->persistSMS($smsInvalid);
                    $em->flush();

                    return;
                }
            }

            $smsReceivedError = new Sms($debitorPhoneNumber, $parsedSms->content, Sms::STATE_ERROR, NULL);
            $smsSentError = $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : Veuillez contacter l\'Association',$smsReceivedError);

            $em->persist($smsReceivedError);
            $this->persistSMS($smsSentError);
            $em->flush();

            throw $e;
        }

        $operation->setFromAccountNumber($res->fromAccount->number);
        $operation->setToAccountNumber($res->toAccount->number);
        $operation->setAmount($res->totalAmount->amount);

        if($toAnalyze){

            //the state of SMS paymentRequest can be WAITING_KEY, SUSPICIOUS or PROCESSED

            $needsValidation = $securityService->paymentNeedsValidation($operation,$debitorPhone);
            if($needsValidation){
                $this->setUpSmsValidation($em, $debitorUser, $parsedSms->content, $debitorPhone);
                $em->flush();
                return true;
            }else{//payment is checked, but is neither suspicious or to be validated
                $smsPaymentRequest = new Sms($debitorPhoneNumber, $parsedSms->content,Sms::STATE_PROCESSED,NULL );
                $em->persist($smsPaymentRequest); 
            }
        }

        $paymentVO = $this->bankingManager->makePayment($res->payment);

        $operation->setPaymentID($paymentVO->id);

        //notify debitor that payment has been executed successfully
        $debitorBalance = $this->get('cairn_user_cyclos_account_info')->getDefaultAccount($debitorUser->getCyclosID())->status->balance;

        $messageDebitor = 'Votre paiement de '.$operation->getAmount().' à '.$creditorUser->getName().' a été accepté.'."\n".'Votre nouveau solde: '.$debitorBalance ;
        $smsSuccessDebitor = $messageNotificator->sendSMS($debitorPhoneNumber,$messageDebitor);


        $messageCreditor = 'Vous avez reçu un paiement de '.$operation->getAmount().' de la part de '.$debitorUser->getName() ;

        $notifPermission = $creditorUser->getSmsData()->getNotificationPermission();
        if($notifPermission->isSmsEnabled()){
            $smsSuccessCreditor = $messageNotificator->sendSMS($creditorPhoneNumber,$messageCreditor);
            $this->persistSMS($smsSuccessCreditor);
        }
        if($notifPermission->isWebPushEnabled()){
            $messageNotificator->sendNotification($creditorUser,'Paiement SMS [e]-Cairn',$messageCreditor);
        }

        if($notifPermission->isEmailEnabled()){
            $subject = 'Paiement SMS [e]-Cairn';
            $from = $messageNotificator->getNoReplyEmail();
            $to = $creditorUser->getEmail();
            $body = $this->renderView('CairnUserBundle:Emails:payment_notification.html.twig',array('operation'=>$operation,'phone'=>$creditorPhone));
        
            $messageNotificator->notifyByEmail($subject,$from,$to,$body);
        }

        $em->persist($operation);
        $this->persistSMS($smsSuccessDebitor);

        return true;

    }


    /**
     * The state of the received SMS is set to "WAITING_kEY" and a validation SMS is sent back with security card cell position
     *
     * An SMS is sent back to the user with a security card cell position to input in the next SMS (code with 4 figures ). 
     * If a previous SMS was already waiting for a validation code, it is set as canceled and, for the new SMS, we ask for the exact same 
     * code than the previous one. 
     *
     * @param EntityManager $em
     * @param User $user : adherent who sent the sms
     * @param string $content : sms content
     */
    public function setUpSmsValidation($em, $user, $content, $userPhone)
    {
        $messageNotificator = $this->get('cairn_user.message_notificator'); 
        $phoneNumber = $userPhone->getPhoneNumber();
        $sms = $em->getRepository('CairnUserBundle:Sms')->findOneBy(array('phoneNumber'=>$phoneNumber,
                                                                          'state'=>Sms::STATE_WAITING_KEY));

        $card = $user->getCard();

        //if sms found, we ask for the exact same code position
        if($sms){
            $str_index = $sms->getCardPosition();
            $str_pos = $card->generateCardPositions($str_index)['cell'];
            $sms->setState(Sms::STATE_CANCELED);
        }else{
            $positions = $card->generateCardPositions();
            $str_pos = $positions['cell'] ;
            $str_index = $positions['index'] ;
        }

        $smsReceived = new Sms($phoneNumber,$content,Sms::STATE_WAITING_KEY,$str_index);
        $em->persist($smsReceived);

        //if there is a canceled sms, we must check that either the new received SMS is not a spam, but the canceled SMS as well
        $smsPotentialSpam = ($sms) ? $sms : $smsReceived;

        $smsSent = $messageNotificator->sendSMS($phoneNumber,'Veuillez saisir votre code de sécurité en position '.$str_pos.' utilisable jusqu\'à ' . date('H:i',strtotime($smsReceived->getSentAt()->format('H:i')." +5 mins")), $smsPotentialSpam);

        $this->persistSMS($smsSent);
    }

}
