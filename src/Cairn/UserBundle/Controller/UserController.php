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
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\ScriptManager;

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
use Cairn\UserBundle\Form\PhoneNumberType;

use Cairn\UserBundle\Validator\UserPassword;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\PasswordType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class contains all actions related to user experience
 *
 * @Security("is_granted('ROLE_ADHERENT')")
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

        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        //last pros registered
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $qb = $userRepo->createQueryBuilder('u')
            ->orderBy('u.creationDate','DESC')
            ->andWhere('u.enabled = true')
            ->setMaxResults(5);
        $userRepo->whereRole($qb,'ROLE_PRO');
        $users =  $qb->getQuery()->getResult();

        //accounts of current user
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);

        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id);

        $accountNumbers = $this->get('cairn_user_cyclos_account_info')->getAccountNumbers($ownerVO->id);

        //last operations
        $ob = $operationRepo->createQueryBuilder('o');
        $processedTransactions = $ob->where(
             $ob->expr()->orX(
                 $ob->expr()->andX(
                     $ob->expr()->in('o.fromAccountNumber', $accountNumbers),
                     $ob->expr()->in('o.type',Operation::getExecutedTypes())
                 ),
                 $ob->expr()->andX(
                     $ob->expr()->in('o.toAccountNumber', $accountNumbers),
                     $ob->expr()->in('o.type',Operation::getExecutedTypes())
                 )
             ))
            ->andWhere('o.paymentID is not NULL')
            ->orderBy('o.executionDate','ASC')
            ->setMaxResults(15)
            ->getQuery()->getResult();

        if($checker->isGranted('ROLE_ADHERENT')){
            if($_format == 'json'){
                $response = $this->get('serializer')->serialize($users[0], 'json');
                //$response = array('accounts'=>$accounts,'lastTransactions'=>$processedTransactions, 'lastUsers'=>$users);
                $response = new Response($response);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
                return $this->json($response);
            }
            return $this->render('CairnUserBundle:User:index.html.twig',array('accounts'=>$accounts,'lastTransactions'=>$processedTransactions,'lastUsers'=>$users));
        }

    }

    /**
     * Changes the current user's phone number
     *
     * This action permits to change current user's phone number. 
     */
    public function changePhoneNumberAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $formPhoneNumber = $this->createForm(PhoneNumberType::class);
        $formCode = $this->createFormBuilder()
            ->add('code', PasswordType::class,array('label'=>'Code de validation'))
            ->add('save', SubmitType::class,array('label'=>'Valider'))
            ->getForm();

        $formPhoneNumber->handleRequest($request);
        if($formPhoneNumber->isSubmitted() && $formPhoneNumber->isValid()){
            //give a new code to be validated
            $code = rand(1000,9999);
            $user->setPhoneNumberValidationCode($code);
            $user->setNbPhoneNumberRequests($user->getNbPhoneNumberRequests() + 1);
            $user->setLastPhoneNumberRequestDate(new \Datetime());
            $user->setPhoneNumber($formPhoneNumber->getData()['phoneNumber']);

            //let this check here in POST context, because the user must be able to access this page to provide code in this case
            if($user->getNbPhoneNumberRequests() > 3){
                $session->getFlashBag()->add('info','Vous avez déjà effectué 3 demandes de changement de numéro de téléphone sans validation... Cette action vous est désormais inaccessible');
                return $this->redirectToRoute('cairn_user_profile_view',array('id' => $user->getID()));
            }

            // send SMS with validation code to current user's new phone number
            $newPhoneNumber = $user->getPhoneNumber();
            $this->get('cairn_user.message_notificator')->sendSMS($newPhoneNumber, '');
            $em->flush();
            $session->getFlashBag()->add('info','Un code vous a été envoyé par SMS au ' .$newPhoneNumber.'. Saisissez-le pour valider votre nouveau numéro de téléphone');
            return $this->render('CairnUserBundle:User:change_phone_number.html.twig',
                array('formPhoneNumber'=>$formPhoneNumber->createView(),
                      'formCode'=>$formCode->createView()));
        }

        $formCode->handleRequest($request);
        if($formCode->isSubmitted() && $formCode->isValid()){
            $code = $formCode->getData()['code'];

            //no new phone number requested
            if(! $user->getPhoneNumberValidationCode()){
                $session->getFlashBag()->add('info','Aucune demande d\'ajout de numéro enregistrée');
                return new RedirectResponse($request->getRequestUri());
            }

            //should never happen but just in case
            if($user->getPhoneNumberValidationCode() && !$user->getPhoneNumber()){
                $session->getFlashBag()->add('info','Aucune numéro de téléphone enregistré');
                return $this->redirectToRoute('cairn_user_profile_view',array('id' => $user->getID()));
            }

            //valid code
            if($code == $user->getPhoneNumberValidationCode()){
                $user->setPhoneNumberValidationCode(NULL);
                $user->setNbPhoneNumberRequests(0);
                $user->setLastPhoneNumberRequestDate(NULL);
                $user->setPhoneNumberValidationTries(0);

                $em->flush();
                $session->getFlashBag()->add('success','Nouveau numéro de téléphone enregistré : '.$user->getPhoneNumber());
                return $this->redirectToRoute('cairn_user_profile_view',array('id' => $user->getID()));

            //invalid code
            }else{
                $user->setPhoneNumberValidationTries($user->getPhoneNumberValidationTries() + 1);
                $remainingTries = 3 - $user->getPhoneNumberValidationTries();
                if($remainingTries > 0){
                    $session->getFlashBag()->add('error','Code invalide : Veuillez réessayer. Il vous reste '.$remainingTries.' essais avant le blocage du compte');
                }else{
                    $this->get('cairn_user.access_platform')->disable(array($user),'Compte bloqué','Echecs');
                    $session->getFlashBag()->add('error','Trop d\'échecs : votre compte a été bloqué.');
                }

                $em->flush();
                return new RedirectResponse($request->getRequestUri());

            }
        }

        return $this->render('CairnUserBundle:User:change_phone_number.html.twig',
            array('formPhoneNumber'=>$formPhoneNumber->createView(),
                  'formCode'=>$formCode->createView()));
    }

    /**
     *Get the list of all users grouped by roles
     *
     */
    public function listUsersAction(Request $request, $_format)
    {
        $currentUserID = $this->getUser()->getID();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $pros = new \stdClass();
        $pros->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PRO',true);
        $pros->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PRO',false);
        $pros->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_PRO');
        $pros->nocard = $userRepo->findUsersWithPendingCard($currentUserID,'ROLE_PRO');

        $persons = new \stdClass();
        $persons->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PERSON',true);
        $persons->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PERSON',false);
        $persons->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_PERSON');
        $persons->nocard = $userRepo->findUsersWithPendingCard($currentUserID,'ROLE_PERSON');

        $admins = new \stdClass();
        $admins->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_ADMIN',true);
        $admins->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_ADMIN',false);
        $admins->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_ADMIN');

        $superAdmins = new \stdClass();
        $superAdmins->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_SUPER_ADMIN',true);
        $superAdmins->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_SUPER_ADMIN',false);
        $superAdmins->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_SUPER_ADMIN');

        $allUsers = array(
            'pros'=>$pros, 
            'persons'=>$persons,
            'admins'=>$admins,
            'superAdmins'=>$superAdmins,
        );

        if($_format == 'json'){
            return $this->json($allUsers);
        }
        return $this->render('CairnUserBundle:User:list_users.html.twig',$allUsers);
    }


