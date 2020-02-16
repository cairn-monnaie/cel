<?php
// src/Cairn/UserBundle/Controller/AdminController.php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Deposit;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\ApiClient;

use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\BankingManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Cairn\UserBundle\Form\ProfileType;
use Cairn\UserBundle\Form\ApiClientType;
use Cairn\UserBundle\Form\AddIdentityDocumentType;
use Cairn\UserBundle\Form\ConfirmationType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cyclos;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

//Events
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;

/**
 * This class contains actions related to user, cards or accounts management by administrators
 *
 * Adminisatrators can have either a role ROLE_ADMIN (resp. ROLE_SUPER_ADMIN) depending on the level of restrictions and rights
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * Deals with all user management actions to operate on Cyclos-side
     *@var UserManager $userManager
     */
    private $userManager;                                              


    public function __construct()
    {                                
        $this->userManager = new UserManager();
        $this->bankingManager = new BankingManager();

    }   

    /**
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function editProfileAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $form = $this->createForm(ProfileType::class, $user);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()) {

                $event = new FormEvent($form, $request);                           
                $this->get('event_dispatcher')->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                if($form->get('initialize_parameters')->getData()){
                    $user->setNbPhoneNumberRequests(0);
                    $user->setPhoneNumberActivationTries(0);
                    $user->setPasswordTries(0);
                    $user->setCardKeyTries(0);
                    $user->setCardAssociationTries(0); 
                }

                $session->getFlashBag()->add('success','Profil utilisateur édité avec succès');
                $em->flush();

                if (null === $response = $event->getResponse()) {                  
                    $url = $this->generateUrl('cairn_user_profile_view',array('username' => $user->getUsername()));            
                    $response = new RedirectResponse($url);                        
                }

                return $response;
            }
        }
        return $this->render('CairnUserBundle:Admin:edit_profile_content.html.twig',
            array('form'=>$form->createView(),'user'=>$user)
            );

    }

    /**
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function generateApiTokenAction(Request $request){
        if ($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $securityService = $this->get('cairn_user.security');

            $token = $securityService->generateToken();
            $apiClient = $em->getRepository(ApiClient::class)->findByAccessToken($securityService->vigenereEncode($token));

            while($apiClient){
                $token = $securityService->generateToken();
                $apiClient = $em->getRepository(ApiClient::class)->findByAccessToken($securityService->vigenereEncode($token));
            }

            $returnArray = array('token'=>$token) ;
            return new JsonResponse($returnArray);
        }
        return new Response("Ajax only",400);
    }

    /**
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function editApiClientAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $securityService = $this->get('cairn_user.security');
        $session = $request->getSession();
        $currentUser = $this->getUser();

        if(! $user->hasRole('ROLE_PRO')){
            throw new AccessDeniedException('Action réservée aux prestataires');
        }

        $apiClient = new ApiClient();

        $login = ($user->getApiClient()) ? $user->getApiClient()->getLogin() : NULL;
        $apiClient->setLogin($login);

        $form = $this->createForm(ApiClientType::class, $apiClient);

         if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $dataForm = $form->getData();

                if( ! ($userApiClient = $user->getApiClient())) {
                    $userApiClient = new ApiClient($user);
                }

                if($accessToken = $apiClient->getAccessToken()){
                    $userApiClient->setAccessToken( $securityService->vigenereEncode($accessToken) );
                }

                if($webhook = $apiClient->getWebhook()){
                    $userApiClient->setWebhook( $securityService->vigenereEncode($webhook) );
                }

                $userApiClient->setLogin($apiClient->getLogin());
                $em->persist($userApiClient);                
                $em->flush();
                $session->getFlashBag()->add('success','Données API éditées avec succès');
                return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
            }
        }
         return $this->render('CairnUserBundle:Admin:edit_apiclient.html.twig',array('form'=>$form->createView()));
      
    }

    /**
     * Administrator can add id document to user profile
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function addIdentityDocumentAction(Request $request, User $user)
    {
        $session = $request->getSession();
        $currentUser = $this->getUser();

        if(! ( $user->hasReferent($currentUser) ) ){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }

        $form = $this->createForm(AddIdentityDocumentType::class, $user);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $session->getFlashBag()->add('success','Pièce ajoutée');
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
            }
        }
        return $this->render('CairnUserBundle:Admin:add_id-document.html.twig',array('form'=>$form->createView()));
    }

    public function phonesDashboardAction(Request $request)
    {
        $currentUser = $this->getUser();
        $currentUserID = $currentUser->getID();
        $em = $this->getDoctrine()->getManager();
        $phoneRepo = $em->getRepository('CairnUserBundle:Phone');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $allPhones = $phoneRepo->findAllPros();

        $form = $this->createFormBuilder()
            ->add('phoneNumber',  TextType::class,array(
                'label'=>'N° de téléphone',
                'data'=>'+33',
                'required'=>false))
            ->add('identifier',  TextType::class,array(
                'label'=>'ID SMS',
                'required'=>false))
            ->add('cairn_user', TextType::class, array('label' => 'Compte','attr'=>array('placeholder'=>'email ou nom'),'required'=>false))
            ->add('save',      SubmitType::class, array('label' => 'Rechercher'))
                ->getForm();

        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();            
                $phoneNumber = $dataForm['phoneNumber'];
                $identifier = $dataForm['identifier'];
                $formAutocompleteName = $dataForm['cairn_user'];

                $pb = $phoneRepo->createQueryBuilder('p');

                if($phoneNumber){
                    $phoneRepo->wherePhoneNumber($pb, $phoneNumber);
                }
                if($identifier){
                    $phoneRepo->whereIdentifier($pb, $identifier);
                }
                if($formAutocompleteName){
                    preg_match('#\((.*)\)$#',$formAutocompleteName,$matches_email);
    
                    if (! $matches_email){
                        $session->getFlashBag()->add('error','Votre recherche ne contient aucun email');
                        return new RedirectResponse($request->getRequestUri());
                    }

                    $user = $userRepo->findOneByEmail($matches_email[1]);

                    if($user){
                        $phoneRepo->whereUser($pb, $user);
                    }
                }

                $allPhones = $pb->getQuery()->getResult();
            }

        }
        return $this->render('CairnUserBundle:Admin:phones_dashboard.html.twig',array(
            'form'=>$form->createView(),
            'allPhones'=>$allPhones));

    }

    /**
     * Administrator's dashboard to see users status on a single page
     *
     * Sets into groups users by several criteria :
     * _ role (pro, person, admin, superadmin)
     * _ status ( opposed, enabled, waiting for validation)
     * _ waiting for security card
     */
    public function userDashboardAction(Request $request)
    {
        $currentUser = $this->getUser();
        $currentUserID = $currentUser->getID();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $pros = new \stdClass();
        $pros->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PRO',true);
        $pros->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PRO',false);
        $pros->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_PRO');
        $pros->nocard = $userRepo->findUsersWithPendingCard($currentUserID,'ROLE_PRO');

        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereReferent($ub, $currentUserID)->whereToRemove($ub, true)->whereRole($ub, 'ROLE_PRO');
        $pros->toRemove = $ub->getQuery()->getResult();

        $persons = new \stdClass();
        $persons->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PERSON',true);
        $persons->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PERSON',false);
        $persons->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_PERSON');
        $persons->nocard = $userRepo->findUsersWithPendingCard($currentUserID,'ROLE_PERSON');

        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereReferent($ub, $currentUserID)->whereToRemove($ub, true)->whereRole($ub, 'ROLE_PERSON');
        $persons->toRemove = $ub->getQuery()->getResult();

        $admins = new \stdClass();
        $admins->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_ADMIN',true);
        $admins->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_ADMIN',false);
        $admins->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_ADMIN');

        $superAdmins = array();

        if($currentUser->hasRole('ROLE_SUPER_ADMIN')){
            $superAdmins = new \stdClass();
            $superAdmins->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_SUPER_ADMIN',false);
            $superAdmins->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_SUPER_ADMIN');
        }

        $form = $this->createFormBuilder()
            ->add('cairn_user', TextType::class, array('label' => 'Compte','attr'=>array('placeholder'=>'email ou nom')))
            ->add('forward', SubmitType::class, array('label' => 'Accéder au profil'))
            ->getForm();

        $allUsers = array(
            'pros'=>$pros, 
            'persons'=>$persons,
            'admins'=>$admins,
            'superAdmins'=>$superAdmins,
        );

        return $this->render('CairnUserBundle:Admin:dashboard.html.twig',array('form'=>$form->createView(),'allUsers'=>$allUsers));
    }

    /**
     * Credits user account from nothing, available only in dev environment 
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function exnihiloCreditAction(Request $request, User $creditor)
    {
        $session = $request->getSession();

        if($this->getParameter('kernel.environment') != 'dev'){
            throw new Exception('Feature only available in development environment');
        }

        $amount = 2000;
        $em = $this->getDoctrine()->getManager();
        $accountManager = $this->get('cairn_user.account_manager');

        $operation = $accountManager->creditUserAccount($creditor, $amount, Operation::TYPE_DEPOSIT, 'Crédit de compte test' );

        $em->persist($operation);
        $em->flush();

        $session->getFlashBag()->add('success','Compte de '.$creditor->getName().' crédité de '.$amount);

        return $this->redirectToRoute('cairn_user_profile_view',array('username' => $creditor->getUsername()));
    }

    /**
     * Administrator's dashboard to see data related to electronic money safe on a single page
     *
     * This action retrieves all waiting deposits, their cumulated amount of money and the currently available electronic money
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moneySafeDashboardAction(Request $request)
    {
        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $depositRepo = $em->getRepository('CairnUserBundle:Deposit');

        //get all deposits with state scheduled + amount of these deposits ordered by date
        $db = $depositRepo->createQueryBuilder('d');
        $depositRepo->whereStatus($db,Deposit::STATE_SCHEDULED);
        $db->orderBy('d.requestedAt','ASC');

        $deposits = $db->getQuery()->getResult();
        $amountOfDeposits = $db->select('sum(d.amount)')->getQuery()->getSingleScalarResult();


        //get amount of available e-mlc
        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($currentUser->getCyclosID(),NULL);

        foreach($accounts as $account){
            if(preg_match('#compte_de_debit_cairn_numerique#', $account->type->internalName)){
                $debitAccount = $account;
            }
        }
        $availableAmount = $debitAccount->status->balance;

        return $this->render('CairnUserBundle:Admin:money_safe_dashboard.html.twig',array('availableAmount'=>$availableAmount, 'deposits'=>$deposits, 'amountOfDeposits'=>$amountOfDeposits));

    }

    /**
     * Declares a new money safe balance 
     *
     * This action allows to declare a new amount of available electronic money, and executes as many waiting deposits as possible
     * according to this new balance, ordered by date of request. The status of these deposits change to "PROCESSED", and equivalent 
     * operations are persisted
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moneySafeEditAction(Request $request)
    {
        $session = $request->getSession();

        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $depositRepo = $em->getRepository('CairnUserBundle:Deposit');

        //get amount of available e-mlc
        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($currentUser->getCyclosID(),NULL);

        foreach($accounts as $account){
            if(preg_match('#compte_de_debit_cairn_numerique#', $account->type->internalName)){
                $debitAccount = $account;
            }
        }
        $moneySafeBalance = $debitAccount->status->balance;

        $form = $this->createFormBuilder()
            ->add('amount',    NumberType::class, array('label' => 'Nombre de [e]-cairns nouvellement gagés'))
            ->add('save',      SubmitType::class, array('label' => 'Confirmation'))
            ->getForm();

        if($request->isMethod('POST')){
            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();            
                $newAvailableAmount = $dataForm['amount'];

                $bankingService = $this->get('cairn_user_cyclos_banking_info');

                $paymentData = $bankingService->getPaymentData('SYSTEM','SYSTEM',NULL);
                foreach($paymentData->paymentTypes as $paymentType){
                    if(preg_match('#creation_mlc_numeriques#', $paymentType->internalName)){
                        $creditTransferType = $paymentType;
                    }
                }
                $amountToCredit = $newAvailableAmount;
                $description = 'Declaration de '.$newAvailableAmount .' nouveaux [e]-cairns disponibles par '.$currentUser->getName().' le '.date('d-m-Y');


                $res = $this->bankingManager->makeSinglePreview($paymentData,$amountToCredit,$description,$creditTransferType,new \Datetime());
                $paymentVO = $this->bankingManager->makePayment($res->payment);

                //get all deposits with state scheduled + amount of these deposits ordered by date
                $db = $depositRepo->createQueryBuilder('d');
                $depositRepo->whereStatus($db,Deposit::STATE_SCHEDULED);
                $db->orderBy('d.requestedAt','ASC');

                $deposits = $db->getQuery()->getResult();

                $reason = 'Acompte post virement Helloasso'; 

                //while there is enough available electronic mlc, credit user

                $moneySafeBalance += $newAvailableAmount;
                foreach($deposits as $deposit){
                    if($deposit->getAmount() <= $moneySafeBalance){
                        $paymentData = $bankingService->getPaymentData('SYSTEM',$deposit->getCreditor()->getCyclosID(),NULL);
                        foreach($paymentData->paymentTypes as $paymentType){
                            if(preg_match('#credit_du_compte#', $paymentType->internalName)){
                                $creditTransferType = $paymentType;
                            }
                        }

                        $now = new \Datetime();
                        $res = $this->bankingManager->makeSinglePreview($paymentData,$deposit->getAmount(),$reason,$creditTransferType,$now);
                        $paymentVO = $this->bankingManager->makePayment($res->payment);

                        $deposit->setStatus(Deposit::STATE_PROCESSED);
                        $deposit->setExecutedAt($now);

                        $operation = new Operation();
                        $operation->setType(Operation::TYPE_CONVERSION_HELLOASSO);
                        $operation->setReason($reason);
                        $operation->setPaymentID($paymentVO->transferId);
                        $operation->setFromAccountNumber($res->fromAccount->number);
                        $operation->setToAccountNumber($res->toAccount->number);
                        $operation->setAmount($res->totalAmount->amount);
                        $operation->setDebitorName($this->get('cairn_user_cyclos_user_info')->getOwnerName($res->fromAccount->owner));
                        $operation->setCreditor($deposit->getCreditor());

                        $em->persist($operation);

                        $moneySafeBalance -= $deposit->getAmount();
                    }
                }


                $session->getFlashBag()->add('info','Des crédits de compte [e]-cairns ont peut-être été exécutés');
                $em->flush();

                return $this->redirectToRoute('cairn_user_electronic_mlc_dashboard');


            }
        }

        return $this->render('CairnUserBundle:Admin:money_safe_edit.html.twig',array('form' => $form->createView(),'availableAmount'=>$moneySafeBalance));

    }

    /**
     * Set the enabled attribute of user with provided ID to true
     *
     * An email is sent to the user being (re)activated
     *
     * @throws  AccessDeniedException Current user trying to activate access is not a referent of the user being involved
     * @Method("GET")
     */ 
    public function activateUserAction(Request $request, User $user, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $currentUser = $this->getUser();

        if($user->getConfirmationToken()){
            if($currentUser->hasRole('ROLE_SUPER_ADMIN')){
                $security = $this->get('cairn_user.security');

                $user->setConfirmationToken(null);
                $security->assignDefaultReferents($user);
            }
        }

         
        if(! $user->hasReferent($currentUser)){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }elseif($user->isEnabled()){
            $session->getFlashBag()->add('info','L\'espace membre de ' . $user->getName() . ' est déjà accessible.');
            return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
        }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){

                $messageNotificator = $this->get('cairn_user.message_notificator');

                //if first activation : create user in cyclos and ask if generate card now
                if(! $user->getLastLogin()){
                    try{
                        $userVO = $this->get('cairn_user_cyclos_user_info')->getUserVO($user->getCyclosID());
                        $this->get('cairn_user.access_platform')->enable(array($user));
                    }catch(\Exception $e){
                        if(! $e->errorCode == 'ENTITY_NOT_FOUND'){
                            throw $e;
                        }else{
                            //create cyclos user
                            $userDTO = new \stdClass();                                    
                            $userDTO->name = $user->getName();                             
                            $userDTO->username = $user->getUsername();                     
                            $userDTO->login = $user->getUsername();                        
                            $userDTO->email = $user->getEmail();                           


                            $temporaryPassword = ($this->getParameter('kernel.environment') == 'prod') ? User::randomPassword() : '@@bbccdd';
                            $user->setPlainPassword($temporaryPassword);

                            $password = new \stdClass();                                   
                            $password->assign = true;                                      
                            $password->type = 'login';
                            $password->value = $temporaryPassword;
                            $password->confirmationValue = $password->value;
                            $userDTO->passwords = $password;                               

                            if($user->hasRole('ROLE_PRO')){
                                $groupName = $this->getParameter('cyclos_group_pros');  
                            }elseif($user->hasRole('ROLE_PERSON')){
                                $groupName = $this->getParameter('cyclos_group_persons');  
                            }else{                                                                 
                                $groupName = $this->getParameter('cyclos_group_network_admins');
                            }

                            $groupVO = $this->get('cairn_user_cyclos_group_info')->getGroupVO($groupName);

                            //if webServices channel is not added, it is impossible to update/remove the cyclos user entity from 3rd party app
                            $webServicesChannelVO = $this->get('cairn_user_cyclos_channel_info')->getChannelVO('webServices');

                            $newUserCyclosID = $this->userManager->addUser($userDTO,$groupVO,$webServicesChannelVO);
                            $user->setCyclosID($newUserCyclosID);

                            if($user->isAdherent()){
                                $icc_account = $this->get('cairn_user_cyclos_account_info')->getDefaultAccount($newUserCyclosID);
                                $icc = $icc_account->number;
                                $user->setMainICC($icc);
                            }


                            //activate user and send email to user
                            $body = $this->renderView('CairnUserBundle:Emails:welcome.html.twig',
                                array('user'=>$user,
                                'login_url'=>$this->get('router')->generate('fos_user_security_login')));
                            $subject = 'Plateforme numérique du Cairn';

                            $this->get('cairn_user.access_platform')->enable(array($user), $subject, $body);

                            //if user is pro, find all adherents (pro / part) close to him according to lat/long data and distance
                            if($user->hasRole('ROLE_PRO')){
                            }

                            //then, if above filter not efficient enough, go through one by one distance calculation
                            //finally, send notifications to all those people


                            //send email to local group referent if pro
                            if($user->hasRole('ROLE_PRO') && ($referent = $user->getLocalGroupReferent()) ){
                                $from = $messageNotificator->getNoReplyEmail();
                                $to = $referent->getEmail();
                                $subject = 'Référent Pro';
                                $body = 'Vous êtes désormais GL référent du professionnel ' . $user->getName();
                                $messageNotificator->notifyByEmail($subject,$from,$to,$body);
                            }

                            $session->getFlashBag()->add('success','L\'utilisateur ' . $user->getName() . ' a été activé. Il peut accéder à la plateforme.');
                            $em->flush();
                            return $this->redirectToRoute('cairn_user_card_associate',array('username'=>$user->getUsername()));
                        }
                    }
                }else{
                    $this->get('cairn_user.access_platform')->enable(array($user));
                }
            }

            $em->flush();
            $session->getFlashBag()->add('success','L\'utilisateur ' . $user->getName() . ' a été activé. Il peut accéder à la plateforme.');
            return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format, 'username' => $user->getUsername()));
        }

        $responseArray = array('user' => $user,'form'=> $form->createView());

        return $this->render('CairnUserBundle:User:activate.html.twig', $responseArray);

    }

    /**
     * Assign a unique local group (ROLE_ADMIN) as a referent of @param
     *
     * @param  User $user  User entity the referent is assigned to
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function assignReferentAction(Request $request, User $user)
    {
        if(!$user->hasRole('ROLE_PRO')){
            throw new AccessDeniedException('Seuls les professionnels doivent avoir des référents assignés manuellement.');
        }

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $choices = $userRepo->myFindByRole(array('ROLE_ADMIN'));

        $messageNotificator = $this->get('cairn_user.message_notificator');
        $from = $messageNotificator->getNoReplyEmail();

        $form = $this->createFormBuilder()
            ->add('singleReferent', EntityType::class, array(
                'class'=>User::class,
                'constraints'=>array(                              
                    new Assert\NotNull()                           
                ),
                'choice_label'=>'name',
                'choice_value'=>'username',
                'data'=> $user->getLocalGroupReferent(),
                'choices'=>$choices,
                'expanded'=>true,
                'required'=>false
            ))
            ->add('cancel', SubmitType::class, array('label'=>'Annuler'))
            ->add('save', SubmitType::class, array('label'=>'Assigner'))
            ->getForm();

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->get('save')->isClicked()){
                $referent = $form->get('singleReferent')->getData();
                $currentAdminReferent = $user->getLocalGroupReferent();

                if($referent && !$referent->hasRole('ROLE_ADMIN')){
                    throw new AccessDeniedException('Seul un groupe local peut être assigné via ce formulaire.');
                }
                if($referent){
                    if($user->hasReferent($referent)){
                        $session->getFlashBag()->add('info',
                            $referent->getName() . ' est déjà le groupe local référent de '.$user->getName());
                        return new RedirectResponse($request->getRequestUri());
                    }
                }

                if(!$currentAdminReferent && !$referent){
                    $session->getFlashBag()->add('info',$user->getName(). ' n\'avait pas de groupe local référent.');
                    return new RedirectResponse($request->getRequestUri());
                }

                if($currentAdminReferent){
                    $to = $currentAdminReferent->getEmail();
                    $subject = 'Référent Professionnel e-Cairn';
                    $body = 'Votre GL n\'est plus référent du professionnel ' . $user->getName();
                    $messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $user->removeReferent($currentAdminReferent);
                }
                if($referent){
                    $user->addReferent($referent);

                    $to = $referent->getEmail();
                    $subject = 'Référent Professionnel e-Cairn';
                    $body = 'Vous êtes désormais GL référent du professionnel ' . $user->getName();
                    $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                    $session->getFlashBag()->add('success',
                        $referent->getName() . ' est désormais référent de '.$user->getName());
                }else{
                    $session->getFlashBag()->add('success',
                        $user->getName(). ' n\'a plus de groupe local référent.');
                }

                $em->flush();
                return $this->redirectToRoute('cairn_user_profile_view',array('username'=>$user->getUsername()));
            }else{
                return $this->redirectToRoute('cairn_user_profile_view',array('username'=>$user->getUsername()));
            }
        }
        return $this->render('CairnUserBundle:User:add_referent.html.twig',array('form'=>$form->createView(),'user'=>$user));
    }   

    /**
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function cyclosSyncAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $accountManager =  $this->get('cairn_user.account_manager');
        $messageNotificator = $this->get('cairn_user.message_notificator');

        $possibleTypes = Operation::getPotentiallyDesynchronizedTypes();

        $form = $this->createFormBuilder()
            ->add('payment_id', TextType::class, array('label' => 'ID de l opération'))
            ->add('reason', TextType::class, array('label' => 'Motif'))
            ->add('type',    ChoiceType::class, array(
                'label' => 'type d\'opération',
                'required'=>true,
                'choices' => $possibleTypes,
                'choice_label'=> function($choice){
                    return Operation::getTypeName($choice);
                },
                'multiple'=>false,
                'expanded'=>false
                ))

            ->add('save',      SubmitType::class, array('label' => 'Synchroniser'))
            ->getForm();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $dataForm = $form->getData();

            $paymentID = $dataForm['payment_id'];
            $type = $dataForm['type'];
            $reason = $dataForm['reason'];

            $operation = $operationRepo->findOneByPaymentID($paymentID);

            if($operation){
                $session->getFlashBag()->add('error','Le paiement d\'identifiant '.$paymentID.' existe déjà');
                return new RedirectResponse($request->getRequestUri());
            }

            try{
                $transferVO = $this->get('cairn_user_cyclos_banking_info')->getTransferByID($paymentID);
            }catch(\Exception $e){
                if( ($e->errorCode == 'ENTITY_NOT_FOUND') || ($e->errorCode == 'NULL_POINTER')){
                    $session->getFlashBag()->add('error','Donnée introuvable');
                    return new RedirectResponse($request->getRequestUri());
                }else{
                    throw $e;
                }
            }

            $operation = $accountManager->hydrateOperation($transferVO,$type,$reason);

            $em->persist($operation);
            $em->flush();

            $session->getFlashBag()->add('success','Synchronisation effectuée avec succès !');
            return new RedirectResponse($request->getRequestUri());

        }

        return $this->render('CairnUserBundle:Admin:operation_sync.html.twig',array('form' => $form->createView() ));
    }

}
