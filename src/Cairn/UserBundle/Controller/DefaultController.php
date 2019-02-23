<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Address;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\BankingManager;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\TextType;                   
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                   
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\RegistrationType;
use Cairn\UserBundle\Form\OperationType;
use Cairn\UserBundle\Form\SimpleOperationType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;


use Cyclos;

/**
 * This class contains actions that need no role at all. Mostly, those can be done before login as anonymous user. 
 */
class DefaultController extends Controller
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
        $this->smsAction('PAYER 13 nico_faus_prod');
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
        
        //3) Prepare error messages
        $res = new \stdClass();
        
        $error = NULL;

        if(! ($matches_payment || $matches_balance || $matches_code)){
            if(! preg_match('#^(PAYER|SOLDE|\d{4})#',$content)){
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
                }else{
                    $error = 'Saisissez un code à 4 chiffres '."\n";
                }
            }
        }else{ //one regex match
            if($matches_payment){
                $res->isPaymentRequest = true;
                $res->isPaymentValidation = false;
                $res->amount = str_replace(',','.',$matches_payment[2]);
                $res->creditorLogin = $matches_payment[4];
            }elseif($matches_balance){
                $res->isPaymentRequest = false;
                $res->isPaymentValidation = false;
            }else{//card code sent
                $res->isPaymentRequest = false;
                $res->isPaymentValidation = true;
                $res->cardKey = $matches[1];
            }
        }

        $res->error = $error;
        return $res;
//        if(!$matches[1]){
//            $errors['action'] = 'ACTION NON RECONNUE : OPTIONS "PAYER" OU "SOLDE"';
//        }else{
//            if($matches[1] == 'PAYER'){
//                if(!$matches[2]){
//                    $errors['amount'] = 'MONTANT INVALIDE : EXEMPLES "15.5" OU "15,52"';
//                }
//                if(!$matches[3]){
//                    $errors['creditor'] = 'PSEUDO CREDITEUR INVALIDE';
//                }
//
//            }   
//        }

    }

    public function smsAction($content)
    {
        $em = $this->getDoctrine()->getManager();
        $messageNotificator = $this->get('cairn_user.message_notificator');
        $securityService = $this->get('cairn_user.security');
        $operation = new Operation();
        $operation->setType(Operation::TYPE_SMS_PAYMENT);

        //TODO here : get data from SMS and parse content
        $debitorPhoneNumber = '0611223344';

        $debitorUsers = $em->getRepository('CairnUserBundle:User')->findBy(array('phoneNumber'=>$debitorPhoneNumber));
        $isUniquePhoneNumber = (count($debitorUsers) == 1);
        $isProAndPersonPhoneNumber = (count($debitorUsers) == 2);


        //1) we ensure that SMS sender exists
        //if there are two members with same phone number, ROLE_PERSON is used by default
        if($isProAndPersonPhoneNumber){
            $debitorUser = ($debitorUsers[0]->hasRole('ROLE_PERSON')) ? $debitorUsers[0] : $debitorUsers[1];
        }elseif($isUniquePhoneNumber){
            $debitorUser = $debitorUsers[0];
        }else{
            //TODO : est-ce qu'on prend même la peine d'envoyer un SMS
            $messageNotificator->sendSMS($debitorPhoneNumber,'COMPTE E-CAIRN INTROUVABLE');
            return;
        }
       
        //2)Then, we ensure that sms actions are enabled for this user
        if(! $debitorUser->isSmsEnabled()){
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
                echo 'AAAAAAAA';
                $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE : Veuillez nous contacter');
            }else{
                $messageNotificator->sendSMS($debitorPhoneNumber,'CONNEXION IMPOSSIBLE : Veuillez nous contacter');
            }
            return;
        }

        //4) Parse SMS content
        $parsedSms = $this->parseSms($content);
        if( $parsedSms->errors){
            $reason = 'FORMAT DU SMS INVALIDE';

            //if($smsIsPayment){
            //    $example = 'EXEMPLE : PAYER 5.5 MAGASIN';
            //}else{
            //    $example = 'EXEMPLE : SOLDE';
            //}
            $messageNotificator->sendSMS($debitorPhoneNumber,$reason);
            return;
        }

        
        if(! $parsedSms->isPaymentRequest){
            if($parsedSms->isPaymentValidation){
                ;
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

            $res = $this->bankingManager->makeSinglePreview($paymentData,$operationAmount,$reason,$smsTransferType,$operation->getExecutionDate());
            $operation->setFromAccountNumber($res->fromAccount->number);
            $operation->setToAccountNumber($res->toAccount->number);

//            if($securityService->paymentNeedsValidation($debitorUser, $operation)){
//                 
//                return;
//            }

            $paymentVO = $this->bankingManager->makePayment($res->payment);
            
        }catch(\Exception $e){
            if($e instanceof Cyclos\ServiceException){
                if($e->errorCode == 'INSUFFICIENT_BALANCE'){ //should not happen because check in OperationValidator
                    $balance = $this->get('cairn_user_cyclos_account_info')->getAccountByID($res->from->id)->status->balance; 
                    $messageNotificator->sendSMS($debitorPhoneNumber,'SOLDE INSUFFISANT : '.$balance);
                }else{
                    $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE CYCLOS: Veuillez nous contacter');
                }
                return;
            }else{
                $messageNotificator->sendSMS($debitorPhoneNumber,'ERREUR TECHNIQUE 500: Veuillez nous contacter');
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
                    $operation->setType(Operation::TYPE_CONVERSION);
                    break;
                case "deposit":
                    $operation->setType(Operation::TYPE_DEPOSIT);
                    break;
                case "withdrawal":
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
     * First step of user's registration
     *
     * The type of user is set in session here because we will need it in our RegistrationEventListener.
     */
    public function registrationAction(Request $request)
    {
        $session = $request->getSession();
        $checker = $this->get('security.authorization_checker');

        $user = $this->getUser();
        if($user){
            if($user->hasRole('ROLE_ADHERENT')){
                throw new AccessDeniedException('Vous avez déjà un espace membre.');
            }
        }

        $type = $request->query->get('type'); 

        if( ($type == 'person') || ($type=='pro') || ($type == 'localGroup') || ($type=='superAdmin')){
            if(($type == 'localGroup' || $type=='superAdmin') && (!$checker->isGranted('ROLE_SUPER_ADMIN')) ){
                throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires.');
            }
            return $this->redirectToRoute('fos_user_registration_register',array('type'=>$type));
        }else{
            return $this->render('CairnUserBundle:Registration:index.html.twig');
        }

    }    


}
