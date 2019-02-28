<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Sms;

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


/**
 * This class contains actions that need no role at all. Mostly, those can be done before login as anonymous user. 
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

    public function smsReceptionAction(Request $request)
    {
        $this->smsAction($request->query->get('phone'),$request->query->get('content'));
//        $this->smsAction('0612345678','LOGIN');
//        $this->smsAction('0612345678','PAYER 40 maltobar');
//        $this->smsAction('0612345678','SOLDE');
//        $this->smsAction('0612345678','1111');
//        $this->smsAction('0612345678','2222');

        return new Response('ok');
    }

    public function parseSms($content)
    {
        //1) content treatment : escape special chars and remove all whitespaces from content
        $content = preg_replace('/\s+/', '',htmlspecialchars($content));

        //2) replace all characters to uppercase chars
        $content = strtoupper($content);

        //3)Regex analysis
        //TODO : make it more flexible
        //PAYER autoriser plus de décimales au montant et tronquer après
        preg_match('#^(PAYER)(\d+([,\.]\d+)?)([A-Z]{1}\w+)$#',$content,$matches_payment);
        preg_match('#^SOLDE$#',$content,$matches_balance);
        preg_match('#^\d{4}$#',$content, $matches_code);
        preg_match('#^LOGIN$#',$content, $matches_login);
       
        //3) Prepare error messages
        $res = new \stdClass();
        
        $error = NULL;

        if(! ($matches_payment || $matches_balance || $matches_code || $matches_login)){
            if(! preg_match('#^(PAYER|SOLDE|LOGIN|\d{4})#',$content)){
                $error = 'Action invalide'."\n".'Envoyer PAYER, SOLDE ou un code à 4 chiffres en cas de validation de paiement';
            }else{
                if(preg_match('#^PAYER#',$content)){ //is payment request
                    if(! preg_match('#^PAYER\d+([,\.]\d+)?$#',$content)){//invalid amount format
                        $error = 'Format du montant invalide : '."\n";
                    }else{
                        $error = 'IDENTIFIANT INCONNU'."\n";
                    }
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

    public function smsAction($debitorPhoneNumber,$content)
    {
        $em = $this->getDoctrine()->getManager();
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
            //TODO : est-ce qu'on prend même la peine d'envoyer un SMS ?
            $messageNotificator->sendSMS($debitorPhoneNumber,'COMPTE E-CAIRN INTROUVABLE');
            return;
        }
       
        //2.1)Then, we ensure that user is active, and then sms actions are enabled for this user
        if(! $debitorUser->isEnabled()){
             $messageNotificator->sendSMS($debitorPhoneNumber,'SMS NON AUTORISE: Compte inactif');
             return;
        }

        //2.2)Then, we ensure that sms actions are enabled for this user
        if(! $debitorUser->getSmsData()->isSmsEnabled()){
             $messageNotificator->sendSMS($debitorPhoneNumber,'OPERATION SMS NON AUTORISÉE!');
             return;
        }

        //3) Connect the user to Cyclos via access client
        try{
            $networkInfo = $this->get('cairn_user_cyclos_network_info');
            $networkName = $this->getParameter('cyclos_currency_cairn');
            $accessClient = $securityService->getSmsClient($debitorUser);

            if(!$accessClient){
                $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : Veuillez nous contacter');
                return; 
            }
            $networkInfo->switchToNetwork($networkName,'access_client', $accessClient);

            $debitorUserVO = $this->get('cairn_user_cyclos_user_info')->getCurrentUser();

        }catch(\Exception $e){

            if($e->errorCode == 'INVALID_ACCESS_CLIENT'){
                $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : Veuillez nous contacter');
            }else{
                $messageNotificator->sendSMS($debitorPhoneNumber,'CONNEXION IMPOSSIBLE : Veuillez nous contacter');
            }
            return;
        }

        //4) Parse SMS content
        $parsedSms = $this->parseSms($content);
        if( $parsedSms->error){
            $reason = 'SMS INVALIDE'."\n".$parsedSms->error;
            $messageNotificator->sendSMS($debitorPhoneNumber,$reason);
            return;
        }

        
        if(! ($parsedSms->isPaymentRequest || $parsedSms->isOperationValidation)){//account balance or SMS Identifier
            if($parsedSms->isSmsIdentifier && !$debitorUser->hasRole('ROLE_PRO')){
               return;
            }
            $sms = $this->setUpSmsValidation($em, $debitorUser, $content);
            $em->persist($sms);
            $em->flush();
            return;
        }elseif($parsedSms->isOperationValidation){
            $sms = $em->getRepository('CairnUserBundle:Sms')->findOneBy(array('phoneNumber'=>$debitorPhoneNumber,
                                                                          'state'=>Sms::STATE_WAITING_KEY));

            if(!$sms){
                $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR : AUCUNE OPERATION A VALIDER');
                return;
            }

            if($sms->getRequestedAt()->diff(new \Datetime())->i > 5){
                $messageNotificator->sendSMS($debitorPhoneNumber,'DELAI DE VALIDATION EXPIRE');
                $sms->setState(Sms::STATE_EXPIRED);
                $em->flush();

                return;
            }
            $event = new InputCardKeyEvent($debitorUser,$parsedSms->cardKey,$sms->getCardPosition(), NULL);
            $this->get('event_dispatcher')->dispatch(SecurityEvents::INPUT_CARD_KEY,$event);

            $card = $debitorUser->getCard();
            $nbTries = $debitorUser->getCardKeyTries();
            $remainingTries = 3 - $nbTries;

            if( ($nbTries  > 0) && ($remainingTries > 0)){
                $messageNotificator->sendSMS($debitorPhoneNumber,'ECHEC CODE CARTE SECURITE : '.$remainingTries.' essais restant(s)');
                return;
            }elseif($remainingTries == 0){
                $messageNotificator->sendSMS($debitorPhoneNumber,'ECHEC CODE CARTE SECURITE : Le compte a été bloqué. Veuillez contacter l\'Association');
                return;
            }

            //get initial sms request
            $parsedInitialSms = $this->parseSms($sms->getContent());
            if($parsedInitialSms->isSmsIdentifier){
                if($debitorUser->hasRole('ROLE_PRO')){
                    $messageNotificator->sendSMS($debitorPhoneNumber,'IDENTIFIANT SMS E-CAIRN : '.$debitorUser->getUsername());
                }
                return;
            }elseif($parsedInitialSms->isPaymentRequest){
                //at this stage, as it is a validation action, it means that payment data has already been checked and validated
                $this->executePayment($debitorUser, $parsedInitialSms, false);    
            }else{//initial sms was about account balance
                $account = $this->get('cairn_user_cyclos_account_info')->getDefaultAccount($debitorUser->getCyclosID()); 
                $messageNotificator->sendSMS($debitorPhoneNumber,'SOLDE COMPTE E-CAIRN : '.$account->status->balance);
            }
          
            //once pending request has been executed, the corresponding sms can be removed
            $em->remove($sms);
            $em->flush();
            return;
        }

        //last option : request is a payment to be validated
        return $this->executePayment($debitorUser,$parsedSms, true);    

    }


    //TODO : changer l'état du SMS
    public function executePayment($debitorUser, $parsedSms, $toValidate)
    {
        $em = $this->getDoctrine()->getManager();
        $messageNotificator = $this->get('cairn_user.message_notificator');
        $securityService = $this->get('cairn_user.security');

        $debitorPhoneNumber = $debitorUser->getPhoneNumber();

        $creditorSmsData = $em->getRepository('CairnUserBundle:SmsData')->findOneByIdentifier(strtoupper($parsedSms->creditorIdentifier));
        if(! $creditorSmsData){//should not occur if validation has already been done
            $messageNotificator->sendSMS($debitorPhoneNumber,'IDENTIFIANT SMS INTROUVABLE');
            return;
        }

        $creditorUser = $creditorSmsData->getUser();

        if(! $creditorUser->hasRole('ROLE_PRO')){
            $messageNotificator->sendSMS($debitorPhoneNumber,'CREDITEUR NON PROFESSIONNEL');
            return;
        }
        $creditorPhoneNumber = $creditorUser->getPhoneNumber();

        $operation = new Operation();
        $operation->setType(Operation::TYPE_SMS_PAYMENT);

        $operationAmount = floatval($parsedSms->amount);

        $operation->setAmount($operationAmount);
        $operation->setDebitor($debitorUser);
        $operation->setCreditor($creditorUser);


        if($toValidate){
            $validator = $this->get('validator');
            $listErrors = $validator->validate($operation);

            if( count($listErrors) > 0 ){
                $content = '';
                foreach($listErrors as $error){
                    $content = $content.$error->getMessage()."\n";
                }
                $messageNotificator->sendSMS($debitorPhoneNumber, $content);
                return;
            }
        }

        $reason = 'Paiement '.$this->getParameter('cyclos_currency_cairn').' par SMS';
        $description = 'Paiement effectué le '.$operation->getExecutionDate()->format('d-m-Y').' à destination de '.$creditorUser->getName();
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
                    $messageNotificator->sendSMS($debitorPhoneNumber,'SOLDE INSUFFISANT : actuellement '.$balance);
                    return;
                }
            }

            $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : Veuillez nous contacter');
            return;
        }

        $operation->setFromAccountNumber($res->fromAccount->number);
        $operation->setToAccountNumber($res->toAccount->number);
        $operation->setAmount($res->totalAmount->amount);

        if($toValidate){
            $needsValidation = $securityService->paymentNeedsValidation($operation);
            $isSuspicious = $securityService->paymentIsSuspicious($operation);
            if($isSuspicious) {
                $currency = $this->getParameter('cyclos_currency_cairn');

                $subject = 'Paiement SMS suspicieux';
                $body = $this->get('templating')->render('CairnUserBundle:Emails:suspicious_sms.html.twig',array(
                    'operation'=>$operation,'toAdmin'=>false));

                $messageNotificator->sendSMS($debitorPhoneNumber,'PAIEMENT SMS BLOQUE : operation jugée suspicieuse');

                $body = $this->get('templating')->render('CairnUserBundle:Emails:suspicious_sms.html.twig',array(
                    'operation'=>$operation,'toAdmin'=>true));

                $messageNotificator->notifiyByEmail($subject, $this->getParameter('cairn_email_noreply'),$this->getParameter('cairn_email_management'), $body);

                $creditorUser->getSmsData()->setSmsEnabled(false);
                $em->flush();
                return;
            }elseif($needsValidation){
                $sms = $this->setUpSmsValidation($em, $debitorUser, $parsedSms->content);
                $em->persist($sms);
                $em->flush();
                return;
            }
        }

        $paymentVO = $this->bankingManager->makePayment($res->payment);

        $operation->setPaymentID($paymentVO->id);
        $em->persist($operation);
        $em->flush();

        //notify debitor that payment has been executed successfully
        $messageBase = 'SUCCES DU PAIEMENT !'."\n".'MONTANT : '.$operation->getAmount();
        $debitorBalance = $this->get('cairn_user_cyclos_account_info')->getDefaultAccount($debitorUser->getCyclosID())->status->balance;
        $messageNotificator->sendSMS($debitorPhoneNumber,$messageBase.' CREDITEUR :'.$creditorUser->getName()."\n".'SOLDE ACTUEL : '.$debitorBalance);

        //to notify creditor with creditor data, we need to connect to Cyclos with his access client
        try{
            $networkInfo = $this->get('cairn_user_cyclos_network_info');
            $networkName = $this->getParameter('cyclos_currency_cairn');
            $accessClient = $securityService->getSmsClient($creditorUser);

            $networkInfo->switchToNetwork($networkName,'access_client', $accessClient);
            $creditorUserVO = $this->get('cairn_user_cyclos_user_info')->getCurrentUser();

            $creditorAccount = $this->get('cairn_user_cyclos_account_info')->getDefaultAccount($creditorUser->getCyclosID());
            $creditorBalance = $creditorAccount->status->balance;
            $messageNotificator->sendSMS($creditorPhoneNumber,'SUCCES DU PAIEMENT !'."\n".'SOLDE ACTUEL : '.$creditorBalance);
    
            return;

        }catch(\Exception $e){//if for some reason connexion to Cyclos fails, we still inform creditor that payment has been executed
            $messageNotificator->sendSMS($creditorPhoneNumber,$messageBase.' DEBITEUR :'.$debitorUser->getName()."\n".'SOLDE ACTUEL : indisponible. Veuillez contacter l\'association');
            return;
        }

    }


    public function setUpSmsValidation($em, $user, $content)
    {
        $phoneNumber = $user->getPhoneNumber();
        $sms = $em->getRepository('CairnUserBundle:Sms')->findOneBy(array('phoneNumber'=>$phoneNumber,
                                                                          'state'=>Sms::STATE_WAITING_KEY));

        $card = $user->getCard();

        $waiting_op = "\n";
        //if sms found, we ask for the exact same code position
        if($sms){
            $str_index = $sms->getCardPosition();
            $str_pos = $card->generateCardPositions($str_index)['cell'];
            $waiting_op = 'OPERATION PRECEDENTE ANNULEE : '.$sms->getContent()."\n";
            $em->remove($sms);
        }else{
            $positions = $card->generateCardPositions();
            $str_pos = $positions['cell'] ;
            $str_index = $positions['index'] ;
        }


        $sms = new Sms($phoneNumber,$content,Sms::STATE_WAITING_KEY,$str_index);
        $this->get('cairn_user.message_notificator')->sendSMS($phoneNumber,$waiting_op.'CONFIRMER CODE CARTE SECURITE '.$str_pos);

        return $sms;

    }

}
