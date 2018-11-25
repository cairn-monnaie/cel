<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Cyclos
use Cyclos;

//manage Events 
use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\AccessClientManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
//manage Forms
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\RegistrationType;
use Cairn\UserBundle\Form\CardType;
use Cairn\UserBundle\Form\BeneficiaryType;
use Cairn\UserBundle\Form\ProfileFormType;
use Cairn\UserBundle\Form\ChangePasswordType;

use Cairn\UserBundle\Validator\UserPassword;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\PasswordType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class contains all actions related to user experience
 *
 * @Security("is_granted('ROLE_PRO')")
 */
class UserController extends Controller
{
    private $userManager;
    //    private $scriptManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    public function indexAction(Request $request, $_format)
    {
        $checker = $this->get('security.authorization_checker');

        //last users registered
        $userRepo = $this->getDoctrine()->getManager()->getRepository('CairnUserBundle:User');
        $qb = $userRepo->createQueryBuilder('u')
            ->orderBy('u.creationDate','DESC')
            ->andWhere('u.enabled = true')
            ->setMaxResults(5);
        $userRepo->whereRole($qb,'ROLE_PRO');
        $users =  $qb->getQuery()->getResult();

        //accounts of current user
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($this->getUser());

        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id);
//        var_dump($accounts);
//        return new Response('ok');

        //last operations
        $accountTypes = array();
        foreach($accounts as $account){
            $accountTypes[] = $account->type;
        }

        $statuses = array(array('PROCESSED'),array('OPEN','CLOSED','CANCELED'),array('CLOSED'));
        $transactions = $this->get('cairn_user_cyclos_banking_info')->getTransactions($ownerVO,$accountTypes,array('PAYMENT','SCHEDULED_PAYMENT'),$statuses,NULL,NULL,NULL,20);

