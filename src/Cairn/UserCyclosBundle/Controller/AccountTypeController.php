<?php
// src/Cairn/UserCyclosBundle/Controller/AccountTypeController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\AccountTypeManager;
use Cairn\UserCyclosBundle\Entity\TransferTypeManager;
use Cairn\UserCyclosBundle\Entity\ProductManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Cairn\UserBundle\Form\ConfirmationType;
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\FormType;                       
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;                                          
use Symfony\Component\Form\FormEvents;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Simple CRUD related to Account Types on cyclos side
 * @Security("has_role('ROLE_SUPER_ADMIN')") 
 *
 */
class AccountTypeController extends Controller
{   
    /**
     * Deals with all account type actions to operate 
     *@var AccountTypeManager $accountTypeManager
     */
    private $accountTypeManager;

    /**
     * Deals with all product actions to operate 
     *@var ProductManager $productManager
     */
    private $productManager;

    /**
     * Deals with all transfer type actions to operate 
     *@var TransferTypeManager $transferTypeManager
     */
    private $transferTypeManager;

    public function __construct()
    {
        $this->accountTypeManager = new AccountTypeManager();
        $this->productManager     = new ProductManager();
        $this->transferTypeManager = new TransferTypeManager();
    }

    /**
     * For now, redirects to the network defined as a global parameter
     *@todo : possibility to change the network : but this is a whole other story
     */
    public function indexAction(Request $request, $_format)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = new Session();

        //        $listCurrencies = $this->get('cairn_user_cyclos_currency_info')->getListCurrencies();
        //        $form = $this->createFormBuilder()
        //            ->add('currency' , ChoiceType::class , array('label'         => 'Quelle monnaie',
        //                'choice_label' => 'name',
        //                'required'      => true,
        //                'choices'       => $listCurrencies)) 
        //                ->add('save'          , SubmitType::class)                              
        //                ->getForm()
        //                ;       
        //        if($request->isMethod('POST')){ //form filled and submitted
        //
        //            $form->handleRequest($request);    
        //            if($form->isValid()){
        //                $currency = $form->get('currency')->getData();
        //                $session->set('currency',$currency);
        return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_list', array('_format'=>$_format,'currency' => $this->getParameter('cyclos_currency_cairn')));

