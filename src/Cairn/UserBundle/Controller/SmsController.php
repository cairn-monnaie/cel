<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Sms;

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
//        $this->smsAction($request->query->get('phone'),$request->query->get('content'));
//        $this->smsAction('0612345678','LOGIN');
        $this->smsAction('0612345678','PAYER 40 maltobar');
//        $this->smsAction('0612345678','SOLDE');
//        $this->smsAction('0612345678','1111');
//        $this->smsAction('0612345678','2222');

        return new Response('ok');
    }

    public function parseSms($content)
    {
        //1) content treatment : escape special chars and remove all whitespaces from content
        $content = preg_replace('/\s+/', '',htmlspecialchars($content));

        //2)Regex analysis
        //TODO : make it more flexible
        //PAYER autoriser plus de décimales au montant et tronquer après
        preg_match('#^(PAYER)(\d+([,\.]\d+)?)([a-zA-Z]{1}\w+)$#',$content,$matches_payment);
        preg_match('#^SOLDE$#',$content,$matches_balance);
        preg_match('#^\d{4}$#',$content, $matches_code);
         preg_match('#^LOGIN$#',$content, $matches_login);
       
        //3) Prepare error messages
        $res = new \stdClass();
        
        $error = NULL;

        if(! ($matches_payment || $matches_balance || $matches_code || $matches_login)){
            if(! preg_match('#^(PAYER|SOLDE|LOGIN|\d{4})#',$content)){
                $error = 'Action invalide'."\n".'Envoyer PAYER, SOLDE, LOGIN ou un code à 4 chiffres en cas de validation de paiement';
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
            if($matches_payment){
                $res->isPaymentRequest = true;
                $res->isPaymentValidation = false;
                $res->isSmsIdentifier = false;
                $res->amount = str_replace(',','.',$matches_payment[2]);
                $res->creditorLogin = $matches_payment[4];
            }elseif($matches_balance){
                $res->isPaymentRequest = false;
                $res->isPaymentValidation = false;
                $res->isSmsIdentifier = false;
            }elseif($matches_login){//SMS identifier requested
                $res->isPaymentRequest = false;
                $res->isPaymentValidation = false;
                $res->isSmsIdentifier = true;
            }else{
                $res->isPaymentRequest = false;
                $res->isPaymentValidation = true;
                $res->isSmsIdentifier = false;
                $res->cardKey = $matches[1];
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
        $operation = new Operation();
        $operation->setType(Operation::TYPE_SMS_PAYMENT);

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
             $messageNotificator->sendSMS($debitorPhoneNumber,'SMS NON AUTORISE: rendez-vous sur la plateforme web pour vous y donner accès !');
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

        
        if(! $parsedSms->isPaymentRequest){
            if($parsedSms->isPaymentValidation){
                ;
            }elseif($parsedSms->isSmsIdentifier){
                $messageNotificator->sendSMS($debitorPhoneNumber,'IDENTIFIANT SMS E-CAIRN : '.$debitorUser->getUsername());
                return;
            }else{// account balance requested
                $account = $this->get('cairn_user_cyclos_account_info')->getDefaultAccount($debitorUser->getCyclosID()); 
                $messageNotificator->sendSMS($debitorPhoneNumber,'SOLDE COMPTE E-CAIRN : '.$account->status->balance);
                return;
            }
        }

        //4) If payment, find Creditor user
        $creditorUser = $em->getRepository('CairnUserBundle:User')->findOneByUsername($parsedSms->creditorLogin);
        if(! $creditorUser){
            $messageNotificator->sendSMS($debitorPhoneNumber,'COMPTE E-CAIRN CREDITEUR INTROUVABLE');
            return;
        }

        $creditorPhoneNumber = $creditorUser->getPhoneNumber();

        $operationAmount = floatval($parsedSms->amount);

        $operation->setAmount($operationAmount);
        $operation->setDebitor($debitorUser);
        $operation->setCreditor($creditorUser);


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

        $reason = 'Virement '.$this->getParameter('cyclos_currency_cairn').' par SMS';
        $operation->setReason($reason);

        $bankingService = $this->get('cairn_user_cyclos_banking_info');
        //make payment on Cyclos-side
        try{
            $paymentData = $bankingService->getPaymentData($debitorUserVO,$creditorUser->getCyclosID(),NULL);
            foreach($paymentData->paymentTypes as $paymentType){
                if(preg_match('#paiement_par_sms#', $paymentType->internalName)){
                    $smsTransferType = $paymentType;
                }
            }

            //allows to make sure payment would be executed according to provided data
            $res = $this->bankingManager->makeSinglePreview($paymentData,$operationAmount,$reason,$smsTransferType,$operation->getExecutionDate());
            $operation->setFromAccountNumber($res->fromAccount->number);
            $operation->setToAccountNumber($res->toAccount->number);
            $operation->setAmount($res->totalAmount->amount);

            if($securityService->paymentNeedsValidation($operation)){
                $sms = $em->getRepository('CairnUserBundle:Sms')->findOneBy(array('phoneNumber'=>$debitorPhoneNumber,'state'=>Sms::STATE_WAITING_KEY));
                $waiting_op = "\n";
                if($sms){
                    $waiting_op = 'OPERATION PRECEDENTE ANNULEE : '.$sms->getContent()."\n";
                    $em->remove($sms);
                }

                $card = $debitorUser->getCard();
                $positions = $securityService->generateCardPositions($card);
                $str_pos = $positions['cell'] ;
                $str_index = $positions['index'] ;

                $code = $card->getKey($str_index) ;
                $sms = new Sms($debitorPhoneNumber,$content,Sms::STATE_WAITING_KEY,$code);
                $messageNotificator->sendSMS($debitorPhoneNumber,$waiting_op.'CONFIRMER CODE CARTE SECURITE '.$str_pos);

                $em->persist($sms);
                $em->flush();
                return;
            }

            $paymentVO = $this->bankingManager->makePayment($res->payment);
            
        }catch(\Exception $e){
            if($e instanceof Cyclos\ServiceException){
                //should never  happen because check in OperationValidator
                if($e->errorCode == 'INSUFFICIENT_BALANCE'){ 
                    $balance = $this->get('cairn_user_cyclos_account_info')->getAccountByID($res->from->id)->status->balance; 
                    $messageNotificator->sendSMS($debitorPhoneNumber,'SOLDE INSUFFISANT : actuellement '.$balance);
                }else{
                    $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE. Veuillez nous contacter');
                }
                return;
            }else{
                $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : Veuillez nous contacter');
                throw $e;
            }

            return;
        }

        //synchronize payment ID
        $operation->setPaymentID($paymentVO->id);

        $em->persist($operation);
        $em->flush();

        //notify creditor that payment has been executed successfully
        $messageNotificator->sendSMS($creditorPhoneNumber,'données du paiement');
        return;
    }


}
