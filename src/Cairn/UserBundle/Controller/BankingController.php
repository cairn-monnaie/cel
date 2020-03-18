<?php
// src/Cairn/UserBundle/Controller/BankingController.php

namespace Cairn\UserBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\BankingManager;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\AccountScore;

//manage Events 
use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

//manage Forms
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\OperationType;
use Cairn\UserBundle\Form\ReconversionType;
use Cairn\UserBundle\Form\CardType;
use Cairn\UserBundle\Form\SimpleOperationType;
use Cairn\UserBundle\Form\RecurringOperationType;
use Cairn\UserBundle\Form\AccountScoreType;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\NumberType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\LogicException;

/**
 * This class contains actions related to account operations 
 *
 * @Security("is_granted('ROLE_ADHERENT')")
 */
class BankingController extends Controller
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


    /**
     * A pro can reconvert mlc 
     *
     * @Security("has_role('ROLE_PRO')")
     */
    public function reconversionAction(Request $request)
    {
        $session = $request->getSession();
        $currentUser = $this->getUser();

        if(! $currentUser->hasRole('ROLE_PRO')){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }   

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $accountService = $this->get('cairn_user_cyclos_account_info');


        $debitorVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $selfAccounts = $accountService->getAccountsSummary($debitorVO->id);

        $operation = new Operation();
        $operation->setType(Operation::TYPE_RECONVERSION);
        //$operation->setToAccountNumber($to); //todo: hack, make it cleaner

        $form = $this->createForm(ReconversionType::class, $operation);
        // create form
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $operation->setReason($this->editDescription('reconversion', $operation->getReason()));

                $fromAccount = $accountService->getAccountByNumber($operation->getFromAccount()->number);


                $fromUserVO = $this->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($fromAccount->number);

                $bankingService = $this->get('cairn_user_cyclos_banking_info'); 
                $paymentData = $bankingService->getPaymentData($currentUser->getCyclosID(),'SYSTEM',NULL);

                //filter the potential transfer types according to the debitor account type
                $transferTypes = $paymentData->paymentTypes;
                foreach($transferTypes as $transferType){
                    if( strpos($transferType->internalName, 'reconversion_numerique') !== false){
                        $reconvertTransferType = $transferType;
                    }
                }

                $amount = $operation->getAmount();

                //WARNING :  on Cyclos side, there is only one field for description, whereas on Symfony side there is
                //both reason & description. For this reason, we transmit the reason as cyclos description
                $cyclosDescription = $operation->getReason();
                $dataTime = $operation->getExecutionDate();

                $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$cyclosDescription,$reconvertTransferType,$dataTime);
                $paymentVO = $this->bankingManager->makePayment($res->payment);


                $operation->setDebitorName($this->get('cairn_user_cyclos_user_info')->getOwnerName($paymentVO->fromOwner));
                $operation->setCreditorName($this->get('cairn_user_cyclos_user_info')->getOwnerName($paymentVO->toOwner));

                $operation->setPaymentID($paymentVO->transferId);
                $operation->setFromAccountNumber($res->fromAccount->number);
                $operation->setToAccountNumber($res->toAccount->number);

                $operation->setDebitor($currentUser);
                $em->persist($operation);
                $em->flush();

                //send emails to eCairn email and to user
                $messageNotificator = $this->get('cairn_user.message_notificator');

                //first email to user
                $body = $this->get('templating')->render('CairnUserBundle:Emails:reconversion.html.twig',array('toAdmin'=>false,'user'=>$currentUser,'operation'=>$operation));
                $messageNotificator->notifyByEmail('Reconversion [e]-Cairn',$messageNotificator->getNoReplyEmail(),$currentUser->getEmail(),$body);

                //second email to admin
                $body = $this->get('templating')->render('CairnUserBundle:Emails:reconversion.html.twig',array('toAdmin'=>true,'user'=>$currentUser,'operation'=>$operation));
                $messageNotificator->notifyByEmail('Reconversion [e]-Cairn',$messageNotificator->getNoReplyEmail(),$this->getParameter('cairn_email_management'),$body);

                $session->getFlashBag()->add('success','La reconversion a été effectuée avec succès.');
                $session->getFlashBag()->add('info','Votre remboursement sera effectué dans les plus brefs délais');
                return $this->redirectToRoute('cairn_user_banking_transfer_view', array('paymentID' => $operation->getPaymentID() ));            

            }
        }

        return $this->render('CairnUserBundle:Banking:reconversion.html.twig', array('form'=>$form->createView(),'operation'=>$operation));

    }

    /*
     * Shows an overview of all @param accounts
     *
     * @param User $user User entity the accounts belong to
     * @throws Cyclos\ServiceException
     * @Method("GET")
     */  
    public function accountsOverviewAction(Request $request, $_format)
    {
        $user = $this->getUser();

        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id);

        if($_format == 'json'){
            $response = new Response(json_encode($accounts) );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return $this->render('CairnUserBundle:Banking:accounts_overview.html.twig', array('user'=>$user,'accounts'=> $accounts));
    }

    /*
     * Shows all operations involving account with ID @param
     *
     * All users granted ROLE_ADMIN have the same accounts : the system accounts. Therefore, any admin trying to access their
     * accounts will see the same operations. If CurrentUser is not the account owner, it must be a referent of the owner
     * Info being displayed : balance/available balance / account type / Account identifier
     *
     * @param integer $accountID Cyclos ID of the involved account
     * @throws Cyclos\ServiceException
     */  
    public function accountOperationsAction(Request $request, $accountID, $_format)
    {
        $session = $request->getSession();
        $accountService = $this->get('cairn_user_cyclos_account_info');

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');
        $mandateRepo = $em->getRepository('CairnUserBundle:Mandate');

        $currentUser = $this->getUser();
        $currentUserVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $currentUserID = $currentUserVO->id;

        $account = $accountService->getAccountByID($accountID);

        //$user is account owner : if system account, any ADMIN works. O.w, get user from account owner cyclos id
        if($account->owner != 'SYSTEM'){
            $user = $this->get('cairn_user.bridge_symfony')->fromCyclosToSymfonyUser($account->owner->id);
        }else{
            $user = $this->get('cairn_user.bridge_symfony')->fromCyclosToSymfonyUser($currentUser->getCyclosID());
        }

        //to see the content, check that currentUser is owner or currentUser is referent
        if(! (($user === $currentUser) || $currentUser->hasRole('ROLE_SUPER_ADMIN'))){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }

        $accountTypeVO = $account->type; 

        //+1 day because the time is 00:00:00 so if currentUser input 2018-07-13 the filter will get payments until 2018-07-12 23:59:59
        $beginDefault = date_modify(new \Datetime(),'-2 months');
        $endDefault = date_modify(new \Datetime(),'+1 days');

        //get, once for all, the list of executed types
        $hasMandate = ( $mandateRepo->findOneByContractor($user) == NULL ) ? false : true;  
        $executedTypes = Operation::getExecutedTypes( ($hasMandate || $user->hasRole('ROLE_SUPER_ADMIN')), $user->hasRole('ROLE_PRO') || $user->hasRole('ROLE_SUPER_ADMIN'));

        //last operations

                $ob = $operationRepo->createQueryBuilder('o');
        $executedTransactions = $ob->where(
             $ob->expr()->orX(
                 $ob->expr()->andX(
                     'o.fromAccountNumber = :number',
                     $ob->expr()->in('o.type',$executedTypes)
                 ),
                 $ob->expr()->andX(
                     'o.toAccountNumber = :number',
                     $ob->expr()->in('o.type',$executedTypes)
                 )
             ))
            ->andWhere('o.paymentID is not NULL')
            ->andWhere('o.executionDate BETWEEN :begin AND :end')
            ->orderBy('o.executionDate','ASC')
            ->setParameter('number',$account->number)
            ->setParameter('begin',$beginDefault)
            ->setParameter('end',$endDefault)
            ->getQuery()->getResult();

        //amount of future transactions : next month total amount
        $query = $em->createQuery('SELECT SUM(o.amount) FROM CairnUserBundle:Operation o WHERE o.type = :type AND o.executionDate < :date AND o.fromAccountNumber = :number AND o.paymentID is not NULL');
        $query->setParameter('type', Operation::TYPE_TRANSACTION_SCHEDULED)
            ->setParameter('date',date_modify(new \Datetime(),'+1 months'))
            ->setParameter('number',$account->number);

        $res = $query->getSingleScalarResult();
        $totalAmount = ($res == NULL) ? 0 : $res ;

        $form = $this->createFormBuilder()
            ->add('orderBy',   ChoiceType::class, array(
                'label' => 'affiché par',
                'choices' => array('dates décroissantes'=>'DESC',
                'dates croissantes' => 'ASC'),
                'data'=>'DESC'
            ))
            ->add('types',    ChoiceType::class, array(
                'label' => 'type d\'opération',
                'required'=>false,
                'choices' => $executedTypes,
                'choice_label'=> function($choice){
                    return Operation::getTypeName($choice);
                },
                'multiple'=>true,
                'expanded'=>false
            ))
            ->add('begin',     DateType::class, array(
                'label' => 'depuis',
                'widget' => 'single_text',
                'data' => $beginDefault,
                'required'=>false,'attr'=>array('class'=>'datepicker_cairn')
            ))
            ->add('end',       DateType::class, array(
                'label' => 'jusqu\'à',
                'widget' => 'single_text',
                'data'=> $endDefault,
                'required'=>false,'attr'=>array('class'=>'datepicker_cairn')
            ))
            ->add('minAmount', NumberType::class,array(
                'label'=>'Montant minimum',
                'required'=>false
            ))
            ->add('maxAmount', NumberType::class,array(
                'label'=>'Montant maximum',
                'required'=>false
            ))
            ->add('creditor',  TextType::class,array(
                'label'=>'Compte créditeur',
                'required'=>false
            ))
            ->add('debitor',  TextType::class,array(
                'label'=>'Compte débiteur',
                'required'=>false
            ))
            ->add('keywords',  TextType::class,array(
                'label'=>'Mots-clés(motif, description)',
                'required'=>false
            ))
            ->add('save',      SubmitType::class, array('label' => 'Filtrer'))
            ->getForm();

        //amount of future transactions : next month total amount
        $query = $em->createQuery('SELECT SUM(o.amount) FROM CairnUserBundle:Operation o WHERE o.type = :type AND o.executionDate < :date AND o.fromAccountNumber = :number AND o.paymentID is not NULL');
        $query->setParameter('type', Operation::TYPE_TRANSACTION_SCHEDULED)
            ->setParameter('date',date_modify(new \Datetime(),'+1 months'))
            ->setParameter('number',$account->number);

        $res = $query->getSingleScalarResult();
        $totalAmount = ($res == NULL) ? 0 : $res ;

        if($request->isMethod('GET')){
            //last operations
            $ob = $operationRepo->createQueryBuilder('o');
            $operationRepo->whereInvolvedAccountNumber($ob, $account->number)
                ->whereTypes($ob,$executedTypes)
                ->whereExecutedBefore($ob,$endDefault)->whereExecutedAfter($ob,$beginDefault);
            $executedTransactions = $ob->andWhere('o.paymentID is not NULL')
                ->orderBy('o.executionDate','DESC')
                ->getQuery()->getResult();
        }


        if($request->isMethod('POST')){

            if($this->get('cairn_user.api')->isRemoteCall()){
                $data = json_decode(htmlspecialchars($request->getContent(),ENT_NOQUOTES), true);

                if(!$data){
                    return $this->get('cairn_user.api')->getErrorResponse(array("Invalid JSON"),Response::HTTP_BAD_REQUEST);
                }

                $operationTypes = $data['types'];
                if($operationTypes){
                    foreach($operationTypes as $key=>$value){
                         $data['types'][$key] = Operation::getTypeIndex($value);
                    }
                }

                $form->submit($data);
            }else{
                $form->handleRequest($request);
            }

            if($form->isSubmitted()){
                $dataForm = $form->getData();            

                $begin = $dataForm['begin'];
                $end = $dataForm['end'];
                $orderBy = $dataForm['orderBy'];

                $operationTypes = $dataForm['types'];
                if(! $operationTypes){
                    $operationTypes = $executedTypes;
                }
                $minAmount = $dataForm['minAmount'];
                $maxAmount = $dataForm['maxAmount'];
                $keywords = $dataForm['keywords'];

                $debitorSubstring = $dataForm['debitor'];
                $creditorSubstring = $dataForm['creditor'];


                //+1 day because the time is 00:00:00 so if currentUser input 2018-07-13 the filter will get payments until 2018-07-12 23:59:59
                $end = date_modify($end,'+1 days');

                $arrayTypes = $executedTypes;
                if($operationTypes){
                    $arrayTypes = $operationTypes;
                }

                $ob = $operationRepo->createQueryBuilder('o');
                $operationRepo->whereInvolvedAccountNumber($ob, $account->number)
                    ->whereTypes($ob,$operationTypes)
                    ->whereExecutedBefore($ob,$end)->whereExecutedAfter($ob,$begin)
                    ->whereKeywords($ob,$dataForm['keywords']);

                if($minAmount){
                    $ob->andWhere('o.amount >= :min')
                        ->setParameter('min',$minAmount);
                }
                if($maxAmount){
                    $ob->andWhere('o.amount <= :max')
                        ->setParameter('max',$maxAmount);
                }

                if($debitorSubstring){
                    if(preg_match('#^\d+$#',$debitorSubstring)){
                        $ob->andWhere('o.fromAccountNumber = :debitorNumber')
                            ->setParameter('debitorNumber',$debitorSubstring);
                    }else{
                        $ob->andWhere($ob->expr()->like('o.debitorName', ':debitor'))
                            ->setParameter('debitor','%'.$debitorSubstring.'%');
                    }
                }

                if($creditorSubstring){
                    if(preg_match('#^\d+$#',$creditorSubstring)){
                        $ob->andWhere('o.toAccountNumber = :creditorNumber')
                            ->setParameter('creditorNumber',$creditorSubstring);
                    }else{
                        $ob->andWhere($ob->expr()->like('o.creditorName', ':creditor'))
                            ->setParameter('creditor','%'.$creditorSubstring.'%');
                    }
                }

                $executedTransactions = $ob->andWhere('o.paymentID is not NULL')
                    ->orderBy('o.executionDate',$orderBy)
                    ->getQuery()->getResult();

                if($this->get('cairn_user.api')->isRemoteCall()){
                    $res = $this->get('cairn_user.api')->serialize($executedTransactions);

                    $response = new Response($res);
                    $response->headers->set('Content-Type', 'application/json');
                    $response->setStatusCode(Response::HTTP_OK);
                    return $response;
                }

            }
        }
        return $this->render('CairnUserBundle:Banking:account_operations.html.twig',
            array('form' => $form->createView(),
            'transactions'=>$executedTransactions,'futureAmount' => $totalAmount,'account'=>$account));
    }


    /*
     * Redirects to the different options regarding operation @param
     *
     * @param string $type Type of operation requested. Possible types restricted in routing.yml
     */  
    public function bankingOperationsAction(Request $request)
    {
        return $this->render('CairnUserBundle:Banking:operations.html.twig');
    }

    /**
     * Allows to define who will benefit from the transaction. 
     *
     * According to who will be the beneficiary (new / self / registered beneficiary) the card security layer will be required or not
     * @param string $frequency Possibles frequencies restricted in routing.yml : unique/recurring
     *
     */
    public function transactionToAction(Request $request, $frequency, $_format)
    {
        $accountService = $this->get('cairn_user_cyclos_account_info');
        $currentUser = $this->getUser();
        $debitorVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $selfAccounts = $accountService->getAccountsSummary($debitorVO->id);
        if($_format == 'json'){
            return $this->json(array('frequency'=>$frequency));
        }
        return $this->render('CairnUserBundle:Banking:transaction_to.html.twig',array('frequency'=>$frequency,'accounts'=>$selfAccounts));
    }

    /**
     * checks that the requested frequency is valid
     *
     * This function is used as another verification layer in case there is a missing verification in routes.
     *
     * @param string $frequency
     * @return bool
     */
    public function isValidFrequency($frequency)
    {
        return ( ($frequency == 'unique') || ($frequency == 'recurring') );
    }


    /**
     * Automatically add a first line to the operation description $description, depending on the type $type 
     *
     *@param string $type transaction | conversion | reconversion | deposit | withdrawal
     *@param text $description
     *@return text edited description
     */
    private function editDescription($type,$description)
    {
        switch ($type) {
        case "transaction":
            $prefix = $this->getParameter('cairn_default_transaction_description');
            break;
        case "reconversion":
            $prefix = $this->getParameter('cairn_default_reconversion_description');

            break;
        case "conversion":
            $prefix = $this->getParameter('cairn_default_conversion_description');

            break;
        case "deposit":
            $prefix = $this->getParameter('cairn_default_deposit_description');

            break;
        case "withdrawal":
            $prefix = $this->getParameter('cairn_default_withdrawal_description');

            break;
        }
        return $prefix."\n".$description;
    }


    /**
     * Builds the transaction request on the cyclos side and created a payment between users with a review to be confirmed
     *
     * If the 'to' attribute of the query request is set to 'new', this action will be preceded by the card security layer.
     * To build the transaction request, Cyclos needs : 
     *      _ a creditor account
     *      _ a debtor account
     *      _ a direction : USER_TO_USER | USER_TO_SELF
     *      _ an amount (always positive)
     *      _a time data : depends if frequency is set to 'unique' or 'recurring'
     *      _a transfer type
     * Once the payment preview is built on Cyclos-side, a Symfony entity "Operation" is created and persisted with all necessary 
     * attributes except "paymentID" as the payment has not been executed yet.
     *
     */
    public function transactionRequestAction(Request $request, $to, $frequency, $_format)
    {
        $session = $request->getSession();
        $currentUser = $this->getUser();

        $apiService = $this->get('cairn_user.api');

        if( count($currentUser->getPhones()) == 0){
            $message = 'Vous devez avoir un numéro de téléphone associé à votre compte pour faire un virement';
            if($apiService->isRemoteCall()){
                return $apiService->getErrorResponse(array($message),Response::HTTP_UNAUTHORIZED);
            }
            $session->getFlashBag()->add('info',$message);
            return $this->redirectToRoute('cairn_user_users_phone_add',array('username'=>$currentUser->getUsername()));
        }

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');

        $session->set('frequency',$frequency);

        $accountService = $this->get('cairn_user_cyclos_account_info');
        

        $type = 'transaction';

        $debitorVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $selfAccounts = $accountService->getAccountsSummary($debitorVO->id);

        if($currentUser->hasRole('ROLE_PRO') || $currentUser->hasRole('ROLE_PERSON')){
            $directionPrefix = 'USER';
        }else{
            $directionPrefix = 'SYSTEM';
        }

        if($to == 'new'){
            $direction = $directionPrefix.'_TO_USER';
            $toAccounts = array();
        }elseif($to == 'self'){
            $direction = $directionPrefix.'_TO_SELF';
            $toAccounts = $selfAccounts;
            if(count($toAccounts) == 1){
                $session->getFlashBag()->add('info','Vous n\'avez qu\'un seul compte.');
                return $this->redirectToRoute('cairn_user_banking_transaction_to',array('frequency'=>$frequency));
            }
        }elseif($to == 'beneficiary'){

            $beneficiaries = $currentUser->getBeneficiaries();

            if(count($beneficiaries) == 0){
                $session->getFlashBag()->add('info','Vous n\'avez aucun bénéficiaire enregistré');
                return $this->redirectToRoute('cairn_user_banking_transaction_to',array('frequency'=>$frequency));
            }

            $direction = $directionPrefix.'_TO_USER';

            $toAccounts = array();
            foreach($beneficiaries as $beneficiary){
                $toAccounts[] = array('unlimited'=>false,'owner'=>$beneficiary->getUser()->getName(),'number'=>$beneficiary->getICC());
            }
        }else{
            $session->getFlashBag()->add('error','Type de destinataire non reconnu');
            return $this->redirectToRoute('cairn_user_banking_transaction_to',array('frequency'=>$frequency));
        }

        $operation = new Operation();
        $operation->setToAccountNumber($to); //todo: hack, make it cleaner

        if($frequency == 'unique'){
            $form = $this->createForm(SimpleOperationType::class, $operation);
        }else{
            $form = $this->createForm(RecurringOperationType::class, $operation);
        }

        if( count($currentUser->getPhoneNumbers()) > 1){
            $form->add('sendTo',ChoiceType::class,array(
                'label'=>'Envoyer le code de confirmation au',
                //'empty_data'=>null,
                //'data'=>'',
                'choices'=>$currentUser->getPhoneNumbers(),
                'choice_label' => function ($value) {
                    return $value;
                },
                'multiple'=>false,
                'mapped'=>false,
            
            ));
        }


        if($request->isMethod('POST')){

            if($apiService->isRemoteCall()){

                $jsonRequest = json_decode($request->getContent(), true);
                if(! preg_match('/^\d+$/',$jsonRequest['executionDate'])){
                    return $apiService->getErrorResponse(array('Wrong execution date format. It should be a timestamp'),Response::HTTP_BAD_REQUEST);
                }

                //HERE, CHECK FOR API SECURITY CODE
                $rightKey = hash('sha256',$this->getParameter('api_secret').$jsonRequest['executionDate']);
                $sentKey = $jsonRequest['api_secret'];
                if(! ($rightKey == $sentKey)){
                    return $apiService->getErrorResponse(array('Wrong API Security code'),Response::HTTP_UNAUTHORIZED);
                }

                $jsonRequest['executionDate'] = date('Y-m-d',$jsonRequest['executionDate']);

                unset($jsonRequest['api_secret']);
                $form->submit($jsonRequest);
            }else{
                $form->handleRequest($request);
            }
            if($form->isValid()){
                $operation->setReason($this->editDescription($type, $operation->getReason()));

                if($frequency == 'recurring'){
                    $dataTime = new \stdClass();
                    $dataTime->periodicity =         $form->get('periodicity')->getData();
                    $dataTime->firstOccurrenceDate = $form->get('firstOccurrenceDate')->getData();
                    $dataTime->lastOccurrenceDate = $form->get('lastOccurrenceDate')->getData();
                }else{
                    $dataTime = $operation->getExecutionDate();
                }

                $fromAccount = $accountService->getAccountByNumber($operation->getFromAccount()->number);
                $toAccount = $operation->getToAccount();

                if($to == 'beneficiary'){
                    $beneficiary = $em->getRepository('CairnUserBundle:Beneficiary')->findOneBy(array('ICC'=>$toAccount->number));
                    if(!$beneficiary || !$currentUser->hasBeneficiary($beneficiary)){
                        $session->getFlashBag()->add('error','Le compte créditeur ne fait pas partie de vos bénéficiaires.' );
                        return new RedirectResponse($request->getRequestUri());
                    }
                }elseif($to == 'self' && !$accountService->hasAccount($debitorVO->id, $toAccount['number'])){
                    $session->getFlashBag()->add('error','Le compte créditeur ne vous appartient pas.' );
                    return new RedirectResponse($request->getRequestUri());
                }

                $toUserVO = $this->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($toAccount->number);

                $bankingService = $this->get('cairn_user_cyclos_banking_info'); 
                $paymentData = $bankingService->getPaymentData($fromAccount->owner,$toUserVO,NULL);

                //filter the potential transfer types according to the debitor account type
                $transferTypes = $paymentData->paymentTypes;
                $accurateTransferTypes = array();
                foreach($transferTypes as $transferType){
                    if( strpos($transferType->internalName, 'virement_inter_adherent') !== false){
                        $onlineTransferType = $transferType;
                    }
                }

                if($operation->getExecutionDate()->format('Y-m-d') != $operation->getSubmissionDate()->format('Y-m-d')){
                    $operation->setType(Operation::TYPE_TRANSACTION_SCHEDULED);
                }else{
                    $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);
                }

                $amount = $operation->getAmount();

                //WARNING :  on Cyclos side, there is only one field for description, whereas on Symfony side there is
                //both reason & description. For this reason, we transmit the reason as cyclos description
                $cyclosDescription = $operation->getReason();

                if($frequency == 'recurring'){
                    try{
                        $res = $this->bankingManager->makeRecurringPreview($paymentData,$amount,$cyclosDescription,$onlineTransferType,$dataTime,$this->getParameter('kernel.environment'));
                    }catch(\Exception $e){
                        if(property_exists($e,'errorCode')){
                            if($e->errorCode == 'VALIDATION'){
                                $session->getFlashBag()->add('error','Virement permanent invalide, les dates sont invalides');
                                return new RedirectResponse($request->getRequestUri());

                            }
                        }

                        throw $e;

                    }
                }else{
                    $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$cyclosDescription,$onlineTransferType,$dataTime);
                }

                $session->set('paymentReview',$res);

                $creditorUser = $userRepo->findOneBy(array('username'=>$toUserVO->username));
                $operation->setFromAccountNumber($res->fromAccount->number);
                $operation->setToAccountNumber($res->toAccount->number);
                $operation->setCreditor($creditorUser);
                $operation->setDebitor($currentUser);
                $em->persist($operation);
                $em->flush();

                //SEND VALIDATION CODE HERE
                //give a new code to be validated
                if($this->getParameter('kernel.environment') != 'prod'){
                    $code = 1111;
                }else{
                    $code = rand(100000,999999);
                }

                // send SMS with validation code to current user's new phone number
                $phoneNumber = $form->has('sendTo') ? $form->get('sendTo')->getData() : $currentUser->getPhones()[0]->getPhoneNumber();
                $sms = $this->get('cairn_user.message_notificator')->sendSMS($phoneNumber,'Code de confirmation de votre virement '.$code.' utilisable jusqu\'à ' . date('H:i',strtotime(date('H:i')." +5 mins")) );

                $session->getFlashBag()->add('info','Un code de confirmation de votre virement vous a été envoyé au '.$phoneNumber);
                //$em->persist($sms);
                //$em->flush();

       
                $encoder = $this->get('security.encoder_factory')->getEncoder($currentUser);
                $session_code = $encoder->encodePassword($code,$currentUser->getSalt());
                $session->set('confirmationCode', $session_code);

                if($_format == 'json'){
                    $redirectUrl = $this->generateUrl(
                        'cairn_user_api_transaction_confirm',
                        array(
                            'id'=>$operation->getID(),
                        )
                    );

                    $redirectOperation = array('confirmation_url' => $redirectUrl,
                                 'operation' => $operation);

                    $res = $this->get('cairn_user.api')->serialize($redirectOperation);
                    $response = new Response($res);
                    $response->headers->set('Content-Type', 'application/json');
                    $response->setStatusCode(Response::HTTP_CREATED);
                    return $response;

                }
                return $this->redirectToRoute('cairn_user_banking_operation_confirm',
                    array('_format'=>$_format,'id'=>$operation->getID(),'type'=>$type));


            }else{
                $apiService = $this->get('cairn_user.api');
                if( $apiService->isRemoteCall()){
                    return $apiService->getFormErrorResponse($form);
                }
            }

        }


        return $this->render('CairnUserBundle:Banking:transaction.html.twig',array(
            'form'=>$form->createView()));

    }


    /**
     * Confirm the requested operation on the Cyclos-side and hydrate Symfony equivalent entity with attribute paymentID
     *
     * @param string $type type of operation occurring : transaction
     * @param Operation $operation transaction to be confirmed
     */
    public function confirmOperationAction(Request $request, Operation $operation, $_format)
    {
        
        if($operation->getPaymentID()){
            throw new LogicException('Cette opération a déjà été traitée');
        }

        $apiService = $this->get('cairn_user.api');

        if( $apiService->isRemoteCall()){
            $jsonRequest = json_decode($request->getContent(), true);
            //HERE, CHECK FOR API SECURITY CODE
            $rightKey = hash('sha256',$this->getParameter('api_secret').$operation->getID() );
            $sentKey = $jsonRequest['api_secret'];
            if(! ($rightKey == $sentKey)){
                return $apiService->getErrorResponse(array('Wrong API Security code'),Response::HTTP_UNAUTHORIZED);
            }
            unset($jsonRequest['api_secret']);
        }
        $messageNotificator = $this->get('cairn_user.message_notificator');

        $currentUser = $this->getUser();
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $accountNumbers = $this->get('cairn_user_cyclos_account_info')->getAccountNumbers($ownerVO->id);

        $fromNumber = $operation->getFromAccountNumber();

        if((! in_array($fromNumber,$accountNumbers))){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }   

        $em = $this->getDoctrine()->getManager();

        //if date interval > 3min => timeout
        $today = new \Datetime();
        if( $operation->getSubmissionDate()->diff($today)->i > 5 ){
            $em->remove($operation);
            $em->flush();

            throw new \Exception('Operation timeout');
        }


        $type = 'transaction';
        $session = $request->getSession();
        $paymentReview = $session->get('paymentReview');

        $confirmationCodeAttr = 'confirmationCode';

        $form = $this->createFormBuilder()
            ->add($confirmationCodeAttr,TextType::class, array('label'=>'Code de confirmation', 'required'=>false))
            ->add('cancel',    SubmitType::class, array('label' => 'Annuler','attr' => array('class'=>'red')))
            ->add('save',      SubmitType::class, array('label' => 'Confirmation'))
            ->getForm();

        if($request->isMethod('POST')){ //form filled and submitted
            if($_format == 'json'){
                $form->submit($jsonRequest);
            }else{
                $form->handleRequest($request);
            }

            if($form->isValid()){
                if($form->get('save')->isClicked()){
                    //CHECK VALIDATION CODE HERE

                    $encoder = $this->get('security.encoder_factory')->getEncoder($currentUser);
                    
                    $providedCode = $form->get($confirmationCodeAttr)->getData();
                    $session_code = $session->get($confirmationCodeAttr);

                    //valid code
                    if($encoder->encodePassword($providedCode,$currentUser->getSalt()) == $session_code){

                        //according to the given type and amount, adapt the banking operation
                        if(property_exists($paymentReview,'recurringPayment')){ //recurring payment

                            $recurringPaymentVO = $this->bankingManager->makeRecurringPayment($paymentReview->recurringPayment);
                            // Cyclos script will create operation when the transfer will occur
                            $em->remove($operation);
                            $session->getFlashBag()->add('success','Votre opération a été enregistrée.');

                            return $this->redirectToRoute('cairn_user_banking_transactions_recurring_view_detailed',array('id'=>$recurringPaymentVO->id )); 

                        }else{
                            if($operation->getType() == Operation::TYPE_TRANSACTION_SCHEDULED){
                                $scheduledPaymentVO = $this->bankingManager->makePayment($paymentReview->scheduledPayment);
                                $operation->setPaymentID($scheduledPaymentVO->id);
                            }else{
                                $paymentVO = $this->bankingManager->makePayment($paymentReview->payment);
                                $operation->setPaymentID($paymentVO->transferId);

                                //IN CASE OF IMMEDIATE PAYMENT, SEND EMAIL NOTIFICATION
                                $body = $this->get('templating')->render('CairnUserBundle:Emails:payment_notification.html.twig',
                                    array('operation'=>$operation,'type'=>'transaction'));

                                $messageNotificator->notifyByEmail('Vous avez reçu un virement',
                                    $messageNotificator->getNoReplyEmail(),$operation->getCreditor()->getEmail(),$body);

                            }
                        }

                        $em->flush();
                        $session->remove($confirmationCodeAttr);
                        $session->remove('confirmationTries');

                        if($_format == 'json'){
                            $res = $this->get('cairn_user.api')->serialize($operation);
                            $response = new Response($res);
                            $response->headers->set('Content-Type', 'application/json');
                            $response->setStatusCode(Response::HTTP_CREATED);
                            return $response;

                        }
                        $session->getFlashBag()->add('success','Votre opération a été enregistrée.');
                        return $this->redirectToRoute('cairn_user_banking_transfer_view',array('paymentID'=>$operation->getPaymentID() ));

                    }else{//wrong confirmation code 
                        if(!$session->get('confirmationTries')){
                            $session->set('confirmationTries',1);
                        }

                        if($session->get('confirmationTries') < 3){
                            $session->set('confirmationTries',$session->get('confirmationTries') + 1);
                            $session->getFlashBag()->add('error','Code de confirmation erroné');
                        }else{
                            $session->remove($confirmationCodeAttr);
                            $session->remove('confirmationTries');

                            $session->getFlashBag()->add('error','3 erreurs de saisie : le virement a été annulé');

                            $em->remove($operation);
                            $em->flush();
                            return $this->redirectToRoute('cairn_user_banking_operations',array('type'=>$type)); 

                        }
                    }
                }else{//cancel button clicked
                    $session->remove($confirmationCodeAttr);
                    $em->remove($operation);
                    $em->flush();
                    return $this->redirectToRoute('cairn_user_banking_operations',array('type'=>$type)); 
                }
            }
        }
        return $this->render('CairnUserBundle:Banking:operation_confirm.html.twig', array('form' => $form->createView(),'operationReview' => $paymentReview,'date'=>$operation->getExecutionDate()));

    }

    /**
     * Executes a failed occurrence with id $id 
     *
     * If an occurrence is to be executed
     *
     *@param bigint $id ID of the failed occurrence: RecurringPaymentVO id
     *@throws Cyclos\ServiceException
     */
    public function executeOccurrenceAction(Request $request, $recurringID, $occurrenceID)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();

        $DTO = new \stdClass();
        $DTO->failureId = $occurrenceID;

        $recurringPaymentData = $this->get('cairn_user_cyclos_banking_info')->getRecurringTransactionDataByID($recurringID);

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){
                try{
                    $transferVO = $this->bankingManager->processOccurrence($DTO);

                    $operation = $em->getRepository('CairnUserBundle:Operation')->findOneBy(array('paymentID'=>$recurringID,'type'=>Operation::TYPE_TRANSACTION_RECURRING));

                    if($operation){
                        $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);
                        $operation->setPaymentID($transferVO->id);
                        
                    }else{
                        $operation = new Operation();

                        $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);
                        $operation->setPaymentID($transferVO->id);
                        $operation->setRecurringID($recurringID);

                        $operation->setAmount($transferVO->currencyAmount->amount);
                        $operation->setReason($recurringPaymentData->transaction->description);
                        $operation->setFromAccountNumber($transferVO->from->number);
                        $operation->setDebitor($currentUser);

                        $creditorUser = $em->getRepository('CairnUserBundle:User')->findOneByUsername($transferVO->to->owner->shortDisplay);
                        $operation->setCreditor($creditorUser);
                        $operation->setToAccountNumber($transferVO->to->number);
                        $operation->setExecutionDate(new \Datetime($transferVO->date));
                        $operation->setSubmissionDate(new \Datetime($recurringPaymentData->transaction->date));

                        $em->persist($operation);
                    }

                    $em->flush();

                    $session->getFlashBag()->add('success','Le virement a été effectué avec succès.');

                }catch(\Exception $e){
                    if($e instanceof Cyclos\ServiceException){
                        if($e->errorCode == 'INSUFFICIENT_BALANCE'){
                            $message = 'Vous n\'avez pas les fonds nécessaires. Le virement ne peut aboutir';
                            $session->getFlashBag()->add('error',$message);
                        }
                    }
                    else{
                        throw $e;
                    }
                    
                }

                return $this->redirectToRoute('cairn_user_banking_transactions_recurring_view_detailed',array('id' => $recurringID));
            }
        }

        return $this->render('CairnUserBundle:Banking:execute_occurrence.html.twig', array(
            'form'   => $form->createView()
        ));
    }


    /**
     * Returns transactions, either executed or scheduled
     *
     * @param string $type transaction
     */
    public function viewOperationsAction(Request $request, $frequency, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        if(!$this->isValidFrequency($frequency)){
            return $this->redirectToRoute('cairn_user_banking_operations_view',array(
                'format'=>$_format,
                'frequency'=>'unique'
            ));
        }

        $operationRepo = $em->getRepository('CairnUserBundle:Operation');
        $session->set('frequency',$frequency);

        $bankingService = $this->get('cairn_user_cyclos_banking_info');
        $accountService = $this->get('cairn_user_cyclos_account_info');

        $user = $this->getUser();
        $userVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($userVO->id);
        $accountTypesVO = array();

        foreach($accounts as $account){
            $accountTypesVO[] = $account->type;
        } 

        $form = $this->createForm(ConfirmationType::class);

        if($frequency == 'unique'){
            $accountNumbers = $accountService->getAccountNumbers($userVO->id);

            $ob = $operationRepo->createQueryBuilder('o');
            $processedTransactions = $ob->where($ob->expr()->in('o.fromAccountNumber', $accountNumbers))
                ->andWhere('o.paymentID is not NULL')
                ->andWhere('o.recurringID is NULL')
                ->andWhere('o.executionDate <= :date')
                ->andWhere('o.type = :type')
                ->setParameter('type', Operation::TYPE_TRANSACTION_EXECUTED)
                ->setParameter('date',new \Datetime())
                ->orderBy('o.executionDate','DESC')
                ->getQuery()->getResult();

            $ob = $operationRepo->createQueryBuilder('o');
            $futureInstallmentQuery = $ob->where($ob->expr()->in('o.fromAccountNumber', $accountNumbers))
                ->andWhere('o.paymentID is not NULL')
                ->andWhere($ob->expr()->in('o.type',array(Operation::TYPE_TRANSACTION_SCHEDULED,Operation::TYPE_SCHEDULED_FAILED)))
                ->orderBy('o.executionDate','ASC')
                ->getQuery();

            $futureInstallments = $futureInstallmentQuery->getResult();

            return $this->render('CairnUserBundle:Banking:view_single_transactions.html.twig',
                array('processedTransactions'=>$processedTransactions ,
                'futureInstallments'=> $futureInstallments,
                'form'=>$form->createView()
            ));

        }else{
            $processedTransactions = $bankingService->getRecurringTransactionsDataBy(
                $userVO,$accountTypesVO,array('CLOSED','CANCELED'));

            $ongoingTransactions = $bankingService->getRecurringTransactionsDataBy(
                $userVO,$accountTypesVO,array('OPEN'));

            if($_format == 'json'){
                return $this->json(array(
                    'processedTransactions'=>$processedTransactions ,
                    'ongoingTransactions'=> $ongoingTransactions));
            }

            return $this->render('CairnUserBundle:Banking:view_recurring_transactions.html.twig', 
                array('form'=>$form->createView(),'processedTransactions'=>$processedTransactions,'ongoingTransactions' => $ongoingTransactions));

        }
    }


    /**
     * Retrieves processed|failed occurrences of a recurring transaction 
     *
     * @param int $id Identifier of the recurring transaction
     */
    public function viewDetailedRecurringTransactionAction(Request $request, $id, $_format)
    {
        $session = $request->getSession();

        //an instance of RecurringPaymentData contains an attribute occurrences which
        //contains instances of RecurringPaymentOccurrenceVO. Beware, although the documentation mentiones it, The transferDate 
        //attribute is not specified if the transfer has not been executed yet
        $recurringPaymentData = $this->get('cairn_user_cyclos_banking_info')->getRecurringTransactionDataByID($id);

        $form = $this->createForm(ConfirmationType::class);

        if($_format == 'json'){
            return $this->json(array('data'=>$recurringPaymentData));
        }
        return $this->render('CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig',array(
            'data'=>$recurringPaymentData,
            'form'=>$form->createView()
            ));
    }

    /**
     * Get details of a specific transfer
     *
     *@param Operation $operation  
     */
    public function viewTransferAction(Request $request, Operation $operation,$_format)
    {
        $session = $request->getSession();

        $currentUser = $this->getUser();
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $accountNumbers = $this->get('cairn_user_cyclos_account_info')->getAccountNumbers($ownerVO->id);

        $fromNumber = $operation->getFromAccountNumber();
        $toNumber = $operation->getToAccountNumber();

        if((! in_array($fromNumber,$accountNumbers)) && (! in_array($toNumber,$accountNumbers))){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }   

        //to see the content, check that currentUser is owner or currentUser is referent
        if(! ($currentUser->isAdherent() || $currentUser->hasRole('ROLE_SUPER_ADMIN'))){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }

        if($_format == 'json'){
            $serializedOperation = $this->get('cairn_user.api')->serialize($operation);
            $response = new Response($serializedOperation);
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }
        return $this->render('CairnUserBundle:Banking:transfer_view.html.twig',array(
            'operation'=>$operation));
    }


    /**
     * Either blocks, unblocks or cancels a scheduled transaction
     *
     * @param int $id cyclos ID of the scheduled transaction
     * @param string $status action to operate on the scheduled transaction : cancel|block|open
     */
    public function changeStatusScheduledTransactionAction(Request $request, $id, $status)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $operation = $this->get('cairn_user.bridge_symfony')->fromCyclosToSymfonyOperation($id);
        if(!$operation){
            return $this->redirectToRoute('cairn_user_banking_operations_view',array('frequency'=>'unique','type'=>'transaction'));
        }
        //instance of ScheduledPaymentVO with installments
        $scheduledPayment = $this->get('cairn_user_cyclos_banking_info')->getTransactionDataByID($id)->transaction;

        //to execute a specific installment, we need to retrieve this specific installment
        //canceling, blocking or unblocking a given scheduled payment is not possible for a single installment, but for the whole payment
        $DTO = new \stdClass();

        if($status == 'execute'){
            $DTO->installment = $scheduledPayment->installments[0]->id;
        }else{
            $DTO->scheduledPayment = $id;
        }

        $form = $this->createForm(ConfirmationType::class);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){
            if($form->get('cancel')->isClicked()){
                return $this->redirectToRoute('cairn_user_banking_operations_view',array('frequency'=>'unique','type'=>'transaction'));
            }

            $res = $this->bankingManager->changeInstallmentStatus($DTO,$status);

            if(!$res->validStatus){
                $session->getFlashBag()->add('error',$res->message);
            }
            else{
                if($status == 'cancel'){
                    $em->remove($operation);
                    $em->flush();
                }

                $session->getFlashBag()->add('success','Le statut de votre virement a été modifié avec succès.');
            }

            return $this->redirectToRoute('cairn_user_banking_operations_view',array('frequency'=>'unique','type'=>'transaction'));
        }

        return $this->render('CairnUserBundle:Banking:scheduled_'.$status.'_confirm.html.twig',array('form'=>$form->createView()));

    }

    /**
     * Cancels the recurring transaction with cyclos ID $id
     *
     * @param int $id cyclos ID of the recurring transaction
     */
    public function cancelRecurringTransactionAction(Request $request, $id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){
                $recurringPaymentData = $this->get('cairn_user_cyclos_banking_info')->getRecurringTransactionDataByID($id);
                $recurringPaymentDTO = new \stdClass();
                $recurringPaymentDTO->recurringPayment = $recurringPaymentData->transaction;

                $this->bankingManager->cancelRecurringPayment($recurringPaymentDTO);

                $operation = $em->getRepository('CairnUserBundle:Operation')->findOneBy(array('paymentID'=>$id,'type'=>Operation::TYPE_TRANSACTION_RECURRING));

                if($operation){
                    $em->remove($operation);
                    $em->flush();
                }
       
                $session->getFlashBag()->add('success','Le virement permanent a été annulé avec succès');
            }

            return $this->redirectToRoute('cairn_user_banking_operations_view',array('frequency'=>'recurring'));
        }

        return $this->render('CairnUserBundle:Banking:recurring_cancel_confirm.html.twig', array(
            'form'   => $form->createView()
        ));

    }


    /**
     * Downloads a PDF document relating an operation 
     *
     * To be able to download operation notice, current user must be either debitor or creditor
     *
     * @param Operation $operation 
     */
    public function downloadOperationNoticeAction(Request $request, Operation $operation)
    {
        $session = $request->getSession();
        $bankingService = $this->get('cairn_user_cyclos_banking_info');

        $currentUser = $this->getUser();
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $accountNumbers = $this->get('cairn_user_cyclos_account_info')->getAccountNumbers($ownerVO->id);

        $fromNumber = $operation->getFromAccountNumber();
        $toNumber = $operation->getToAccountNumber();

        if((! in_array($fromNumber,$accountNumbers)) && (! in_array($toNumber,$accountNumbers))){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }   

        $html = $this->renderView('CairnUserBundle:Pdf:operation_notice.html.twig',array(
            'operation'=>$operation));

        $filename = sprintf('avis-operation-cairn-%s.pdf',date('Y-m-d'));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    /**
     * Downloads a PDF document relating the transfer notice with ID $id
     *
     * This document can be requested by a pro for himself, by an admin for himself, by an admin for a pro under its responsibility
     * which means current user is referent of account's owner
     *
     * @param int $id account ID
     * @throws Cyclos\ServiceException
     * @throws AccessDeniedException A non admin granted user requests for a SYSTEM account
     * @throws AccessDeniedException The current user is not referent of account's owner
     */
    public function downloadRIBAction(Request $request, $id, $_format)
    {
        $session = $request->getSession();
        $accountService = $this->get('cairn_user_cyclos_account_info');
        $userRepo = $this->getDoctrine()->getManager()->getRepository('CairnUserBundle:User');

        $currentUser = $this->getUser();
        $currentUserVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);

        $currentUserID = $currentUserVO->id;

        $account = $accountService->getAccountByID($id);

        //$user is account owner : if system account, owner is install admin O.w, get user from account owner cyclos id
        if($account->type->nature == 'SYSTEM'){
            if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                throw new AccessDeniedException('Ce compte ne vous appartient pas ou n\'existe pas.');
            }
            else{
                $owner = $currentUser;
            }
        }
        else{//check that owner exists, o.w maintenance must be warned(an account with owner without Doctrine association 
            //should not happen
            $owner = $this->get('cairn_user.bridge_symfony')->fromCyclosToSymfonyUser($account->owner->id);

            if(!$owner){
                $session->getFlashBag()->add('error','Donnée introuvable');
                return $this->redirectToRoute('cairn_user_welcome',array('_format'=>$_format));
            }
            if(! ($owner->hasReferent($currentUser) || $owner === $currentUser)){
                throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires');
            }
        }

        $html = $this->renderView('CairnUserBundle:Pdf:rib_cairn.html.twig',array(
            'downloader'=>$currentUser,'owner'=>$owner,'account'=>$account));

        $filename = sprintf('rib-cairn-%s.pdf',$account->type->name.'-'.$owner->getUsername());

        if($_format == 'json'){
            return new JsonResponse(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ]
            );

        }
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );

    }

    /**
     * Downloads a document with accounts overview : balance + account history for a given period
     *
     * This document can be requested by a pro for himself, by a SUPER_ADMIN for himself
     * A local group(ROLE_ADMIN) cannot download it.
     * Two formats are possible : CSV | PDF
     * By default, end date is today's date
     *
     * @throws AccessDeniedException Current user is not a ROLE_PRO, ROLE_PERSON or a ROLE_SUPER_ADMIN 
     */
    public function downloadAccountsOverviewAction(Request $request)
    {
        $session = $request->getSession();
        $accountService = $this->get('cairn_user_cyclos_account_info');

        $currentUser = $this->getUser();
        if( $currentUser->hasRole('ROLE_ADMIN')){
            throw new AccessDeniedException('Vous ne pouvez pas télécharger le relevé de compte d\'un adhérent.');
        }

        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);

        $accounts = $accountService->getAccountsSummary($ownerVO->id);

        $form = $this->createFormBuilder()
            ->add('format',ChoiceType::class,array(
                'label'=>'Format du fichier',
                'choices'=>array('CSV'=>'csv','PDF'=>'pdf'),
                'multiple'=>false,
            ))
                ->add('accounts',ChoiceType::class,array(
                    'label'=>'Comptes',
                    'choices'=>$accounts,
                    //'data'=>json_decode(json_encode($accounts[0]), true),
                    'choice_label'=>'type.name',
                    //'multiple'=>true,
//                    'expanded'=>true
                ))
                    ->add('begin', DateType::class,array(
                        'label'=>'depuis',
                        'choice_translation_domain'=>true,
                        'data'=> date_modify(new \Datetime(),'-1 months'),
                        'required'=>false))
                        ->add('end', DateType::class,array(
                            'label'=>'jusqu\'à',
                            'choice_translation_domain'=>true,
                            'data'=> new \Datetime(),
                            'required'=>false))
                            ->add('save', SubmitType::class,array(
                                'label'=>'Télécharger'))
                                ->getForm();

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $dataForm = $form->getData();

                $format = $dataForm['format'];
                $begin = $dataForm['begin'];
                $end = $dataForm['end'];

                //if nothing selected, select all
                $accounts = array($dataForm['accounts']);
                if(! $this->get('cairn_user.datetime_checker')->isValidInterval($begin,$end)){
                    $session->getFlashBag()->add('error','La date de fin ne peut être antérieure à la date de première échéance.');
                    return new RedirectResponse($request->getRequestUri());
                }

                //+1 day because the time is 00:00:00 so if user input 2018-07-13 the filter will get payments 
                //until 2018-07-12 23:59:59
                $period = array('begin' => $dataForm['begin']->format('Y-m-d'),
                    'end' => date_modify($dataForm['end'],'+1 day')->format('Y-m-d'));

                if($format == 'csv'){
                    $response = new StreamedResponse();
                    $response->setCallback(function() use($period,$accounts) {

                        foreach($accounts as $account){
                            $history = $this->get('cairn_user_cyclos_account_info')->getAccountHistory($account->id,$period);

                            $handle = fopen('php://output', 'r+');

                            $userService = $this->get('cairn_user_cyclos_user_info');

                            // Add the header of the CSV file
                            fputcsv($handle, array('Situation de votre compte ' . $account->type->name .' '. $userService->getOwnerName($account->owner) . ' (Cairn) au '. $period['end']),';');
                            fputcsv($handle,array('Numéro de compte Cairn : ' . $account->number),';');
                            fputcsv($handle,array('Solde initial : ' . $history->status->balanceAtBegin),';');

                            fputcsv($handle, array('Date', 'Motif','Partie prenante', 'Débit', 'Crédit','Solde'),';');
                            $balance = $history->status->balanceAtBegin;
                            foreach($history->transactions as $transaction){
                                $balance += $transaction->amount;
                                if($transaction->amount > 0){
                                    $credit = $transaction->amount;
                                    $debit = NULL;
                                }else{
                                    $debit = $transaction->amount;
                                    $credit = NULL;

                                }

                                $date = new \Datetime($transaction->date);

                                fputcsv(
                                    $handle, // The file pointer
                                    array($date->format('d-m-Y'), 
                                    $transaction->description, 
                                    $userService->getOwnerName($transaction->relatedAccount->owner),
                                    $debit, 
                                    $credit,
                                    $balance), // The fields
';' // T    he delimiter
                      );

                            }
                            fputcsv($handle,array('Solde au ' . $period['end'] . ' : '.$history->status->balanceAtEnd),';');
                            fputcsv($handle,array());
                        }
                        fclose($handle);
                    });

                    //          $response->setStatusCode(200);
                    $response->headers->set('Content-Type', 'application/force-download');
                    $response->headers->set('Content-Disposition', 'attachment; filename="relevé-Cairn'.date('Y-m-d').'.csv"');

                    return $response; 
                }
                else{//pdf

                    $html = '';
                    foreach($accounts as $account){
                        $history = $accountService->getAccountHistory($account->id,$period);

                        $html = $html . $this->renderView('CairnUserBundle:Pdf:accounts_statement.html.twig',
                            array('account'=>$account,'history'=>$history,'period'=>$period));

                    }
                    $filename = sprintf('relevé-de-comptes-%s.pdf',$account->type->name . date('Y-m-d'));
                    return new Response(
                        $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                        200,
                        [
                            'Content-Type'        => 'application/pdf',
                            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                        ]
                    );
                }


            }
        }
        return $this->render('CairnUserBundle:Banking:accounts_download.html.twig',array('form'=>$form->createView(),'accounts'=>$accounts));
    }

    

    public function confirmOnlinePaymentAction(Request $request, $suffix)
    {
        $debitorUser = $this->getUser();

        if(! $debitorUser->isAdherent()){
            throw new AccessDeniedException('Pas adhérent');
        }

        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $oPRepo = $em->getRepository('CairnUserBundle:OnlinePayment');
        $securityService = $this->get('cairn_user.security');

        $onlinePayment = $oPRepo->findOneByUrlValidationSuffix($suffix);

        if(! $onlinePayment){
            $session->getFlashBag()->add('error','Aucun paiement ne correspond à votre recherche');
            return $this->redirect($request->headers->get('referer'));
        }

        $creditorUser = $userRepo->findOneByMainICC($onlinePayment->getAccountNumber());
        if(! $creditorUser->hasRole('ROLE_PRO')){
            return $this->redirect($onlinePayment->getUrlFailure());
        }

        if(! $debitorUser->getCard()){
            return $this->redirect($onlinePayment->getUrlFailure());
        }

        $operation = new Operation();
        $operation->setToAccountNumber($onlinePayment->getAccountNumber()); //todo: hack, make it cleaner
        $operation->setAmount($onlinePayment->getAmount());
        $operation->setReason($onlinePayment->getReason());
        $operation->setType(Operation::TYPE_ONLINE_PAYMENT);
        $operation->setCreditor($creditorUser);
        $operation->setDebitor($debitorUser);
        $operation->setFromAccountNumber($debitorUser->getMainICC());
        $operation->setSubmissionDate($onlinePayment->getSubmittedAt());

        $positions = $debitorUser->getCard()->generateCardPositions();
        if($request->isMethod('GET')){
            $session->set('position',$positions['index']);
        }
        $string_pos = $positions['cell'];

        $form = $this->createFormBuilder()
            ->add('field',   PasswordType::class, array('label'=>'Code de vérification '.$string_pos))
            ->add('execute', SubmitType::class, array('label' => 'Exécuter'))
            ->add('cancel', SubmitType::class, array('label' => 'Abandonner'))
            ->getForm();

        if ($request->isMethod('POST')){
            if($form->handleRequest($request)->isValid()){
                if($form->get('execute')->isClicked()){

                    $position = $session->get('position');
                    $cardKey =  $form->get('field')->getData();

                    $event = new InputCardKeyEvent($debitorUser,$cardKey,$position, $session);
                    $this->get('event_dispatcher')->dispatch(SecurityEvents::INPUT_CARD_KEY,$event);

                    if($event->getRedirect()){
                        $redirectUrl = $onlinePayment->getFailureSuccess();
                        return $this->redirect($redirectUrl);
                    }

                    $nbTries = $debitorUser->getCardKeyTries();
                    if($nbTries != 0){
                        $session->getFlashBag()->add('error','Clé invalide. Veuillez réessayer');
                        return new RedirectResponse($request->getRequestUri());
                    }

                    //make payment on Cyclos-side
                    try{
                        $bankingService = $this->get('cairn_user_cyclos_banking_info');

                        $paymentData = $bankingService->getPaymentData($debitorUser->getCyclosID(),$creditorUser->getCyclosID(),NULL);
                        foreach($paymentData->paymentTypes as $paymentType){
                            if(preg_match('#virement_inter_adherent#', $paymentType->internalName)){
                                $onlineTransferType = $paymentType;
                            }
                        }

                        //preview allows to make sure payment would be executed according to provided data
                        $res = $this->bankingManager->makeSinglePreview($paymentData,$onlinePayment->getAmount(),$onlinePayment->getReason(),$onlineTransferType,$operation->getExecutionDate());
                        $paymentVO = $this->bankingManager->makePayment($res->payment);
                    }catch(\Exception $e){
                        if($e instanceof Cyclos\ServiceException){
                           
                            /*this is the only criteria that could be checked whether payment data have already been checked or not
                             */
                            if($e->errorCode == 'INSUFFICIENT_BALANCE'){ 
                                $session->getFlashBag()->add('error','Le solde de votre compte est insuffisant');
                                return new RedirectResponse($request->getRequestUri());
                            }

                        }

                         throw $e;
                    }



                    $operation->setPaymentID($paymentVO->transferId);

                    $redirectUrl = $onlinePayment->getUrlSuccess();


                    $webhook = $securityService->vigenereDecode($creditorUser->getApiClient()->getWebhook());

                    $payload = json_encode(array(
                        'code' => Response::HTTP_CREATED,
                        'payment_id'=> $operation->getPaymentID(),
                        'amount' => $operation->getAmount(),
                        'invoice_id' => $onlinePayment->getInvoiceID(),
                        'reason' => $operation->getReason(),
                        'debitor_account_number' => $operation->getFromAccountNumber(),
                        'creditor_account_number' => $operation->getToAccountNumber(),
                        'payment_date' => $operation->getExecutionDate()->format('d-m-Y H:i:s'),
                    ));

                    $em->persist($operation);
                    $em->remove($onlinePayment);
                    $em->flush();

                    $ch = \curl_init($webhook);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    \curl_setopt($ch, CURLOPT_POST, true);
                    \curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

                    \curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($payload))
                    );

                    $result = \curl_exec($ch);
                    \curl_close($ch);


                }else{
                    $redirectUrl = $onlinePayment->getUrlFailure();

//                    $webhook = $securityService->vigenereDecode($creditorUser->getApiClient()->getWebhook());
//
//                    $payload = json_encode(array(
//                        'code' => Response::HTTP_NO_CONTENT,
//                        'invoice_id' => $onlinePayment->getInvoiceID(),
//                        'info' => 'The payment has been canceled'
//                    ));
//
                    $em->remove($onlinePayment);
                    $em->flush();
//
//                    $ch = \curl_init($webhook);
//                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                    \curl_setopt($ch, CURLOPT_POST, true);
//                    \curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//
//                    \curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//                        'Content-Type: application/json',
//                        'Content-Length: ' . strlen($payload))
//                    );
//
//                    $result = \curl_exec($ch);
//                    \curl_close($ch);


                }
            }

            return $this->redirect($redirectUrl);

        }

        return $this->render('CairnUserBundle:Banking:online_payment.html.twig', array(
            'form' => $form->createView(), 'operation'=>$operation, 'onlinePayment'=>$onlinePayment
        ));

    }
}