        if($checker->isGranted('ROLE_PRO')){
            if($_format == 'json'){
                $response = array('accounts'=>$accounts,'lastTransactions'=>$transactions, 'lastUsers'=>$users);
                return $this->json($response);
            }
            return $this->render('CairnUserBundle:User:index.html.twig',array('accounts'=>$accounts,'lastTransactions'=>$transactions,'lastUsers'=>$users));
        }

    }

    /**
     *Get the list of all users grouped by roles
     *
     */
    public function usersAction(Request $request, $_format)
    {
        $currentUserID = $this->getUser()->getID();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $ub = $userRepo->createQueryBuilder('u');

        //validated pros who the current user is referent of 
        $validatedPros = new \stdClass();
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereRole($ub,'ROLE_PRO')->whereReferent($ub, $currentUserID);

        $listEnabledPros = $ub->andWhere('u.confirmationToken is NULL')
            ->andWhere('u.enabled = true')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        //blocked pros who the current user is referent of 
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereRole($ub,'ROLE_PRO')->whereReferent($ub, $currentUserID);
        $listBlockedPros = $ub->andWhere('u.confirmationToken is NULL')
            ->andWhere('u.enabled = false')
            ->andWhere('u.lastLogin is not NULL')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        $validatedPros->enabledPros = $listEnabledPros;
        $validatedPros->blockedPros = $listBlockedPros;

        //never validated pros who the current user is referent of 
        $pendingUsers = new \stdClass();
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereRole($ub,'ROLE_PRO')->whereReferent($ub, $currentUserID);
        $pendingUsers->pros = $ub->andWhere('u.confirmationToken is NULL')
            ->andWhere('u.enabled = false')
            ->andWhere('u.lastLogin is NULL')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        //blocked admins who the current user is referent of 
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereRole($ub,'ROLE_ADMIN')->whereReferent($ub, $currentUserID);
        $pendingUsers->admins = $ub->andWhere('u.confirmationToken is NULL')
            ->andWhere('u.enabled = false')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        //all members waiting for a card who the current user is referent of 
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereReferent($ub, $currentUserID);
        $pendingCards = $ub
            ->andWhere('u.confirmationToken is NULL')
            ->andWhere('u.enabled = true')
            ->join('u.card','c')
            ->andWhere('c.fields is NULL')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        //all ROLE_ADMIN
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereRole($ub,'ROLE_ADMIN');
        $listAdmins = $ub->andWhere('u.confirmationToken is NULL')
            ->andWhere('u.enabled = true')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        //all ROLE_SUPER_ADMIN
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereRole($ub,'ROLE_SUPER_ADMIN');
        $listSuperAdmins = $ub->andWhere('u.confirmationToken is NULL')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        $allUsers = array(
            'validPros'=>$validatedPros, 
            'pendingUsers'=>$pendingUsers,
            'pendingCards'=>$pendingCards,
            'listAdmins'=>$listAdmins,
            'listSuperAdmins'=>$listSuperAdmins
        );

        if($_format == 'json'){
            return $this->json($allUsers);
        }
        return $this->render('CairnUserBundle:User:list_users.html.twig',$allUsers);
    }


    /**
     * View the profile of $user
     *
     * What will be displayed on the screen will depend on the current user
     *
     * @param User $user User with profile to view
     * @Method("GET")
     */
    public function viewProfileAction(Request $request , User $user, $_format)                           
    {                                                                          
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        if($_format == 'json'){
            $array_user = array('name'=>$user->getName(),
                                'username'=>$user->getUsername(),
                                'email'=>$user->getEmail(),
                                'street1'=>$user->getAddress()->getStreet1(),
                                'street2'=>$user->getAddress()->getStreet2(),
                                'zipcode'=>$user->getAddress()->getZipCity()->getZipCode(),
                                'city'=>$user->getCity(),
                                'image'=>$user->getImage()
                            );

            return $this->json(array('user'=>$array_user));
        }
        return $this->render('CairnUserBundle:Pro:view.html.twig', array('user'=>$user));
    }                      

    /**
     * Get the list of beneficiaries for current User
     *
     */
    public function listBeneficiariesAction(Request $request, $_format)
    {
        $beneficiaries = $this->getUser()->getBeneficiaries();

        if($_format == 'json'){
            return $this->json(array('beneficiaries'=>$beneficiaries));
        }
        return $this->render('CairnUserBundle:Pro:list_beneficiaries.html.twig',array('beneficiaries'=>$beneficiaries));
    }



    /**
     * Checks if the beneficiary exists in database, and is a current beneficiary of $user
     *
     *@param User $user User who is supposed to own the account
     *@param int $ICC account cyclos ID

     *@return stdClass with attributes : 'existingBeneficiary'(Beneficiary class) and 'hasBeneficiary'(boolean)
     */
    public function isValidBeneficiary($user, $ICC)
    {
        $em = $this->getDoctrine()->getManager();
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');
        $toReturn = new \stdClass();

        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        $toReturn->account = $this->get('cairn_user_cyclos_account_info')->hasAccount($ownerVO->id,$ICC);

        $existingBeneficiary = $beneficiaryRepo->findOneBy(array('user'=>$user,'ICC'=>$ICC));

        if($existingBeneficiary){
            $toReturn->existingBeneficiary = $existingBeneficiary;
            $toReturn->hasBeneficiary = $this->getUser()->hasBeneficiary($existingBeneficiary);
        }
        else{
            $toReturn->existingBeneficiary = NULL;
            $toReturn->hasBeneficiary = NULL; 
        }
        return $toReturn;
    }


    /**
     * Adds a new beneficiary to the existing list
     *
     * This action is considered as a sensible operation
     * Proposes a list of potential users with autocompletion, then checks if the user and the ICC match, before ensuring that the
     * beneficiary is valid and adding it.
     *
     * As the User and Beneficiary class have a ManyToMany bidirectional relationship, adding it the two directions must be done
     *
     */
    public function addBeneficiaryAction(Request $request, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');


        $possibleUsers = $userRepo->myFindByRole(array('ROLE_PRO'));
        $currentUser = $this->getUser();

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, array('label' => 'Nom du bénéficiaire'))
            ->add('email', TextType::class, array('label' => 'email du bénéficiaire'))
            //ICC : IntegerType does not work for bigint : rounding after 14 figures (Account Ids in Cyclos have 19)
            ->add('ICC',   TextType::class,array('label'=>'Identifiant de Compte Cairn(ICC)'))
            ->add('add', SubmitType::class, array('label' => 'Ajouter'))
            ->getForm();

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $dataForm = $form->getData();

                $re_email ='#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#' ;
                $re_name ='#^[\w\.]+$#' ;
                $re_ICC = '#^[-]?[0-9]+$#';
                preg_match_all($re_email,$dataForm['email'], $matches_email, PREG_SET_ORDER, 0);
                preg_match_all($re_name, $dataForm['name'], $matches_name, PREG_SET_ORDER, 0);
                preg_match_all($re_ICC, $dataForm['ICC'], $matches_ICC, PREG_SET_ORDER, 0);

                if((count($matches_email) >= 1) || (count($matches_name) >= 1)){
                    $user = $userRepo->findOneBy(array('email'=>$matches_email[0][0]));
                    if(!$user){
                        $user = $userRepo->findOneBy(array('name'=>$matches_name[0][0]));
                        if(!$user){
                            $session->getFlashBag()->add('error','Votre recherche ne correspond à aucun membre');
                            return new RedirectResponse($request->getRequestUri());

                        }
                    }
                    else{
                        if($user->getID() == $currentUser->getID())
                        {
                            $session->getFlashBag()->add('error','Vous ne pouvez pas vous ajouter vous-même...');
                            return new RedirectResponse($request->getRequestUri());
                        }
                        $ICC = $matches_ICC[0][0];
                        //check that ICC exists and corresponds to this user

                        $validity = $this->isValidBeneficiary($user,$ICC);
                        if(!$validity->account){
                            $session->getFlashBag()->add('error','L\' ICC indiqué ne correspond à aucun compte de ' .$user->getName());
                            return new RedirectResponse($request->getRequestUri());

                        }

                        //check that beneficiary is not already in database, o.w create new one
                        $existingBeneficiary = $validity->existingBeneficiary;

                        if(!$existingBeneficiary){
                            $beneficiary = new Beneficiary();
                            $beneficiary->setUser($user);
                            $beneficiary->setICC($ICC);
                        }
                        else{ 
                            if($currentUser->hasBeneficiary($existingBeneficiary)){
                                $session->getFlashBag()->add('info','Ce compte fait déjà partie de vos bénéficiaires.');
                                return $this->redirectToRoute('cairn_user_beneficiaries_list', array('_format'=>$_format));
                            }
                            $beneficiary = $existingBeneficiary;
                        }

                        $beneficiary->addSource($currentUser);
                        $currentUser->addBeneficiary($beneficiary);
                        $em->persist($beneficiary);                    
                        $em->persist($currentUser);
                        $em->flush();
                        $session->getFlashBag()->add('success','Nouveau bénéficiaire ajouté avec succès');
                        return $this->redirectToRoute('cairn_user_beneficiaries_list', array('_format'=>$_format));
                    }
                }
                else{
                    $session->getFlashBag()->add('error','Votre recherche ne correspond à aucun compte');
                    return new RedirectResponse($request->getRequestUri());
                }       

            }
        }

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'pros'=>$possibleUsers));
        }
        return $this->render('CairnUserBundle:Pro:add_beneficiaries.html.twig',array('form'=>$form->createView(),'pros'=>$possibleUsers));
    }

    /**
     *Edit an existing beneficiary
     *
     * Only the ICC can be changed. Then, this new beneficiary is verified : 
     *  _checks that the user's beneficiary has an account with the provided ICC
     *  _check that new beneficiary is not already a beneficiary
     *@param Beneficiary $beneficiary Beneficiary with a given ICC is edited
     *@Method("GET")
     */
    public function editBeneficiaryAction(Request $request, Beneficiary $beneficiary, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');

        $newBeneficiary = new Beneficiary();
        $newBeneficiary->setUser($beneficiary->getUser());
        $newBeneficiary->setICC($beneficiary->getICC());
        $form = $this->createForm(BeneficiaryType::class,$newBeneficiary);
        $currentUser = $this->getUser();

        if($request->isMethod('GET')){
            $session->set('formerICC',$beneficiary->getICC());
        }
        if($request->isMethod('POST')){ //form filled and submitted            
            $formerBeneficiary = $beneficiaryRepo->findOneBy(array('ICC'=>$session->get('formerICC')));
            //            $session->remove('formerICC');
            $form->handleRequest($request);                                    
            if($form->isValid()){                                              
                $validity = $this->isValidBeneficiary($newBeneficiary->getUser(),$newBeneficiary->getICC());
                if(!$validity->account){
                    $session->getFlashBag()->add('error','L\' ICC indiqué ne correspond à aucun compte de ' .$newBeneficiary->getUser()->getName());
                    return new RedirectResponse($request->getRequestUri());
                }
                if($validity->hasBeneficiary){
                    $session->getFlashBag()->add('info','Ce compte fait déjà partie de vos bénéficiaires.');
                    return $this->redirectToRoute('cairn_user_beneficiaries_list', array('_format'=>$_format));
                }

                $existingBeneficiary = $validity->existingBeneficiary;
                if($existingBeneficiary){
                    $newBeneficiary = $existingBeneficiary;
                }

                $nbSources = count($formerBeneficiary->getSources()) ;
                $formerBeneficiary->removeSource($currentUser);
                $currentUser->removeBeneficiary($formerBeneficiary);
                if($nbSources == 1){
                    $em->remove($formerBeneficiary);
                }
                $currentUser->addBeneficiary($newBeneficiary);
                $newBeneficiary->addSource($currentUser);

                $em->persist($newBeneficiary);
                $em->persist($currentUser);
                $em->flush();
                $session->getFlashBag()->add('success','Modification effectuée avec succès');
                return $this->redirectToRoute('cairn_user_beneficiaries_list', array('_format'=>$_format));
            }                                                              
        }                                                                  

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView()));
        }
        return $this->render('CairnUserBundle:Pro:confirm_edit_beneficiary.html.twig',array('form'=>$form->createView()));
    }


    /**
     * Removes a given beneficiary
     *
     * Once $beneficiary is removed, we ensure that this beneficiary is associated to at least one user. Otherwise, it is removed
     * @TODO : try the option OrphanRemoval in annotations to let Doctrine do it 
     * @param Beneficiary $beneficiary Beneficiary to remove
     * @Method("GET")
     */
    public function removeBeneficiaryAction(Request $request, Beneficiary $beneficiary, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ConfirmationType::class);
        $currentUser = $this->getUser();

        if(!$currentUser->hasBeneficiary($beneficiary)){
            $session->getFlashBag()->add('error',' Donnée introuvable');
            return $this->redirectToRoute('cairn_user_beneficiaries_list',array('_format'=>$_format));
        }
        if($request->isMethod('POST')){ //form filled and submitted            

            $form->handleRequest($request);                                    
            if($form->isValid()){                                              
                if($form->get('save')->isClicked()){ 
                    $nbSources = count($beneficiary->getSources());
                    $beneficiary->removeSource($currentUser);
                    $currentUser->removeBeneficiary($beneficiary);
                    if($nbSources == 1){
                        $em->remove($beneficiary);
                    }

                    $em->flush();
                    $session->getFlashBag()->add('success','Suppression effectuée avec succès');

                    //TODO here
                }                                                              
                else{
                    $session->getFlashBag()->add('info','Suppression annulée');
                }
                return $this->redirectToRoute('cairn_user_beneficiaries_list',array('_format'=>$_format));
            }                                                                  
        }        
        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'beneficiary_name'=>$beneficiary->getUser()->getName()));
        }

        return $this->render('CairnUserBundle:Pro:confirm_remove_beneficiary.html.twig',
            array(
                'form'=>$form->createView(),
                'beneficiary_name'=>$beneficiary->getUser()->getName()
            ));
    }


    /**
     *Removes a user with id given in query
     *
     * This operation is considered as super sensible. It needs the security layer + the password input
     * If the input is incorrect, user's attribute 'cardKeyTries' or 'passwordTries' is incremented. 3 failures leads to disable the user.
     *
     * A user can remove its own member area, or an admin can do it if he is a referent.
     *
     * If the user to remove is a ROLE_USER, we ensure that all his accounts have a balance to zero
     *
     * @Method("GET")
     *
     * Can only be done by an admin on Cyclos side. 
     * Solution 1 : create a specific screen for all users with attribute "ask_removal = true" and remove them all in once in admin's
     * action. 
     *
     * Problem : If a user asks for removal and is accepted, it means that all his accounts are at 0. But if he receives a payment
     * between this and validation by admin, his account won't be 0 so he will not be removed. But there is no way to check (from an ICC provided) that the corresponding Symfony User has this attrbute, because an user can't look for the account of soemone else.
     *
     * Solution 1 : récupérer un compte via un account_number (configurer un account_number : script de génération +  service d'interception 'création de compte' + visiblité dans les Produits). Ainsi on pourrait récupérer, à partir du account_number, le propriétaire du compte puis son équivalent Symfony pour dire s'il est en instance de suppression ou non. (Long car beaucoup de nouvelles choses à voir)
     *
     * Solution 2 : vérifier le message d'erreur à la suppression(ConstraintViolatedOnRemoveException) et, si la suppression ne peut avoir lieu à cause d'un compte non nul, avertir l'utilisateur
     *
     */
    public function confirmRemoveUserAction(Request $request, User $user, $_format)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'); 

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        $factory = $this->get('security.encoder_factory');

        $userRepo = $em->getRepository('CairnUserBundle:User');

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        //check that account balances are all 0 (for PRO only)
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id,NULL);

        if($user->hasRole('ROLE_PRO')){
            foreach($accounts as $account){
                if($account->status->balance != 0){
                    $session->getFlashBag()->add('error','Certains comptes ont un solde non nul. La suppression ne peut aboutir.');
                    return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format,'id' => $user->getID()));
                }
            }
        }

        $form = $this->createForm(ConfirmationType::class);
        $form->add('current_password', PasswordType::class,array('label'=>'Mot de passe','required'=>false,
                                                              'constraints'=> new UserPassword() ));
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                if($form->get('save')->isClicked()){
                    if($isAdmin){
                        $redirection = 'cairn_user_users_home';
                    }else{
                        $redirection = 'fos_user_security_logout';
                    }

                    $this->removeUser($user, $currentUser);
                    $session->getFlashBag()->add('success','Espace membre supprimé avec succès');
                    return $this->redirectToRoute($redirection, array('_format'=>$_format));
                }
                else{
                    $session->getFlashBag()->add('info','La demande de suppression a été annulée.');
                    return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format,'id'=> $user->getID()));
                }
            }
        }

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'user'=>$user));
        }
        return $this->render('CairnUserBundle:Pro:confirm_remove.html.twig',array('form'=>$form->createView(),'user'=>$user));
    }

    /**
     *Does remove the user on both the Cyclos and Doctrine sides and sends email to removed user and their referents
     *
     * It removes the user from Doctrine and all related Beneficiary entities with it.
     *@param User $user User to be removed
     *@todo : test query builder request using a join to get beneficiaries associated to user to see if it is faster
     */
    public function removeUser(User $user, User $currentUser)
    {
        $em = $this->getDoctrine()->getManager();
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');
        $messageNotificator = $this->get('cairn_user.message_notificator');

        $referents = $user->getReferents();

        $saveName = $user->getName();

        $params = new \stdClass();
        $params->status = 'REMOVED';
        $params->user = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        $this->userManager->changeStatusUser($params);
        $emailTo = $user->getEmail();

        //remove beneficiaries associated to the user to remove
        $beneficiaries = $beneficiaryRepo->findBy(array('user'=>$user));
        foreach($beneficiaries as $beneficiary){
            $em->remove($beneficiary);
        }
        $em->remove($user);
        $em->flush();

        $subject = 'Ce n\'est qu\'un au revoir !';
        $from = $messageNotificator->getNoReplyEmail();
        $to = $emailTo;
        $body = $this->renderView('CairnUserBundle:Emails:farwell.html.twig');

        $messageNotificator->notifyByEmail($subject,$from,$to,$body);

        $subject = 'Un professionnel a été supprimé de la plateforme';
        $body = $saveName .' a été supprimé de la plateforme par '. $currentUser->getName();
        foreach($referents as $referent){
            $to = $referent->getEmail();
            $messageNotificator->notifyByEmail($subject,$from,$to,$body);
        }
    }

}