        //            }
        //        }
        //        return $this->render('CairnUserCyclosBundle:Config/AccountType:index.html.twig',array('form' => $form->createView()));


    }

    /**
     * List all account types, whether it is SYSTEM or USER, associated to the currency defined as a global parameter
     *
     */
    public function listAccountTypesAction(Request $request, $_format)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->getParameter('cyclos_network_cairn'));

        $currency = $this->get('cairn_user_cyclos_currency_info')->getCurrencyVO($this->getParameter('cyclos_currency_cairn'));

        $listUserAccountTypes = $this->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes($currency,'USER');
        $listSystemAccountTypes = $this->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes($currency,'SYSTEM');

        if($_format == 'json'){
            return $this->json(array(
                'userAccountTypes' => $listUserAccountTypes, 
                'systemAccountTypes' => $listSystemAccountTypes));
        }
        return $this->render('CairnUserCyclosBundle:Config/AccountType:list.html.twig',array(
            'userAccountTypes' => $listUserAccountTypes, 
            'systemAccountTypes' => $listSystemAccountTypes));
    }

    /**
     * View details about the account type with ID $id
     *
     * This action provides details about a given AccountType :
     *  _status : open | closed
     *  _credit default limit
     *  _the transfer types so that the debtor account is the given account type 
     *
     * @param int $id Account Type ID
     */
    public function viewAccountTypeAction(Request $request, $id, $_format)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();
        $mailer = $this->get('mailer');
        $accountTypeDTO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeDTOByID($id);

        if($accountTypeDTO->nature == 'USER'){
            $productVO = $this->get('cairn_user_cyclos_product_info')->getProductVOByName($accountTypeDTO->name);
            $groupVO = $this->get('cairn_user_cyclos_group_info')->getGroupVO($this->getParameter('cyclos_group_pros'),'MEMBER_GROUP');

            $isAssigned = $this->get('cairn_user_cyclos_accounttype_info')->groupHasAssignedProduct($accountTypeDTO->id,$groupVO->id);

            //get product DTO with the same name(much more data)
            $productDTO = $this->get('cairn_user_cyclos_product_info')->getAccountProductDTO($accountTypeDTO);
            //            foreach($productDTO->userPayments as $transferTypeVO){
            //                $transferFees = $this->get('cairn_user_cyclos_transferfee_info')->getListTransferFeesVO($transferTypeVO);
            //                //only one transferFee per transfertype : configuration rule
            //                //WARNING : TransferTypeVO does not have any attribute TransferFeeVO (documentation), but we add it here to be able to manage it from the view
            //                $transferTypeVO->transferFee = ($transferFees) ? $transferFees[0] : NULL ;
            //                $transferFeeDTO = $this->get('cairn_user_cyclos_transferfee_info')->getTransferFeeDTOByID($transferFees[0]->id);
            //
            //            }
        }else{
            $adminProductVO = $this->get('cairn_user_cyclos_product_info')->getProductVOByName($this->getParameter('cyclos_group_network_admins'));
            $isAssigned = true;//always for admin products
            $productDTO = $this->get('cairn_user_cyclos_product_info')->getProductDTOByID($adminProductVO->id);

        }
        $session->set('productID', $productDTO->id);
        $session->set('accountTypeID', $accountTypeDTO->id);

        if($_format == 'json'){
            return $this->json(array(
                'accountType' => $accountTypeDTO,'product' => $productDTO,'isAssigned'=>$isAssigned));
        }
        return $this->render('CairnUserCyclosBundle:Config/AccountType:view.html.twig', array(
            'accountType' => $accountTypeDTO,'product' => $productDTO,'isAssigned'=>$isAssigned));
    }


    /**
     * Generates a new transfer type object in Cyclos Database
     *
     * By default, we set the transfer types so that they allow recurring and scheduled payments. 
     * The internal name must contain no space and little to no special characters. To make sure this won't happen, the name and internal
     * name depend directly on account type names and internal names respectively, and we force these internal names not to contain 
     * special characters using regex
     *
     *@param string $nature PAYMENT | GENERATED
     *@param stdClass $fromAccountType representing Java type: org.cyclos.model.banking.accounttypes.AccountTypeVO
     *@param stdClass $toAccountType representing Java type: org.cyclos.model.banking.accounttypes.AccountTypeVO
     *@param string $direction USER_TO_USER | USER_TO_SELF | USER_TO_SYSTEM | SYSTEM_TO_USER
     *@param array $channels channels from which this transfertype can be used : mainWeb | webServices | mobileApp
     *
     *@return stdClass representing Java type: org.cyclos.model.banking.transfertypes.TransferTypeVO 
     */
    public function generateTransferType($nature,$fromAccountType,$toAccountType,$direction,$channels)
    {
        $dataParams = new \stdClass();
        $dataParams->fromAccountType = $fromAccountType;
        $dataParams->nature = $nature;
        $data = $this->transferTypeManager->dataForNew($dataParams); 
        $transferTypeDTO = $data->dto;

        $transferTypeDTO->to = $toAccountType;
        $transferTypeDTO->enabled = true;
        $transferTypeDTO->direction = $direction;
        $transferTypeDTO->name = $direction . ' to ' . $toAccountType->name;

        $transferTypeDTO->internalName = $direction . ' to ' . $toAccountType->internalName;
        $transferTypeDTO->internalName = preg_replace('/\s/', '', $transferTypeDTO->internalName); 

        $transferTypeDTO->allowsRecurringPayments = true;
        $transferTypeDTO->allowsScheduledPayments = true;
        $transferTypeDTO->maxInstallments = 12;

        foreach($channels as $channelVO){
            $transferTypeDTO->channels[] = $channelVO;
        }

        $transferTypeID = $this->transferTypeManager->editTransferType($transferTypeDTO);
        $transferTypeVO = $this->get('cairn_user_cyclos_transfertype_info')->getTransferTypeVOByID($fromAccountType,$toAccountType,$direction,$transferTypeID);

        return $transferTypeVO;
    }

    /**
     * Adds a USER account type and assigns it to the group of users defined in global parameter
     * 
     * This action is probably the most complicated while managing cyclos configuration from web services
     * Cyclos does not provide any way to get a new Account Type object so that we just fill the missing properties.
     * To get around this issue, we follow the steps below :
     *  _ 1)retrieve an instance of AccountTypeDTO and its corresponding productDTO
     *  _ 2) unset its attribute id (doing this will make it a new AccountType object)
     *  _ 3) if no account is using the current currency, retrieve AccountTypeDTO using another currency, then change its property currency
     *  _ 4)unset associated productDTO id (doing this will make it a new Product object)
     * 
     * Then, once the new objects $newAccountType and $newProduct have been created, we must ensure that the names won't provok 
     * confusion in Cyclos.
     * To do so, we force an AccountType name not be contained or contain another AccountType name
     *
     * Then, we must generate the TransferType objects involving $newAccountType. This means generating all transfertypes from/to 
     * $newAccountType from/to all other existing account types :
     *  _for transfertypes going from a system accounttype to $newAccountType : direction is USER_TO_SYSTEM
     *  _for transfertypes going from $newAccountType to a system Account type : direction is SYSTEM_TO_USER
     *  _for transfertypes going from/to $newAccountType from/to another user accounttype, two transfertypes must be created :
     *      . with direction USER_TO_USER
     *      . with direction USER_TO_SELF
     * 
     * Finally, the transfertypes must be added to the products. Products involved : 
     *  _$newProduct which will contain all transfertypes from $newAccountType
     *  _$adminProduct will contain all transfertypes from system account types to $newAccountType
     *  _$existingProduct( associated to an existing Account Type) will contain transfertypes from $existingAccount to $newAccountType
     *
     * In the end, $newProduct must be assigned to the group of users defined as a global parameter
     *@param string $nature USER|SYSTEM 
     */
    public function addAccountTypeAction(Request $request,  $nature)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();
        $messageNotificator = $this->get('cairn_user.message_notificator');

        if($nature != 'USER'){
            $session->getFlashBag()->add('error','Seul un compte utilisateur peut être créé.');
            return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_list');
        }
        $channelVO = $this->get('cairn_user_cyclos_channel_info')->getChannelVO('webServices');
        $groupVO = $this->get('cairn_user_cyclos_group_info')->getGroupVO($this->getParameter('cyclos_group_pros'),'MEMBER_GROUP');
        $currencyVO = $this->get('cairn_user_cyclos_currency_info')->getCurrencyVO($this->getParameter('cyclos_currency_cairn'));
        $usersVO = $this->get('cairn_user_cyclos_user_info')->getListInGroup($groupVO);

        //1)
        $accountTypeDTO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeDTO(NULL,$currencyVO,$nature);

        //2)no account type associated with the given currency
        if($accountTypeDTO == NULL){
            $accountTypeDTO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeDTO(NULL,NULL,$nature);
            //            $listCurrencies = $this->get('cairn_user_cyclos_currency_info')->getListCurrencies(); 
            //            foreach($listCurrencies as $currency){
            //                if($currencyVO->name == $currency){
            $accountTypeDTO->currency = $currencyVO;
            //                }
            //            }
        }

        if($nature == 'USER'){ //BEFORE unsetting ids: retrieve associated instance of ProductDTO
            //'SYSTEM' account types don't have associated Product
            $productDTO = $this->get('cairn_user_cyclos_product_info')->getAccountProductDTO($accountTypeDTO);
        }
        //3) unset ids
        $accountTypeDTO->description = NULL;
        unset($accountTypeDTO->id);
        unset($productDTO->id);
        $profileFields = $productDTO->myProfileFields;
        foreach($profileFields as $field){
            if(property_exists($field,'id')){
                unset($field->id);
            }
        }

        $accountTypeDTO->name         = NULL;
        $productDTO->name = NULL;
        $productDTO->systemPayments = array();
        $productDTO->userPayments = array();
        $productDTO->selfPayments = array();
        $accountTypeArray = json_decode(json_encode($accountTypeDTO), true);

        //        $allPros = new \stdClass();
        //        $allPros->shortDisplay = 'ALL';
        //        $allPros->display = 'Tous les professionnels';
        $form = $this->createFormBuilder($accountTypeArray)
            ->add('name'     , TextType::class , array('label' => 'nom du nouveau compte'))
            //            ->add('limitType'     , ChoiceType::class , array('choices' => array('limité' => 'LIMITED' ,'illimité' => 'UNLIMITED')))
            ->add('creditLimit'   , TextType::class , array('label' => 'débit maximal du compte',
                'data'=>'0'))                                
                ->add('description'   , TextType::class , array('label' => 'Description'))                                
                //            ->add('to'            , ChoiceType::class , array('label'=>'Assigné à',
                //                                                              'choices' => array($allPros,$usersVO),
                //                                                              'choice_label'=>'display',
                //                                                              'multiple' => true,
                //                                                              'expanded' => true ))
                ->add('save'     , SubmitType::class)                              
                ->getForm()
                ;       


        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();

                if(!$this->get('cairn_user_cyclos_accounttype_info')->hasAvailableName($dataForm['name'])){
                    $session->getFlashBag()->add('error','Le nom du compte est déjà utilisé, ou prête à confusion. Choisissez un nom bien distinct des autres types de comptes déjà créés');
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_add',array('nature'=>$nature));
                }

                //create the accountType and its associated product
                $accountTypeDTO = (object) $dataForm;
                $accountTypeDTO->internalName = preg_replace('#[^a-zA-Z0-9]#', '', $accountTypeDTO->name); 

                $productDTO->name = $accountTypeDTO->name;
                $productDTO->internalName = $accountTypeDTO->internalName;

                $accountTypeID = $this->accountTypeManager->editAccountType($accountTypeDTO);

                $createdAccountTypeDTO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeDTOByID($accountTypeID);
                $createdAccountTypeVO =  $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeVO($createdAccountTypeDTO);
                $productDTO->userAccount = $createdAccountTypeVO;
                $productDTO->defaultCreditLimit = $dataForm['creditLimit'];
                $productDTO->description = $dataForm['description'];

                //once the accountType is created, create transfer types from/to  all accountTypes and fill Admin/Member ProductDTO

                //first, retrieve all system account types and user account types
                $userAccountTypes = $this->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes($currencyVO,'USER');
                $systemAccountTypes = $this->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes($currencyVO,'SYSTEM');

                $adminProductVO = $this->get('cairn_user_cyclos_product_info')->getProductVOByName($this->getParameter('cyclos_group_network_admins'));
                $adminProductDTO = $this->get('cairn_user_cyclos_product_info')->getProductDTOByID($adminProductVO->id);

                //generation of transfer types
                foreach($userAccountTypes as $userAccountTypeVO){
                    //from new created account to all user accounts
                    $transferTypeVO = $this->generateTransferType('PAYMENT',$createdAccountTypeVO,$userAccountTypeVO,'USER_TO_USER',array($channelVO));
                    $productDTO->userPayments[] = $transferTypeVO;
                    $adminProductDTO->userPaymentsAsUser[] = $transferTypeVO;

                    if($userAccountTypeVO->id != $createdAccountTypeVO->id){
                        $existingProductDTO = $this->get('cairn_user_cyclos_product_info')->getAccountProductDTO($userAccountTypeVO);

                        //add self account transactions (accounts must be differents of course)
                        $transferTypeVO = $this->generateTransferType('PAYMENT',$createdAccountTypeVO,$userAccountTypeVO,'USER_TO_SELF',array($channelVO));
                        $productDTO->selfPayments[] = $transferTypeVO;
                        $adminProductDTO->selfPaymentsAsUser[] = $transferTypeVO;

                        //from all user accounts to new created account
                        $transferTypeVO = $this->generateTransferType('PAYMENT',$userAccountTypeVO,$createdAccountTypeVO,'USER_TO_USER',array($channelVO));
                        $existingProductDTO->userPayments[] = $transferTypeVO;
                        $adminProductDTO->userPaymentsAsUser[] = $transferTypeVO;

                        //add self account transactions
                        $transferTypeVO = $this->generateTransferType('PAYMENT',$userAccountTypeVO,$createdAccountTypeVO,'USER_TO_SELF',array($channelVO));
                        $productDTO->selfPayments[] = $transferTypeVO;
                        $adminProductDTO->selfPaymentsAsUser[] = $transferTypeVO;

                    }
                }

                foreach($systemAccountTypes as $systemAccountTypeVO){
                    //from all user accounts to new created account
                    $transferTypeVO = $this->generateTransferType('PAYMENT',$systemAccountTypeVO,$createdAccountTypeVO,'SYSTEM_TO_USER',array($channelVO));
                    $adminProductDTO->systemToUserPayments[] = $transferTypeVO;

                    if($systemAccountTypeVO->name != $createdAccountTypeVO->name){
                        //from new created account to all user accounts
                        $transferTypeVO = $this->generateTransferType('PAYMENT',$createdAccountTypeVO,$systemAccountTypeVO,'USER_TO_SYSTEM',array($channelVO));
                        $productDTO->systemPayments[] = $transferTypeVO;
                        $adminProductDTO->systemPaymentsAsUser[] = $transferTypeVO;
                    }
                }

                //at this point, all transfer types have been created
                $productID = $this->productManager->editProduct($productDTO);
                $this->productManager->editProduct($adminProductDTO);

                //once the accounttype and its product have been created, assign the product
                //need to retrieve instance of ProductVO associated with the new ProductDTO : can't pass by getAccountProductVO
                //because the product is not associated with the the AccountType yet(this is what we want to do here)
                //to do so, we apply a rule to follow : the name of the product is the name of its accounttype. 
                //Then we search the productVO by its name (not internal name because default ADMIN Products have only a name)
                //this must be changed in initial configuration or search here by name instead
                $productVO = $this->get('cairn_user_cyclos_product_info')->getProductVOByName($createdAccountTypeDTO->name);
                $this->productManager->assignToGroup($productVO,$groupVO->id);

                //                if($dataForm['to'][0]->shortDisplay == 'ALL'){
                //                        $this->productManager->assignToGroup($productVO,$groupVO->id);
                //                }
                //                else{
                //                    $this->productManager->assignToUsers($productVO,$dataForm['to']);
                //
                //                }

                $subject = 'Création de compte Cairn';
                $from = $messageNotificator->getNoReplyEmail();
                $body = $this->renderView('CairnUserBundle:Emails:account_creation.html.twig',array('account'=>$accountTypeDTO));
                $messageNotificator->notifyRolesByEmail(array('ROLE_PRO'),$subject,$from,$body);

                $session->getFlashBag()->add('success','Le type de compte '. $accountTypeDTO->name . ' a été créé avec succès.');
                return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id'=>$accountTypeID));
            }
        }

        return $this->render('CairnUserCyclosBundle:Config/AccountType:add.html.twig', array('form' => $form->createView()));
    }

    /*
     * Edits an account type with ID $id
     *
     * This action allows to edit an Account Type. If the account is of type SYSTEM : it can't be the debit Account(with unlimited 
     * balance)
     *
     * @param int $id Cyclos ID of tha account type  
     */
    public function editAccountTypeAction(Request $request, $id)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();
        $accountTypeDTO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeDTOByID($id);

        $debitAccount = $this->get('cairn_user_cyclos_account_info')->getDebitAccount();
        if($debitAccount->type->id == $id){
            $session->getFlashBag()->add('error','Ce compte ne peut être modifié.');
            return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id'=>$id));
        }

        $accountTypeArray = json_decode(json_encode($accountTypeDTO), true);

        if($accountTypeArray == NULL){
            $session->getFlashBag()->add('error','Aucun compte n\'est associé à la recherche');
            return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_list');
        }

        $builder = $this->createFormBuilder($accountTypeArray)
            ->add('name'          , TextType::class , array('label' => 'nom du compte','disabled'=>true));                                
        $builder->addEventListener(                                            
            FormEvents::PRE_SET_DATA,                                          
            function (FormEvent $event) use ($accountTypeArray) {                          
                $form = $event->getForm();                                     
                if($accountTypeArray['nature'] == 'SYSTEM'){
                    $form->add('limitType', ChoiceType::class , array('choices' => array('limité' => 'LIMITED' ,'illimité' => 'UNLIMITED')));
                }
            }                                                                  
        );  

        $builder->add('creditLimit'   , TextType::class , array('label' => 'débit maximal du compte'));
        $builder->add('save'          , SubmitType::class); 

        $form = $builder->getForm();
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();

                // If USER, modify the associated product
                if($dataForm['nature'] == 'USER'){ 
                    $productDTO = $this->get('cairn_user_cyclos_product_info')->getAccountProductDTO($accountTypeDTO);
                    $productDTO->defaultCreditLimit = $dataForm['creditLimit'];
                    $productID = $this->productManager->editProduct($productDTO);
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id' => $id));
                }
                else{//if SYSTEM, modify accountType directly.
                    $accountTypeDTO = (object) $dataForm;
                    //                    $accountTypeDTO->internalName = preg_replace('/\s\s+/', '', $accountTypeDTO->name); 

                    $accountTypeID = $this->accountTypeManager->editAccountType($accountTypeDTO);
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id' => $accountTypeID));
                }


            }
        }

        return $this->render('CairnUserCyclosBundle:Config/AccountType:edit.html.twig', array('form' => $form->createView(),'accountType'=>$accountTypeDTO));

    }


    /**
     * Assigns/ Unassigns the Product associated with account type of ID $id with the group of users defined as global parameter
     *
     * @param string $assign 
     * @param int $id
     * @return boolean
     * @throws Cyclos\ServiceException
     */
    public function changeAssignationAccountType($id,$assign)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        //for now, only this group is concerned
        $groupVO = $this->get('cairn_user_cyclos_group_info')->getGroupVO($this->getParameter('cyclos_group_pros'),'MEMBER_GROUP');
        $accountTypeDTO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeDTOByID($id);

        $productDTO = $this->get('cairn_user_cyclos_product_info')->getAccountProductDTO($accountTypeDTO);

        //can't use productDTO to get corresponding productVO : id don't match
        $productVO = $this->get('cairn_user_cyclos_product_info')->getAccountProductVO($accountTypeDTO);

        if(!$assign){
            $assignationChanged = $this->productManager->unassignToGroup($productVO,$groupVO->id);
        }
        else{
            $assignationChanged = $this->productManager->assignToGroup($productVO,$groupVO->id);
        }
        return $assignationChanged;
    }


    /**
     *
     * Assigns the Product associated with account type of ID $id with the group of users defined as global parameter
     *
     */
    public function assignAccountTypeAction(Request $request, $id)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();

        $accountTypeDTO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeDTOByID($id);

        $form = $this->createForm(ConfirmationType::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                if($form->get('save')->isClicked()){
                    $changeAssignation = $this->changeAssignationAccountType($id,true);
                    $session->getFlashBag()->add('info','Un compte a été assigné à chaque professionnel.');
                }
                return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id'=>$id));
            }
        }

        return $this->render('CairnUserCyclosBundle:Config/AccountType:confirm_open.html.twig',array('form'=>$form->createView(),'accountType'=>$accountTypeDTO));

    }



    /**
     * Removes account type with ID $id
     *
     * It means that all accounts with this type will be unavailable. If a single transaction has occurred involving an account with this
     * type, removing the Account Type will be impossible. In this case, the associated product will be unassigned to its group of users,
     * so that the account won't be seen anymore, even if it is still available in database.  
     *
     * @param id $id ID of the Account Type to remove
     *
     */
    public function removeAccountTypeAction(Request $request, $id)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');
        $messageNotificator = $this->get('cairn_user.message_notificator');

        $accountTypeDTO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeDTOByID($id);

        //if debit account, refuse
        $debitAccount = $this->get('cairn_user_cyclos_account_info')->getDebitAccount();
        if($debitAccount->type->id == $id){
            $session->getFlashBag()->add('error','Ce compte ne peut être supprimé.');
            return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id'=>$id));
        }

        //if system account type : deny
        if($accountTypeDTO->nature == 'SYSTEM'){
            $session->getFlashBag()->add('error','Un compte système ne peut être supprimé.');
            return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id'=>$id));
        }

        //if only one user account type, refuse
        $currency = $this->get('cairn_user_cyclos_currency_info')->getCurrencyVO($this->getParameter('cyclos_currency_cairn'));
        $listUserAccountTypes = $this->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes($currency,'USER');
        if(count($listUserAccountTypes) == 1){
            $session->getFlashBag()->add('error','Compte unique côté utilisateur. Il ne peut être supprimé.');
            return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id'=>$id));
        }

        //check if all null balances for the given account type
        $groupVO = $this->get('cairn_user_cyclos_group_info')->getGroupVO($this->getParameter('cyclos_group_pros'),'MEMBER_GROUP');
        $usersVO =  $this->get('cairn_user_cyclos_user_info')->getListInGroup($groupVO);

        $accountIds = array();

        $nullAccounts = true;
        foreach($usersVO as $user){
            $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($user->id);
            foreach($accounts as $account){
                if($account->type->id == $id){
                    $accountIds[] = intval($account->id);
                    if($account->status->balance != 0){
                        $nullAccounts = false;
                    }
                }
            }
        }   

        //if non null balances : refuse
        if($nullAccounts == false){
            $session->getFlashBag()->add('error','Certains comptes ont un solde non nul, le compte ne peut être fermé. Un email a été envoyé à tous les professionnels pour les avertir.');

            $subject = 'Suppression de compte';
            $from = $messageNotificator->getNoReplyEmail();
            $body = $this->renderView('CairnUserBundle:Emails:account_alert_removal.html.twig',array('account'=>$accountTypeDTO));
            $messageNotificator->notifyRolesByEmail(array('ROLE_PRO'),$subject,$from,$body);

            return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id'=>$id));

        }

        $form = $this->createForm(ConfirmationType::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                if($form->get('save')->isClicked()){

                    //unassign product to group Pros
                    //cyclos returns ILLEGAL_ACTION if trying to unassign an unassigned product
                    $changeAssignation = true;
                    if($this->get('cairn_user_cyclos_accounttype_info')->groupHasAssignedProduct($id,$groupVO)){ 
                        $changeAssignation = $this->changeAssignationAccountType($id,false);
                    }

                    $processedTransactions = $this->get('cairn_user_cyclos_banking_info')->getTransactions(NULL,$id,NULL,NULL,NULL);

                    if(count($processedTransactions) != 0){
                        $session->getFlashBag()->add('info','Des transactions impliquant ce compte ont déjà été réalisées. Le compte ne peut donc être supprimé. En revanche, il sera désormais invisible des professionnels.');

                    }else{//remove product then remove account type
                        $productDTO = $this->get('cairn_user_cyclos_product_info')->getAccountProductDTO($accountTypeDTO);
                        //Awful error to avoid ABSOLUTELY : While removing a system Account, removing the AdminProductDTO
                        //which allows the administator to manage all actions made in Cyclos through web Services
                        if($productDTO->nature == 'MEMBER'){
                            $this->productManager->removeProduct($productDTO->id);
                        }
                        $this->accountTypeManager->removeAccountType($id);
                        $session->getFlashBag()->add('info','Le compte a été supprimé avec succès.');

                    }


                    $bb = $beneficiaryRepo->createQueryBuilder('b');
                    $bb->where($bb->expr()->in('b.ICC','?1'))
                        ->setParameter(1,$accountIds);

                    $beneficiaries = $bb->getQuery()->getResult();
                    foreach($beneficiaries as $beneficiary){
                        $em->remove($beneficiary);
                    }
                    $em->flush();

                    $subject = 'Suppression de compte';
                    $from = $messageNotificator->getNoReplyEmail();
                    $body = $this->renderView('CairnUserBundle:Emails:account_removal.html.twig',array('account'=>$accountTypeDTO));
                    $messageNotificator->notifyRolesByEmail(array('ROLE_PRO'),$subject,$from,$body);

                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_list');
                }else{
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_accounttype_view',array('id'=>$id));
                }
            }

        }
        return $this->render('CairnUserCyclosBundle:Config/AccountType:confirm_remove.html.twig',array(
            'form'=>$form->createView(),'accountType'=>$accountTypeDTO));

    }
}