//    public function getAction(Request $request, User $user)
//    {
//
//    }
    /**
     * List API options related to user URI 
     *
     */
    public function optionsAction(Request $request)                           
    {   
        $template = array(
            'notes'=> '',
            'path'=> '',
            'method'=> '',
            'Parameters'=> array(
                'query'=>array(),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(),
                'access denied'=> array(),
                'error'=> array()
            )
        );

        $options = array();
        $options['get users list'] = array(
            'notes'=> 'Request list of users',
            'path'=> 'api/users',
            'method'=> 'GET',
            'Parameters'=> array(
                'query'=>array(),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful request'
                ),
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'you do not have access to these users'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['get user profile'] = array(
            'notes'=> 'Request profile data for an user with id {id}',
            'path'=> "api/users/{id}",
            'method'=> 'GET',
            'Parameters'=> array(
                'query'=>array(
                    'id'=>array(
                        'description'=> 'user id (required)',
                        'data_type'=> 'int'
                    )
                ),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful request'
                ),
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'forbidden access'
                 ),
                'undefined user'=> array(
                     'status code'=> '404',
                     'reason'=> 'no user with given id'
                 ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['post'] = array(
            'notes'=> 'Create a new user using provided data',
            'path'=> 'api/users',
            'method'=> 'POST',
            'Parameters'=> array(
                'query'=>array(
                    'type'=>array(
                        'description'=> 'kind of user to add',
                        'data_type'=> 'string',
                        'values'=> array('pro','localGroup','superAdmin')
                    )
                ),
                'body'=>array(
                    'name'=>array(
                        'description'=> 'User name (required)',
                        'data_type'=> 'string'
                    ),
                    'username'=>array(
                        'description'=> 'User login (required)',
                        'data_type'=> 'string'
                    ),
                    'address'=>array(
                        'address1'=> array(
                            'description'=> 'User main address (required)',
                            'data_type'=> 'string'
                        ),
                        'address2'=> array(
                            'description'=> 'User complement address',
                            'data_type'=> 'string'
                        ),
                        'city'=> array(
                            'description'=> 'User current city (required)',
                            'data_type'=> 'string'
                        ),
                        'zipcode'=> array(
                            'description'=> 'Zipcode of the city (required)',
                            'data_type'=> 'int'
                        ),

                    ),
                    'description'=>array(
                        'description'=> 'Provide a few words on user\'s activity',
                        'data_type'=> 'text'
                    ),
                    'logo'=>array(
                        'description'=> 'User avatar',
                        'data_type'=> 'image',
                        'mimeTypes'=> array('image/jpeg','image/jpg','image/png','image/gif')
                    )
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '201',
                     'reason'=> 'successful user creation'
                ),
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'Given current user\'s role, creating a user is not authorized'
                 ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );        

        $options['put'] = array(
            'notes'=> ' Update user with id {id} using provided properties',
            'path'=> 'api/users/{id}',
            'method'=> 'PUT',
            'Parameters'=> array(
                'query'=>array(
                    'id'=>array(
                        'description'=> 'user id (required)',
                        'data_type'=> 'int'
                    )
                ),
                'body'=>array(
                    'name'=>array(
                        'description'=> 'User name',
                        'data_type'=> 'string'
                    ),
                    'username'=>array(
                        'description'=> 'User login',
                        'data_type'=> 'string'
                    ),
                    'address'=>array(
                        'address1'=> array(
                            'description'=> 'User main address',
                            'data_type'=> 'string'
                        ),
                        'address2'=> array(
                            'description'=> 'User complement address',
                            'data_type'=> 'string'
                        ),
                        'city'=> array(
                            'description'=> 'User current city',
                            'data_type'=> 'string'
                        ),
                        'zipcode'=> array(
                            'description'=> 'Zipcode of the city',
                            'data_type'=> 'int'
                        ),

                    ),
                    'description'=>array(
                        'description'=> 'Provide a few words on user\'s activity',
                        'data_type'=> 'text'
                    ),
                    'logo'=>array(
                        'description'=> 'User avatar',
                        'data_type'=> 'image',
                        'mimeTypes'=> 'image/jpeg','image/jpg','image/png','image/gif'
                    )
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user update'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['delete'] = array(
            'notes'=> 'Request delete user with id {id}',
            'path'=> 'api/users/{id}',
            'method'=> 'DELETE',
            'Parameters'=> array(
                'query'=>array(
                    'id'=> array(
                      'description'=> 'user id (required)',
                      'data_type'=> 'int'
                  )
                ),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user update'
                ),
                'access denied'=> array(
                 'status code'=> '403',
                 'reason'=> 'forbidden removal on given user => not referent or not the user himself'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );
        $options['Change_password'] = array(
            'notes'=> 'Change password',
            'path'=> 'api/users/change-password',
            'method'=> 'PATCH',
            'Parameters'=> array(
                'query'=> array(),
                'body'=> array(
                    'current password'=> array(
                      'description'=> 'User current password (required)',
                      'data_type'=> 'string'
                    ),
                    'new password'=> array(
                      'description'=> 'User requested password (required)',
                      'data_type'=> 'string'
                    ),
                    'confirm new password'=> array(
                      'description'=> 'Confirm requested password (required)',
                      'data_type'=> 'string'
                    ),
                ),
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful password change'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );
        $options['associate_card'] = array(
            'notes'=> 'Associate a security card to a user',
            'path'=> 'api/users/{id}/associate-card',
            'method'=> 'PATCH',
            'Parameters'=> array(
                'query'=>array(
                    'id'=> array(
                        'description'=> 'user id (required)',
                        'data_type'=> 'int'
                    ),
                ),
                'body'=>array(
                    'current password'=> array(
                        'description'=> 'User current password (required)',
                        'data_type'=> 'string'
                    ),
                    'new password'=> array(
                        'description'=> 'User requested password (required)',
                        'data_type'=> 'string'
                    ),
                    'confirm new password'=> array(
                        'description'=> 'Confirm requested password (required)',
                        'data_type'=> 'string'
                    ),
             
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user creation'       
                ),
                'access denied'=> array(
                 'status code'=> '403',
                 'reason'=> 'forbidden removal on given user => not referent or not the user himself'
                ),
                'error'=> array(
                      'status code'=> '500',
                     'reason'=> 'error on server-side'           
                ),
            )
        );

        return new Response(json_encode($options));
    }                      

    /**
     * View the profile of $user
     *
     * What will be displayed on the screen will depend on the current user
     *
     * @param User $user User with profile to view
     */
    public function viewProfileAction(Request $request , User $user, $_format)                           
    {                                                                          
        $currentUser = $this->getUser();
        if( (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) && $user->hasRole('ROLE_SUPER_ADMIN')){
            throw new AccessDeniedException('Pas les droits nécessaires pour accéder au profil de cet utilisateur');
        } 

        if($_format == 'json'){
            $serializedUser = $this->get('cairn_user.api')->serialize($user);
            $response = new Response($serializedUser);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        return $this->render('CairnUserBundle:Pro:view.html.twig', array('user'=>$user));
    }                      



    /**
     *Removes a user with id given in query
     *
     * This operation is considered as super sensible. It needs the security layer + the password input
     * If the input is incorrect, user's attribute 'cardKeyTries' or 'passwordTries' is incremented. 3 failures leads to disable the user.
     *
     * A user can remove its own member area, or an admin can do it if he is a referent.
     *
     * If the user to remove is a ROLE_ADHERENT, we ensure that all his accounts have a balance to zero
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

        $userRepo = $em->getRepository('CairnUserBundle:User');

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        //check that account balances are all 0 (for PRO only)
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id,NULL);

        if($user->hasRole('ROLE_PRO') || $user->hasRole('ROLE_PERSON')){
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
                        $isRemoved = $this->removeUser($user, $currentUser);
                        $session->getFlashBag()->add('success','Espace membre supprimé avec succès');
                    }else{//is ROLE_PRO or ROLE_PERSON
                        $user->setRemovalRequest(true);
                        $user->setEnabled(false);

                        $redirection = 'fos_user_security_logout';
                        $session->getFlashBag()->add('success','Votre demande de suppression d\'espace membre a été prise en compte');
                    }
                    $em->flush();

                    return $this->redirectToRoute($redirection);
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
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $messageNotificator = $this->get('cairn_user.message_notificator');

        $referents = $user->getReferents();

        $saveName = $user->getName();

        $params = new \stdClass();
        $params->status = 'REMOVED';
        $params->user = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        try{
            $this->userManager->changeStatusUser($params);
            $emailTo = $user->getEmail();

            //remove beneficiaries associated to the user to remove
            $beneficiaries = $beneficiaryRepo->findBy(array('user'=>$user));
            foreach($beneficiaries as $beneficiary){
                $em->remove($beneficiary);
            }
            
            //TODO : ONE single SQL insert request instead of the two here
            //set Operations with user to remove as creditor/debitor to NULL
            $operations = $operationRepo->findBy(array('creditor'=>$user));
            foreach($operations as $operation){
                $operation->setCreditor(NULL);
            }

            $operations = $operationRepo->findBy(array('debitor'=>$user));
            foreach($operations as $operation){
                $operation->setDebitor(NULL);
            }

            $em->remove($user);

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

            return true;

        }catch(Cyclos\ServiceException $e){
            if($e->errorCode == 'VALIDATION'){
                $subject = 'Demande de suppression annulée';
                $from = $messageNotificator->getNoReplyEmail();
                $to = $user->getEmail();
                $body = 'Vous avez demandé à supprimer votre espace membre, mais certains de vos comptes ont un solde non nul. Elle n\'a donc pas pu être validée. Mettez vos comptes à 0, et vérifiez que vous ne recevez pas des virements entre la demande et la validation';//$this->renderView('CairnUserBundle:Emails:farwell.html.twig');

                $messageNotificator->notifyByEmail($subject,$from,$to,$body);
                return false;

            }else{
                throw $e;
            }
        }

    }

    /**
     * Removes definitively users that requested to be removed
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removePendingUsersAction(Request $request, $_format)
    {
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $currentUser = $this->getUser();

        //members on pending removal status
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereReferent($ub, $currentUser->getID());
        $listUsers = $ub
            ->andWhere('u.removalRequest = true')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        $form = $this->createForm(ConfirmationType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($form->get('save')->isClicked()){
                $notRemovedUsers = '';
                foreach($listUsers as $user){
                    $isRemoved = $this->removeUser($user, $currentUser);
                    if(!$isRemoved){
                        $user->setEnabled(true);
                        $user->setRemovalRequest(false);
                        $notRemovedUsers = $notRemovedUsers.', '.$user->getName();
                    }
                }

                $em->flush();

                if($notRemovedUsers != ''){
                    $session->getFlashBag()->add('info','Les membres suivants n\'ont pas pu être supprimés : ' .$notRemovedUsers); 
                }else{
                    $session->getFlashBag()->add('success','Tous les membres ont pas pu être supprimés avec succès'); 
                }
                return $this->redirectToRoute('cairn_user_users_home', array('_format'=>$_format));

            }else{
                $session->getFlashBag()->add('info','Opération annulée'); 
                return $this->redirectToRoute('cairn_user_users_home', array('_format'=>$_format));
            }
        }
        return $this->render('CairnUserBundle:Pro:confirm_remove_pending.html.twig',
            array('form'=>$form->createView(),'listUsers'=>$listUsers));

    }

}
