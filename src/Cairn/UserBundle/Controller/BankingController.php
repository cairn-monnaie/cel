<?php
// src/Cairn/UserBundle/Controller/UserController.php

namespace Cairn\UserBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\BankingManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\SimpleTransaction;
use Cairn\UserBundle\Entity\RecurringTransaction;
use Cairn\UserBundle\Entity\Operation;


//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

//manage Forms
use Cairn\UserBundle\Form\ConversionType;
use Cairn\UserBundle\Form\ReconversionType;
use Cairn\UserBundle\Form\DepositType;
use Cairn\UserBundle\Form\WithdrawalType;
use Cairn\UserBundle\Form\TransferType;
use Cairn\UserBundle\Form\SimpleTransactionType;
use Cairn\UserBundle\Form\RecurringTransactionType;
use Cairn\UserBundle\Form\RecurringTransferType;
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\OperationType;
use Cairn\UserBundle\Form\SimpleOperationType;


use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\NumberType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;                                          
use Symfony\Component\Form\FormEvents;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * This class contains actions related to account operations 
 *
 * @Security("is_granted('ROLE_PRO')")
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

    /*
     * Shows an overview of all @param accounts
     *
     * @param User $user User entity the accounts belong to
     * @throws Cyclos\ServiceException
     * @Method("GET")
     */  
    public function accountsOverviewAction(Request $request, User $user, $_format)
    {
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id);

        if($_format == 'json'){
            return $this->json(array('user'=>$user,'accounts'=> $accounts));
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
        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        $accountTypeVO = $account->type; 

        //+1 day because the time is 00:00:00 so if currentUser input 2018-07-13 the filter will get payments until 2018-07-12 23:59:59
        $begin = date_modify(new \Datetime(),'-2 months');
        $end = date_modify(new \Datetime(),'+1 days');

        if($account->type->nature == 'SYSTEM'){
            $id = $accountID;
        }else{
            $id = $account->number;
        }

        //last operations
        $ob = $operationRepo->createQueryBuilder('o');
        $executedTransactions = $ob->where(
             $ob->expr()->orX(
                 $ob->expr()->andX(
                     'o.fromAccountNumber = :number',
                     $ob->expr()->in('o.type',Operation::getFromOperationTypes())
                 ),
                 $ob->expr()->andX(
                     'o.toAccountNumber = :number',
                     $ob->expr()->in('o.type',Operation::getToOperationTypes())
                 )
             ))
            ->andWhere('o.paymentID is not NULL')
            ->andWhere('o.executionDate BETWEEN :begin AND :end')
            ->orderBy('o.executionDate','ASC')
            ->setParameter('number',$id)
            ->setParameter('begin',$begin)
            ->setParameter('end',$end)
            ->getQuery()->getResult();

        //amount of future transactions : next month total amount
        $query = $em->createQuery('SELECT SUM(o.amount) FROM CairnUserBundle:Operation o WHERE o.type = :type AND o.executionDate < :date AND o.fromAccountNumber = :number AND o.paymentID is not NULL');
        $query->setParameter('type', Operation::TYPE_TRANSACTION_SCHEDULED)
            ->setParameter('date',date_modify(new \Datetime(),'+1 months'))
            ->setParameter('number',$id);

        $totalAmount = $query->getSingleScalarResult();

        $form = $this->createFormBuilder()
            ->add('orderBy',   ChoiceType::class, array(
                'label' => 'affiché par',
                'choices' => array('dates décroissantes'=>'DESC',
                'dates croissantes' => 'ASC')))
                ->add('begin',     DateType::class, array(
                    'label' => 'depuis',
                    'widget' => 'single_text',
                    'data' => $begin,
                    'required'=>false))
                    ->add('end',       DateType::class, array(
                        'label' => 'jusqu\'à',
                        'widget' => 'single_text',
                        'data'=> $end,
                        'required'=>false))
                        ->add('minAmount', NumberType::class,array(
                            'label'=>'Montant minimum',
                            'required'=>false))
                            ->add('maxAmount', NumberType::class,array(
                                'label'=>'Montant maximum',
                                'required'=>false))
                                ->add('keywords',  TextType::class,array(
                                    'label'=>'Mots-clés',
                                    'required'=>false))
                                    ->add('save',      SubmitType::class, array('label' => 'Rechercher'))
                                    ->getForm();

        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();            
                $orderBy = $dataForm['orderBy'];
                $begin = $dataForm['begin'];
                $end = $dataForm['end'];
                $minAmount = $dataForm['minAmount'];
                $maxAmount = $dataForm['maxAmount'];
                $keywords = $dataForm['keywords'];

                if(! $this->get('cairn_user.datetime_checker')->isValidInterval($begin, $end)){
                    $session->getFlashBag()->add('error','La date de fin ne peut être antérieure à la date de première échéance.');
                    return new RedirectResponse($request->getRequestUri());
                }

                //+1 day because the time is 00:00:00 so if currentUser input 2018-07-13 the filter will get payments until 2018-07-12 23:59:59
                $end = date_modify($end,'+1 days');

                $ob = $operationRepo->createQueryBuilder('o');
                $ob->where(
                    $ob->expr()->orX(
                        $ob->expr()->andX(
                            'o.fromAccountNumber = :number',
                            $ob->expr()->in('o.type',Operation::getFromOperationTypes())
                        ),
                        $ob->expr()->andX(
                            'o.toAccountNumber = :number',
                            $ob->expr()->in('o.type',Operation::getToOperationTypes())
                        )
                    ))
                    ->andWhere('o.paymentID is not NULL')
                    ->andWhere('o.executionDate BETWEEN :begin AND :end');
                if($minAmount){
                    $ob->andWhere('o.amount >= :min')
                        ->setParameter('min',$minAmount);
                }
                if($maxAmount){
                    $ob->andWhere('o.amount <= :max')
                        ->setParameter('max',$maxAmount);
                }
                if($dataForm['keywords']){
                    $keywords = preg_split('/\s+/',$dataForm['keywords']);
                    //separate keywords into list of words
                    for($i = 0 ; $i < count($keywords) ; $i++){
                        $ob->andWhere($ob->expr()->orX(
                            $ob->expr()->like('o.reason', '?'.$i),
                            $ob->expr()->like('o.description', '?'.$i)
                        ))
                        ->setParameter($i ,'%'.$keywords[$i].'%');

                    }
                }

                $ob->orderBy('o.executionDate',$orderBy)
                    ->setParameter('number',$id)
                    ->setParameter('begin',$begin)
                    ->setParameter('end',$end);
                $executedTransactions =  $ob->getQuery()->getResult();

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
    public function bankingOperationsAction(Request $request, $type, $_format)
    {
        if($_format == 'json'){
            return $this->json(array('type'=>$type));
        }
        return $this->render('CairnUserBundle:Banking:'.$type.'_operations.html.twig');
    }

    /**
     * Allows to define who will benefit from the transaction. 
     *
     * According to who will be the beneficiary (new / self / registered beneficiary) the card security layer will be requested or not
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

    public function clearingBankAction()
    {
        ;
    }

    public function processBankingTransfer($type,$amount)
    {
        ;
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
     * Builds the transaction request on the cyclos side and created a payment review to be confirmed
     *
     * If the 'to' attribute of the query request is set to 'new', this action will be preceded by the card security layer.
     * To build the transaction request, Cyclos needs all parameters in the cyclosProcessTransfer function : 
     *      _ a creditor account
     *      _ a debtor account
     *      _ a direction : USER_TO_USER | USER_TO_SELF | SYSTEM_TO_USER ...
     *      _ an amount (always positive)
     *      _a time data : depends if frequency is set to 'unique' or 'recurring'
     *
     */
    public function transactionRequestAction(Request $request, $to, $frequency, $_format)
    {
        $session = $request->getSession();
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');

        $session->set('frequency',$frequency);

        $accountService = $this->get('cairn_user_cyclos_account_info');

        $type = 'transaction';

        $debitorVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $selfAccounts = $accountService->getAccountsSummary($debitorVO->id);

        if($currentUser->hasRole('ROLE_PRO')){
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

        if($frequency == 'unique'){
            $operation = new Operation();
            $form = $this->createForm(SimpleOperationType::class, $operation);
        }
        //        else{
        //            $transaction = new RecurringTransaction();
        //            $form = $this->createForm(RecurringTransactionType::class, $transaction);
        //        }
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $operation->setDescription($this->editDescription($type, $operation->getDescription()));
                //                if($frequency == 'recurring'){
                //                    $dataTime = new \stdClass();
                //                    $dataTime->periodicity =         $transaction->getPeriodicity();
                //                    $dataTime->firstOccurrenceDate = $transaction->getFirstOccurrenceDate();
                //                    $dataTime->lastOccurrenceDate =  $transaction->getLastOccurrenceDate();
                //                }else{
                $dataTime = $operation->getExecutionDate();
                //                }

                $fromAccount = $accountService->getAccountByNumber($operation->getFromAccount()['accountNumber']);
                $toAccount = $operation->getToAccount();

                if($to == 'beneficiary'){
                    //TODO : changer les lignes précédentes en une seule requête d'un Beneficiary avec ICC dont la source est currentUser
                    $beneficiary = $em->getRepository('CairnUserBundle:Beneficiary')->findOneBy(array('ICC'=>$toAccount['accountNumber']));
                    if(!$beneficiary || !$currentUser->hasBeneficiary($beneficiary)){
                        $session->getFlashBag()->add('error','Le compte créditeur ne fait pas partie de vos bénéficiaires.' );
                        return new RedirectResponse($request->getRequestUri());
                    }
                }elseif($to == 'self' && !$accountService->hasAccount($debitorVO->id, $toAccount['accountNumber'])){
                    $session->getFlashBag()->add('error','Le compte créditeur ne vous appartient pas.' );
                    return new RedirectResponse($request->getRequestUri());
                }


                if($toAccount['accountNumber']){
                    $toUserVO = $this->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($toAccount['accountNumber']);
                }else{
                    $toUserVO = $this->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($toAccount['email']);
                }

                $bankingService = $this->get('cairn_user_cyclos_banking_info'); 
                $paymentData = $bankingService->getPaymentData($fromAccount->owner,$toUserVO,NULL);

                //filter the potential transfer types according to the debitor account type
                $transferTypes = $paymentData->paymentTypes;
                $accurateTransferTypes = array();
                foreach($transferTypes as $transferType){
                    if($transferType->from->id == $fromAccount->type->id){
                        $accurateTransferTypes[] = $transferType;
                    }
                }

                if($operation->getExecutionDate()->format('Y-m-d') != $operation->getSubmissionDate()->format('Y-m-d')){
                    $operation->setType(Operation::TYPE_TRANSACTION_SCHEDULED);
                }else{
                    $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);
                }

                $amount = $operation->getAmount();
                $description = $operation->getDescription();

                //                if($frequency == 'recurring'){
                //
                //                    $environment = $this->getParameter('kernel.environment');
                //                    if($toAccount['accountNumber']){
                //
                //                        foreach($accurateTransferTypes as $transferType){
                //                            $res = $this->bankingManager->makeRecurringPreview($paymentData,$amount,$description,$transferType,$dataTime,$environment);
                //                            if($res->toAccount->number == $toAccount['accountNumber']){
                //                                $session->set('paymentReview',$res);
                //                            }
                //
                //                        }
                //                    }else{
                //                        $res = $this->bankingManager->makeRecurringPreview($paymentData,$amount,$description,$accurateTransferTypes[0],$dataTime,$environment);
                //                        $session->set('paymentReview',$res);
                //
                //                    }
                //                    return $this->redirectToRoute('cairn_user_banking_operation_confirm',array('_format'=>$_format, 'type'=>$type));
                //
                //                }elseif($frequency == 'unique'){

                if($toAccount['accountNumber']){

                    foreach($accurateTransferTypes as $transferType){
                        $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$description,$transferType,$dataTime);

                        if($res->toAccount->number == $toAccount['accountNumber']){
                            $session->set('paymentReview',$res);
                        }
                    }
                }else{
                    $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$description,$accurateTransferTypes[0],$dataTime);
                    $session->set('paymentReview',$res);

                }

                $creditorUser = $userRepo->findOneBy(array('username'=>$toUserVO->username));
                $operation->setFromAccountNumber($res->fromAccount->number);
                $operation->setToAccountNumber($res->toAccount->number);
                $operation->setStakeholder($creditorUser);
                $em->persist($operation);
                $em->flush();
                return $this->redirectToRoute('cairn_user_banking_operation_confirm',
                    array('_format'=>$_format,'id'=>$operation->getID(),'type'=>$type));

                //                }


            }
        }

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'fromAccounts'=>$selfAccounts,'toAccounts'=>$toAccounts));
        }
        return $this->render('CairnUserBundle:Banking:transaction.html.twig',array(
            'form'=>$form->createView(),
            'fromAccounts'=>$selfAccounts,
            'toAccounts'=>$toAccounts));

    }

    /**
     * Requests for a reconversion from local currency to euros
     *
     * Only a pro can request for a reconversion. On the cyclos-side, the transfer occurring is from the requested user account
     * to the debitAccount(which is a specific system account with unlimited balance to justify credit/debit of the user's account).
     * Any credit/debit on an account in Cyclos must be compensated by the opposite operation on another one.
     * @Security("has_role('ROLE_PRO')")
     */
    public function reconversionRequestAction(Request $request, $_format)
    {
        $accountService = $this->get('cairn_user_cyclos_account_info');
        $session = $request->getSession();
        $currentUser = $this->getUser();
        $type = 'reconversion';

        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);
        $selfAccounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id);
        $debitAccount = $accountService->getDebitAccount();

        $form = $this->createForm(ReconversionType::class);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $dataForm = $form->getData();
                $amount = $dataForm['amount'];
                $toAccount = $debitAccount;
                $description = $this->editDescription($type,$dataForm['description']);

                $dataTime = new \Datetime(date('Y-m-d'));

                if(!$accountService->hasAccount($ownerVO->id,$dataForm['fromAccount']['id'])){
                    $session->getFlashBag()->add('error','Ce compte n\'existe pas ou ne vous appartient pas.');
                    return new RedirectResponse($request->getRequestUri());
                }

                $fromAccount = $accountService->getAccountByID($dataForm['fromAccount']['id']);

                if(!$fromAccount){
                    $session->getFlashBag()->add('error','Les champs du formulaire ne correspondent à aucun compte');
                    return new RedirectResponse($request->getRequestUri());
                }

                $review = $this->processCyclosTransaction($type,$fromAccount,$toAccount,'USER_TO_SYSTEM',$amount,'unique',$dataTime,$description);
                if(property_exists($review,'error')){//differenciate with cyclos exceptions that should not be catched
                    $session->getFlashBag()->add('error',$review->error);
                    return new RedirectResponse($request->getRequestUri());
                }

                $session->set('paymentReview',$review);
                return $this->redirectToRoute('cairn_user_banking_operation_confirm',array('type'=>$type));

            }
        }

        return $this->render('CairnUserBundle:Banking:reconversion.html.twig',array('form'=>$form->createView(),'accounts'=>$selfAccounts));
    }

    /**
     * Requests for a conversion on a user's account
     *
     * On the cyclos-side, the transfer occurring is to the requested user account  and from the debitAccount(which is a specific 
     * system account &with unlimited balance to justify credit/debit of the user's account).
     * Any credit/debit on an account in Cyclos must be compensated by the opposite operation on another one.
     * The operation must be done by an admin : a pro comes with banknotes, and admin credits account
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function conversionRequestAction(Request $request, $_format)
    {
        $accountService = $this->get('cairn_user_cyclos_account_info');
        $session = $request->getSession();


        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $currentUser = $this->getUser();
        $type = 'conversion';
        $direction = 'SYSTEM_TO_USER';

        $debitAccount = $accountService->getDebitAccount();
        $involvedAccounts = array();

        $transaction = new Operation();
        $transaction->setType(Operation::TYPE_CONVERSION);
        $transaction->setReason('Conversion Cairn '. $transaction->getSubmissionDate()->format('Y-m-d'));

        $formUser = $this->createFormBuilder()
            ->add('username', TextType::class,array('label'=>'Pseudo','required'=>false))
            ->add('email', EmailType::class,array('label'=>'Email','required'=>false))
            ->add('save', SubmitType::class,array('label'=>'Rechercher les comptes'))
            ->getForm();

        $formConversion = $this->createForm(SimpleOperationType::class, $transaction);

        $formUser->handleRequest($request);
        if($formUser->isSubmitted() && $formUser->isValid()){
            $data = $formUser->getData();
            $userToCredit = $userRepo->findOneBy(array('email'=>$data['email']));
            if(!$userToCredit){
                $userToCredit = $userRepo->findOneBy(array('username'=>$data['username']));
                if(!$userToCredit){
                    $session->getFlashBag()->add('error','Aucun professionnel trouvé');
                    return new RedirectResponse($request->getRequestUri());
                }
            }
            $userToCreditVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($userToCredit);

            $involvedAccounts = $accountService->getAccountsSummary($userToCreditVO->id);
            return $this->render('CairnUserBundle:Banking:conversion.html.twig',array('formUser'=>$formUser->createView(),'formConversion'=>$formConversion->createView(),'accounts'=>$involvedAccounts));
        }

        $formConversion->handleRequest($request);
        if($formConversion->isSubmitted() && $formConversion->isValid()){
            $amount = $transaction->getAmount();
            $description = $this->editDescription($type,$transaction->getDescription());

            $dataTime = $transaction->getExecutionDate();
            $toAccount = $accountService->getAccountByNumber($transaction->getToAccount()['accountNumber']);

            //get transfer type. Here we have a list, but with such precise arguments there is only one transfer type
            $transferType = $this->get('cairn_user_cyclos_transfertype_info')->getListTransferTypes($debitAccount->type,$toAccount->type,$direction,'PAYMENT')[0];

            //process Cyclos review
            $bankingService = $this->get('cairn_user_cyclos_banking_info'); 
            $paymentData = $bankingService->getPaymentData($debitAccount->owner,$toAccount->owner,$transferType);
            $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$description,$transferType,$dataTime);

            $transaction->setFromAccountNumber($debitAccount->number);
            $transaction->setToAccountNumber($res->toAccount->number);

            $userToCredit = $this->get('cairn_user.bridge_symfony')->fromCyclosToSymfonyUser($toAccount->owner->id);
            $transaction->setStakeholder($userToCredit);
            $em->persist($transaction);
            $em->flush();

            $session->set('paymentReview',$res);
            return $this->redirectToRoute('cairn_user_banking_operation_confirm',array('id'=>$transaction->getID(),'type'=>$type));
        }

        return $this->render('CairnUserBundle:Banking:conversion.html.twig',array('formUser'=>$formUser->createView(),'formConversion'=>$formConversion->createView(),'accounts'=>$involvedAccounts));
    }


    /**
     * Requests for a deposit on a user's account
     *
     * On the cyclos-side, the transfer occurring is to the requested user account  and from the debitAccount(which is a specific 
     * system account &with unlimited balance to justify credit/debit of the user's account).
     * Any credit/debit on an account in Cyclos must be compensated by the opposite operation on another one.
     * The operation must be done by an admin : a pro comes with banknotes, and admin credits account
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function depositRequestAction(Request $request, $_format)
    {
        $accountService = $this->get('cairn_user_cyclos_account_info');
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $currentUser = $this->getUser();
        $type = 'deposit';
        $direction = 'SYSTEM_TO_USER';

        $debitAccount = $accountService->getDebitAccount();
        $involvedAccounts = array();

        $transaction = new Operation();
        $transaction->setType(Operation::TYPE_DEPOSIT);
        $transaction->setReason('Dépôt Cairn '. $transaction->getSubmissionDate()->format('Y-m-d'));

        $formUser = $this->createFormBuilder()
            ->add('username', TextType::class,array('label'=>'Pseudo','required'=>false))
            ->add('email', EmailType::class,array('label'=>'Email','required'=>false))
            ->add('save', SubmitType::class,array('label'=>'Rechercher les comptes'))
            ->getForm();

        $formDeposit = $this->createForm(SimpleOperationType::class, $transaction);

        $formUser->handleRequest($request);
        if($formUser->isSubmitted() && $formUser->isValid()){
            $data = $formUser->getData();
            $userToCredit = $userRepo->findOneBy(array('email'=>$data['email']));
            if(!$userToCredit){
                $userToCredit = $userRepo->findOneBy(array('username'=>$data['username']));
                if(!$userToCredit){
                    $session->getFlashBag()->add('error','Aucun professionnel trouvé');
                    return new RedirectResponse($request->getRequestUri());
                }
            }
            $userToCreditVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($userToCredit);

            $involvedAccounts = $accountService->getAccountsSummary($userToCreditVO->id);
            return $this->render('CairnUserBundle:Banking:deposit.html.twig',array('formUser'=>$formUser->createView(),'formDeposit'=>$formDeposit->createView(),'accounts'=>$involvedAccounts));
        }

        $formDeposit->handleRequest($request);
        if($formDeposit->isSubmitted() && $formDeposit->isValid()){
            $amount = $transaction->getAmount();
            $description = $this->editDescription($type,$transaction->getDescription());

            $dataTime = $transaction->getExecutionDate();
            $toAccount = $accountService->getAccountByNumber($transaction->getToAccount()['accountNumber']);

            //get transfer type. Here we have a list, but with such precise arguments there is only one transfer type
            $transferType = $this->get('cairn_user_cyclos_transfertype_info')->getListTransferTypes($debitAccount->type,$toAccount->type,$direction,'PAYMENT')[0];

            //process Cyclos review
            $bankingService = $this->get('cairn_user_cyclos_banking_info'); 
            $paymentData = $bankingService->getPaymentData($debitAccount->owner,$toAccount->owner,$transferType);
            $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$description,$transferType,$dataTime);

            $transaction->setFromAccountNumber($debitAccount->number);
            $transaction->setToAccountNumber($res->toAccount->number);

            $userToCredit = $this->get('cairn_user.bridge_symfony')->fromCyclosToSymfonyUser($toAccount->owner->id);
            $transaction->setStakeholder($userToCredit);
            $em->persist($transaction);
            $em->flush();

            $session->set('paymentReview',$res);
            return $this->redirectToRoute('cairn_user_banking_operation_confirm',array('id'=>$transaction->getID(),'type'=>$type));
        }

        return $this->render('CairnUserBundle:Banking:deposit.html.twig',array('formUser'=>$formUser->createView(),'formDeposit'=>$formDeposit->createView(),'accounts'=>$involvedAccounts));
    }

    /**
     * Requests for a withdrawal from a user's account
     *
     * On the cyclos-side, the transfer occurring is to the requested user account  and from the debitAccount(which is a specific 
     * system account &with unlimited balance to justify credit/debit of the user's account).
     * Any credit/debit on an account in Cyclos must be compensated by the opposite operation on another one.
     * The operation must be done by an admin : a pro comes with banknotes, and admin credits account
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function withdrawalRequestAction(Request $request, $_format)
    {
        $accountService = $this->get('cairn_user_cyclos_account_info');
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $currentUser = $this->getUser();
        $type = 'withdrawal';
        $direction = 'USER_TO_SYSTEM';

        $debitAccount = $accountService->getDebitAccount();
        $involvedAccounts = array();

        $transaction = new Operation();
        $transaction->setType(Operation::TYPE_WITHDRAWAL);
        $transaction->setReason('Retrait Cairn '. $transaction->getSubmissionDate()->format('Y-m-d'));

        $formUser = $this->createFormBuilder()
            ->add('username', TextType::class,array('label'=>'Pseudo','required'=>false))
            ->add('email', EmailType::class,array('label'=>'Email','required'=>false))
            ->add('save', SubmitType::class,array('label'=>'Rechercher les comptes'))
            ->getForm();

        $formWithdrawal = $this->createForm(SimpleOperationType::class, $transaction);

        $formUser->handleRequest($request);
        if($formUser->isSubmitted() && $formUser->isValid()){
            $data = $formUser->getData();
            $userToDebit = $userRepo->findOneBy(array('email'=>$data['email']));
            if(!$userToDebit){
                $userToDebit = $userRepo->findOneBy(array('username'=>$data['username']));
                if(!$userToDebit){
                    $session->getFlashBag()->add('error','Aucun professionnel trouvé');
                    return new RedirectResponse($request->getRequestUri());
                }
            }
            $userToDebitVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($userToDebit);

            $involvedAccounts = $accountService->getAccountsSummary($userToDebitVO->id);
            return $this->render('CairnUserBundle:Banking:withdrawal.html.twig',array('formUser'=>$formUser->createView(),'formWithdrawal'=>$formWithdrawal->createView(),'accounts'=>$involvedAccounts));
        }

        $formWithdrawal->handleRequest($request);
        if($formWithdrawal->isSubmitted() && $formWithdrawal->isValid()){
            $amount = $transaction->getAmount();
            $description = $this->editDescription($type,$transaction->getDescription());

            $dataTime = $transaction->getExecutionDate();
            $fromAccount = $accountService->getAccountByNumber($transaction->getFromAccount()['accountNumber']);

            //get transfer type. Here we have a list, but with such precise arguments there is only one transfer type
            $transferType = $this->get('cairn_user_cyclos_transfertype_info')->getListTransferTypes($fromAccount->type,$debitAccount->type,$direction,'PAYMENT')[0];

            //process Cyclos review
            $bankingService = $this->get('cairn_user_cyclos_banking_info'); 
            $paymentData = $bankingService->getPaymentData($fromAccount->owner,$debitAccount->owner,$transferType);
            $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$description,$transferType,$dataTime);

            $transaction->setToAccountNumber($debitAccount->number);
            $transaction->setFromAccountNumber($res->fromAccount->number);

            $userToDebit = $this->get('cairn_user.bridge_symfony')->fromCyclosToSymfonyUser($fromAccount->owner->id);
            $transaction->setStakeholder($userToDebit);
            $em->persist($transaction);
            $em->flush();

            $session->set('paymentReview',$res);
            return $this->redirectToRoute('cairn_user_banking_operation_confirm',array('id'=>$transaction->getID(),'type'=>$type));
        }

        return $this->render('CairnUserBundle:Banking:withdrawal.html.twig',array('formUser'=>$formUser->createView(),'formWithdrawal'=>$formWithdrawal->createView(),'accounts'=>$involvedAccounts));
    }


    /**
     *Build the transaction review to be confirmed by the user requesting it
     *
     *
     *@param string    $type type of operation occurring : transaction|conversion|reconversion|deposit|withdrawal
     *@param stdClass  $fromAccount debtor account representing an account Java type: org.cyclos.model.banking.accounts.AccountVO
     *@param stdClass  $toAccount creditor account  representing an account Java type: org.cyclos.model.banking.accounts.AccountVO 
     *@param string    $direction USER_TO_USER | USER_TO_SYSTEM | SYSTEM_TO_USER | USER_TO_SELF
     *@param int       $amount
     *@param string    $frequency unique | recurring
     *@param stdClass  $dataTime depends on $frequency
     *@param text      $description
     *
     *@return stcClass $review payment review 
     *@throws Exception At least one of the two involved accounts is not active.
     *@throws Exception The data provided does not allow to get an unique transferTypeVO 
     *@throws Exception The TransferTypeVO is unique but inactive
     */
    public function processCyclosTransaction($type,$fromAccount,$toAccount,$direction,$amount,$frequency,$dataTime,$description)
    {
        $bankingService = $this->get('cairn_user_cyclos_banking_info'); 
        $messageNotificator = $this->get('cairn_user.message_notificator');
        $review = new \stdClass();

        $fromAccountType = $fromAccount->type;
        $toAccountType = $toAccount->type;
        $transferTypes = $this->get('cairn_user_cyclos_transfertype_info')->getListTransferTypes($fromAccountType,$toAccountType,$direction,'PAYMENT');

        $toName = $this->get('cairn_user_cyclos_user_info')->getOwnerName($toAccount->owner);
        $fromName = $this->get('cairn_user_cyclos_user_info')->getOwnerName($fromAccount->owner);

        if((!$toAccount->active) || (!$fromAccount->active)){
            $message = 'Contexte : ' . $type . ' de ' .$fromName . ' vers ' .$toName. ' L\'un des comptes est inactif. \n Détail : Compte de '.$fromName.' Numéro de compte : ' .$fromAccount->id. ' \n  Compte de '.$toName.' Numéro de compte : ' .$toAccount->id. '\n Solution potentielle : seuls les comptes actifs devraient être visibles dans le formulaire de paiement. Voir ' .$type. 'RequestAction';

            throw new \Exception($message);
        }

        //check that transfer type is unique and active
        if((count($transferTypes) >= 2) || (count($transferTypes) == 0)){//unique
            $message = 'Contexte : ' .$type. ' de ' .$fromName . ' vers ' .$toName. '. \n Détail : Il existe ' . count($transferTypes) .' types de transfert actifs allant du compte ' .$fromAccountType->name .' vers le compte ' .$toAccountType->name . ' avec une direction ' .$direction.' \n Solution : Il doit y avoir un unique type de transfert actif pour chaque direction(Entre comptes/vers compte partenaire). Ajouter/Désactiver/Supprimer les autres.';
            throw new \Exception($message);

        }
        else{ //active
            if(!$transferTypes[0]->enabled){
                $message = 'Contexte : ' .$type. ' de ' .$fromName . ' vers ' .$toName. '. \n Détail : Le type de transfert allant du compte ' .$fromAccountType->name .' vers le compte ' .$toAccountType->name . ' avec une direction ' .$direction.' est inactif. \n Solution : Activez le type de transfert en question pour permettre cette transaction d\'aboutir.';
                throw new \Exception($message);
            }
        }

        $paymentData = $bankingService->getPaymentData($fromAccount->owner,$toAccount->owner,$transferTypes[0]);

        if($frequency == 'recurring'){
            try{
                $environment = $this->getParameter('kernel.environment');
                $res = $this->bankingManager->makeRecurringPreview($paymentData,$amount,$description,$transferTypes[0],$dataTime,$environment);
                $review = $res->recurringPayment;
            }catch(\Exception $e){
                if($e instanceof Cyclos\ServiceException){
                    throw $e;
                }else{
                    $review->error = $e->getMessage();
                    return $review;
                }
            }
        }
        elseif($frequency == 'unique'){
            $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$description,$transferTypes[0],$dataTime);

            if(property_exists($res,'installments')){//specific attribute to a scheduled payment
                $review = $res->scheduledPayment;
            }else{
                $review = $res->payment;
            }
        }

        return $review;
    }




    /**
     * Confirm the requested operation on the Cyclos-side and make corresponding banking operation according to $type
     *
     *@todo :  If $type is set to 'conversion' or 'reconversion', a banking transfer must be done automatically from/to the
     * Association account from/to the user's banking account. If $type is set to 'deposit' or 'withdrawal', this is not done, but a 
     * clearingBankingAccount must be done regularly to clear the guarantee funds(numeric and banknotes)
     * @param string $type type of operation occurring : transaction|conversion|reconversion|deposit|withdrawal 
     */
    public function confirmOperationAction(Request $request, Operation $operation, $type, $_format)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $paymentReview = $session->get('paymentReview');

        $form = $this->createFormBuilder()
            ->add('cancel',    SubmitType::class, array('label' => 'Annulation'))
            ->add('save',      SubmitType::class, array('label' => 'Confirmation'))
            ->getForm();

        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                if($form->get('save')->isClicked()){
                    //according to the given type and amount, adapt the banking operation
                    switch ($type){
                    case 'transaction':
                        //                        if(property_exists($paymentReview,'recurringPayment')){ //recurring payment
                        //                            $operation->setType(Operation::TYPE_TRANSACTION_RECURRING);
                        //                            $paymentVO = $this->bankingManager->makeRecurringPayment( $paymentReview);
                        //                        }else
                        if($operation->getType() == Operation::TYPE_TRANSACTION_SCHEDULED){
                            $paymentVO = $this->bankingManager->makePayment($paymentReview->scheduledPayment);
                            $operation->setPaymentID($paymentVO->id);
                        }else{
                            $paymentVO = $this->bankingManager->makePayment($paymentReview->payment);
                            $operation->setPaymentID($paymentVO->id);
                        }
                        break;
                    case 'conversion':
                            $paymentVO = $this->bankingManager->makePayment($paymentReview->payment);
                            $operation->setPaymentID($paymentVO->id);
                        break;
                    case 'reconversion':
                        break;
                    case 'deposit':
                            $paymentVO = $this->bankingManager->makePayment($paymentReview->payment);
                            $operation->setPaymentID($paymentVO->id);
                        break;
                    case 'withdrawal':
                            $paymentVO = $this->bankingManager->makePayment($paymentReview->payment);
                            $operation->setPaymentID($paymentVO->id);
                        break;
                    default:
                        $session->getFlashBag()->add('error','Undefined operation');
                        return $this->redirectToRoute('cairn_user_welcome');
                    }

                    $em->flush();

                    $session->getFlashBag()->add('success','Votre opération a été enregistrée.');
                    return $this->redirectToRoute('cairn_user_banking_operations',array('type'=>$type)); 
                }
                else{//cancel button clicked
                    $em->remove($operation);
                    $em->flush();
                    return $this->redirectToRoute('cairn_user_banking_operations',array('type'=>$type)); 
                }
            }
        }
        return $this->render('CairnUserBundle:Banking:operation_confirm.html.twig', array('form' => $form->createView(),'operationReview' => $paymentReview));

    }

    /**
     * Executes a failed occurrence with id $id 
     *
     *@param bigint $id ID of the failed occurrence
     *@throws Cyclos\ServiceException
     */
    public function executeOccurrenceAction(Request $request, $id)
    {
        $session = $request->getSession();
        $DTO = new \stdClass();
        $DTO->failureId = $id;

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('cancel')->isClicked()){
                return $this->redirectToRoute('cairn_user_banking_transactions_recurring_view_detailed',
                    array('id'=>$session->get('recurringID'))
                );
            }

            try{
                $occurrenceID = $this->bankingManager->processOccurrence($DTO);
                $session->getFlashBag()->add('success','Le virement a été effectué avec succès.');

            }catch(\Exception $e){
                if($e instanceof Cyclos\ServiceException){
                    if($e->errorCode == 'INSUFFICIENT_BALANCE'){
                        $message = 'Vous n\'avez pas les fonds nécessaires. Le virement ne peut aboutir';
                    }
                }
                else{
                    throw $e;
                }
                $session->getFlashBag()->add('error',$message);
            }
            return $this->redirectToRoute('cairn_user_banking_transactions_recurring_view_detailed',array('id'=>$session->get('recurringID')));
        }

        return $this->render('CairnUserBundle:Banking:execute_occurrence.html.twig', array(
            'form'   => $form->createView()
        ));


    }


    /**
     * Filters the operations by $type and $frequency
     *
     * @param string $type type of operation occurring : transaction|conversion|reconversion|deposit|withdrawal 
     */
    public function viewOperationsAction(Request $request, $type, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $frequency = $request->get('frequency');

        if(!$this->isValidFrequency($frequency)){
            return $this->redirectToRoute('cairn_user_banking_operations_view',array(
                'format'=>$_format,
                'type'=>$type,
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

        if($type == 'transaction'){
            $description = $this->getParameter('cairn_default_transaction_description');
            if($frequency == 'unique'){
                $accountNumbers = $accountService->getAccountNumbers($userVO->id);

                $ob = $operationRepo->createQueryBuilder('o');
                $processedTransactions = $ob->where($ob->expr()->in('o.fromAccountNumber', $accountNumbers))
                    ->andWhere('o.paymentID is not NULL')
                    ->andWhere('o.executionDate <= :date')
                    ->andWhere('o.type = :type')
                    ->setParameter('type', Operation::TYPE_TRANSACTION_EXECUTED)
                    ->setParameter('date',new \Datetime())
                    ->orderBy('o.executionDate','ASC')
                    ->getQuery()->getResult();
                //                $processedTransactions = $bankingService->getTransactions(
                //                    $userVO,$accountTypesVO,array('PAYMENT','SCHEDULED_PAYMENT'),array('PROCESSED',NULL,'CLOSED'),$description);
                //
                //                //instances of ScheduledPaymentInstallmentEntryVO (these are actually installments, not transactions yet)
                //the id used to execute an operation on this installment is from an instance of ScheduledPaymentEntryVO
                //                $futureInstallments = $bankingService->getInstallments($userVO,$accountTypesVO,array('BLOCKED','SCHEDULED'),$description);
                //                var_dump($futureInstallments);
                //                return new Response('ok');
                $ob = $operationRepo->createQueryBuilder('o');
                $futureInstallments = $ob->where($ob->expr()->in('o.fromAccountNumber', $accountNumbers))
                    ->andWhere('o.paymentID is not NULL')
                    ->andWhere($ob->expr()->in('o.type',array(Operation::TYPE_TRANSACTION_SCHEDULED,Operation::TYPE_SCHEDULED_FAILED)))
                    ->orderBy('o.executionDate','ASC')
                    ->getQuery()->getResult();

                return $this->render('CairnUserBundle:Banking:view_single_transactions.html.twig',
                    array('processedTransactions'=>$processedTransactions ,
                    'futureInstallments'=> $futureInstallments));

            }else{
                $processedTransactions = $bankingService->getRecurringTransactionsDataBy(
                    $userVO,$accountTypesVO,array('CLOSED','CANCELED'),$description);

                $ongoingTransactions = $bankingService->getRecurringTransactionsDataBy(
                    $userVO,$accountTypesVO,array('OPEN'),$description);

                if($_format == 'json'){
                    return $this->json(array(
                        'processedTransactions'=>$processedTransactions ,
                        'ongoingTransactions'=> $ongoingTransactions));
                }

                return $this->render('CairnUserBundle:Banking:view_recurring_transactions.html.twig', 
                    array('processedTransactions'=>$processedTransactions,'ongoingTransactions' => $ongoingTransactions));

            }
        }
        // elseif($type == 'reconversion'){ 
        //     $description = $this->getParameter('cairn_default_reconversion_description');
        //     $processedTransactions = $bankingService->getTransactions(
        //         $userVO,$accountTypesVO,array('PAYMENT'),array('PROCESSED',NULL,NULL),$description);

        //     if($_format == 'json'){
        //         return $this->json(array('processedTransactions'=>$processedTransactions));
        //     }
        //     return $this->render('CairnUserBundle:Banking:view_reconversions.html.twig',
        //         array('processedTransactions'=>$processedTransactions));

        // }elseif($type == 'conversion'){
        //     $description = $this->getParameter('cairn_default_conversion_description');
        //     $processedTransactions = $bankingService->getTransactions(
        //         $userVO,$debitAccount->type,array('PAYMENT'),array('PROCESSED',NULL,NULL),$description);

        //     //instances of ScheduledPaymentInstallmentEntryVO (these are actually installments, not transactions yet)
        //     $ongoingTransactions = array();

        //     if($_format == 'json'){
        //         return $this->json(array(
        //             'processedTransactions'=>$processedTransactions,
        //             'ongoingTransactions' => $ongoingTransactions));
        //     }
        //     return $this->render('CairnUserBundle:Banking:view_conversions.html.twig', array(
        //         'processedTransactions'=>$processedTransactions,
        //         'ongoingTransactions' => $ongoingTransactions));

        // }elseif($type == 'withdrawal'){
        //     $description = $this->getParameter('cairn_default_withdrawal_description');
        //     $processedTransactions = $bankingService->getTransactions($userVO,$debitAccount->type,array('PAYMENT'),array('PROCESSED',NULL,NULL),$description);

        //     if($_format == 'json'){
        //         return $this->json(array('processedTransactions'=>$processedTransactions));
        //     }
        //     return $this->render('CairnUserBundle:Banking:view_withdrawals.html.twig', 
        //         array('processedTransactions'=>$processedTransactions));

        // }elseif($type == 'deposit'){
        //     $description = $this->getParameter('cairn_default_deposit_description');
        //     $processedTransactions = $bankingService->getTransactions($userVO,$debitAccount->type,array('PAYMENT'),array('PROCESSED',NULL,NULL),$description);

        //     if($_format == 'json'){
        //         return $this->json(array('processedTransactions'=>$processedTransactions));
        //     }
        //     return $this->render('CairnUserBundle:Banking:view_deposits.html.twig', array('processedTransactions'=>$processedTransactions));
        else{
            return $this->redirectToRoute('cairn_user_welcome', array('_format'=>$_format));
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
        $session->set('recurringID',$id);

        //an instance of RecurringPaymentData contains an attribute occurrences which
        //contains instances of RecurringPaymentOccurrenceVO. Beware, although the documentation mentiones it, The transferDate 
        //attribute is not specified
        $recurringPaymentData = $this->get('cairn_user_cyclos_banking_info')->getRecurringTransactionDataByID($id);
        $transaction = $recurringPaymentData->transaction;
        $account = $this->get('cairn_user_cyclos_account_info')->getUserAccountWithType($transaction->fromOwner->id,$transaction->type->from->id);

        if($_format == 'json'){
            return $this->json(array('data'=>$recurringPaymentData,'fromAccount'=>$account));
        }
        return $this->render('CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig',array(
            'data'=>$recurringPaymentData,
            'fromAccount'=>$account));
    }

    /**
     * Get details of a specific transfer
     *
     * One shoud not be confused about typologies : transfer|transaction are different entities in Cyclos
     * The way to get a transfer depends if it is a ScheduledPayment or a Payment as the data available on Cyclos-side differ : 
     * either the transfer number is available or the transfer ID
     *These identifiers are accessible from TransactionEntryVO subclasses (paymentEntryVO, RecurringEntryVO,...) :
     *  _scheduled payment with status SCHEDULED : installment.id works
     *  _scheduled payment with status PROCESSED : id works
     *  _simple payment : transactionNumber works, id does not
     *  _recurring payment : occurrence.id works 
     *In the view, if the transfer involves two different people, the account type of the receiver is not mentioned
     *The attribute "date" has not the same meaning for all types :
     *  _scheduled payment with status SCHEDULED : date is submission date
     *  _scheduled payment with status PROCESSED : date is execution date
     *  _simple payment :                          date is execution date
     *  _recurring payment :                       date is execution date

     *@param string $type Type of transaction the transfer belongs to
     *@param id $id Identifier of the transfer : either it is the cyclos identifier or the cyclos transfer number
     */
    public function viewTransferAction(Request $request, $type,Operation $operation, $_format)
    {
        $bankingService = $this->get('cairn_user_cyclos_banking_info');
        $session = $request->getSession();

        switch ($type){
        case 'scheduled.past':
            $data = $bankingService->getTransactionDataByID($id);
            $transfer = $bankingService->getTransferByID($data->transaction->installments[0]->transferId);
            $transfer->dueDate = $data->transaction->installments[0]->dueDate;
            break;
        case 'scheduled.futur':
            //the transfer is not done yet, so there is no real object "transferVO" related to the provided id, but all information can 
            //be gathered and put in an object. The fact that there is no id is the indicator(in the view) to know that it's not a real 
            //transfer 
            $data = $bankingService->getInstallmentData($id);
            $transaction = $data->transaction;

            $debitorAccounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($transaction->fromOwner->id);
            //            $creditorAccounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($transaction->toOwner->id);

            foreach($debitorAccounts as $account){
                if($account->type->id == $transaction->type->from->id){
                    $fromAccount = $account;
                }
            }
            //            foreach($creditorAccounts as $account){
            //                if($account->type->id == $transaction->type->to->id){
            //                    $toAccount = $account;
            //                }
            //            }

            //            var_dump($transaction);
            //            return new Response('ok');
            $transfer = new \stdClass();
            $transfer->from = $fromAccount; 
            $transfer->to = new \stdClass();
            $transfer->to->owner = $transaction->toOwner;
            $transfer->description = $transaction->description;
            $transfer->status = $transaction->installments[0]->status;
            $transfer->date = $transaction->date;
            $transfer->dueDate = $transaction->installments[0]->dueDate;
            $transfer->currencyAmount = $transaction->dueAmount;

            break;
            #        case 'recurring':
            #            $transfer = $bankingService->getTransferByID($id);
            #
            #            $transfer->dueDate = $transfer->date;
            #            break;
        case 'simple':
            $transfer = $operation;
            //            $transfer = $bankingService->getTransferByTransactionNumber($id);
            //            $transfer->dueDate = $transfer->date;

            break;
        default:
            return $this->redirectToRoute('cairn_user_banking_operations_view',array(
                '_format'=>$_format, 'type'=>'transaction','frequency'=>$session->get('frequency')));
        }

        if($transfer){
            if($_format == 'json'){
                return $this->json(array('transfer'=>$transfer));
            }
            return $this->render('CairnUserBundle:Banking:transfer_view.html.twig',array(
                'transfer'=>$transfer));
        }else{
            $session->getFlashBag()->add('error','Impossible de trouver le transfert recherché');
            return $this->redirectToRoute('cairn_user_banking_operations_view',array(
                '_format'=>$_format, 
                'type'=>'transaction',
                'frequency'=>$session->get('frequency')));
        }
    }


    /**
     * Either blocks, unblocks or cancels a scheduled transaction
     *
     * @param int $id ID of the scheduled transaction
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
                if($status == 'execute'){
                    $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);
                    $operation->setExecutionDate(new \Datetime());
                }elseif($status == 'cancel'){
                    $em->remove($operation);
                }

                $em->flush();
                $session->getFlashBag()->add('success','Le statut de votre virement a été modifié avec succès.');
            }

            return $this->redirectToRoute('cairn_user_banking_operations_view',array('frequency'=>'unique','type'=>'transaction'));
        }

        return $this->render('CairnUserBundle:Banking:scheduled_'.$status.'_confirm.html.twig',array('form'=>$form->createView()));

    }

    /**
     * Cancels the recurring transaction with ID $id
     *
     * @param int $id ID of the recurring transaction
     */
    public function cancelRecurringTransactionAction(Request $request, $id)
    {
        $session = $request->getSession();

        //        $user = $this->getUser();
        //        $userVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        //
        //        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($userVO->id);
        //        $accountTypesVO = array();
        //
        //        foreach($accounts as $account){
        //            $accountTypesVO[] = $account->type;
        //        } 

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){
                $recurringPaymentData = $this->get('cairn_user_cyclos_banking_info')->getRecurringTransactionDataByID($id);
                $recurringPaymentDTO = new \stdClass();
                $recurringPaymentDTO->recurringPayment = $recurringPaymentData->transaction;

                $this->bankingManager->cancelRecurringPayment($recurringPaymentDTO);

                $session->getFlashBag()->add('success','Le virement permanent a été annulé avec succès');
            }

            return $this->redirectToRoute('cairn_user_banking_operations_view',array('frequency'=>'recurring','type'=>'transaction'));
        }

        return $this->render('CairnUserBundle:Banking:recurring_cancel_confirm.html.twig', array(
            'form'   => $form->createView()
        ));

    }


    /**
     * Downloads a PDF document relating the operation notice with ID $id
     *
     * @param int $id transfer ID
     *
     */
    public function downloadTransferNoticeAction(Request $request, Operation $operation)
    {
        $session = $request->getSession();
        $bankingService = $this->get('cairn_user_cyclos_banking_info');


        $accountNumbers = $this->get('cairn_user_cyclos_account_info')->getAccountNumbers($this->getUser()->getCyclosID());
        if(! in_array($operation->getFromAccountNumber(), $accountNumbers)){
            throw new AccessDeniedException('Vous n\'avez pas accès à cette information');
        }

        $html = $this->renderView('CairnUserBundle:Pdf:operation_notice.html.twig',array(
            'transfer'=>$operation));

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
     * @throws AccessDeniedException Current user is not a ROLE_PRO or a ROLE_SUPER_ADMIN 
     */
    public function downloadAccountsOverviewAction(Request $request)
    {
        $session = $request->getSession();
        $accountService = $this->get('cairn_user_cyclos_account_info');

        $currentUser = $this->getUser();
        if(! ($currentUser->hasRole('ROLE_PRO') || $currentUser->hasRole('ROLE_SUPER_ADMIN')) ){
            throw new AccessDeniedException('Vous ne pouvez pas télécharger le relevé de compte d\'un professionnel.');
        }

        $accountTypesVO = array();

        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);

        $accounts = $accountService->getAccountsSummary($ownerVO->id);

        $form = $this->createFormBuilder()
            ->add('format',ChoiceType::class,array(
                'label'=>'Format du fichier',
                'choices'=>array('CSV'=>'csv','PDF'=>'pdf'),
                'expanded'=>true))
                ->add('accounts',ChoiceType::class,array(
                    'label'=>'Comptes',
                    'choices'=>$accounts,
                    'choice_label'=>'type.name',
                    'multiple'=>true,
                    'expanded'=>true))
                    ->add('begin', DateType::class,array(
                        'label'=>'depuis',
                        'data'=> date_modify(new \Datetime(),'-1 months'),
                        'required'=>false))
                        ->add('end', DateType::class,array(
                            'label'=>'jusqu\'à',
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
                $accounts = ($dataForm['accounts'] != NULL) ? $dataForm['accounts'] : $accounts;
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
                            fputcsv($handle,array('RIB Cairn : ' . $account->number),';');
                            fputcsv($handle,array('Solde initial : ' . $history->status->balanceAtBegin),';');

                            fputcsv($handle, array('Date', 'Description', 'Débit', 'Crédit','Solde'),';');
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
                                fputcsv(
                                    $handle, // The file pointer
                                    array($transaction->date, 
                                    $transaction->description, 
                                    $debit, 
                                    $credit,
                                    $balance), // The fields
';' // T    he delimiter
                      );

                            }
                            fputcsv($handle,array('Solde au : ' . $period['end'] . $history->status->balanceAtEnd),';');
                            fputcsv($handle,array());
                        }
                        fclose($handle);
                    });

                    //          $response->setStatusCode(200);
                    $response->headers->set('Content-Type', 'application/force-download');
                    $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

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

}
