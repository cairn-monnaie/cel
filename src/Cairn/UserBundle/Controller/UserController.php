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
use Cairn\UserBundle\Entity\SmsData;
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
use Cairn\UserBundle\Form\SmsDataType;
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
     * Changes the current user's sms data
     *
     * This action permits to change current user's sms data, such as phone number, or status enabled/disabled
     */
    public function editSmsDataAction(Request $request, User $user)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();

        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);

        //****************** All cases where edit sms is not allowed ****************//
        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        if(! ($user->hasRole('ROLE_PRO') || $user->hasRole('ROLE_PERSON')) ){
            throw new AccessDeniedException('Réserver aux comptes adhérents');
        }

        if(! $user->getCard()){
            $session->getFlashBag()->add('error','Pas de carte de sécurité associée !');
            return $this->redirectToRoute('cairn_user_profile_view',array('username'=> $user->getUsername()));
        }


        $smsData = $user->getSmsData();

        $previousPhoneNumbers = $user->getPhoneNumbers();


        //cas à gérer : un ADMIN veut modifier l'ID SMS d'un PRO qui n'a pas renseigné de numéro de téléphone
        if( $user->hasRole('ROLE_PRO') && $isAdmin && ! (count($user->getSmsData()) == 0) ){
            $session->getFlashBag()->add('info','Ce professionnel n\'a saisi de coordonnées SMS');
            return $this->redirectToRoute('cairn_user_profile_view',array('id' => $user->getID()));
        }

        if($currentUser->getNbPhoneNumberRequests() >= 3 && !$session->get('activationCode')){
            $session->getFlashBag()->add('info','Vous avez déjà effectué 3 demandes de changement de numéro de téléphone sans validation... Cette action vous est désormais inaccessible');
            return $this->redirectToRoute('cairn_user_profile_view',array('username'=>$currentUser->getUsername()));
        }

        //************************ end of cases where edit sms is disallowed *************************//

        $formSmsData = $this->createForm(SmsDataType::class, $user);
        $formCode = $this->createFormBuilder()
            ->add('code', PasswordType::class,array('label'=>'Code de validation'))
            ->add('save', SubmitType::class,array('label'=>'Valider'))
            ->getForm();

        $formSmsData->handleRequest($request);
        if($formSmsData->isSubmitted() && $formSmsData->isValid()){

            if($previousPhoneNumber != $smsData->getPhoneNumber()){ //request is new phone number
                if($user !== $currentUser ){
                    throw new AccessDeniedException('Action réservée à '.$user->getName());
                }

                if($currentUser->getNbPhoneNumberRequests() >= 3){
                    $session->getFlashBag()->add('info','Vous avez déjà effectué 3 demandes de changement de numéro de téléphone sans validation... Cette action vous est désormais inaccessible');
                    return $this->redirectToRoute('cairn_user_profile_view',array('username'=>$currentUser->getUsername()));
                }

                //give a new code to be validated
                if($this->getParameter('kernel.environment') != 'prod'){
                    $code = 1111;
                }else{
                    $code = rand(1000,9999);
                }
    
                $session_code = $encoder->encodePassword($code,$currentUser->getSalt());
                $session->set('activationCode', $session_code);
    
                $currentUser->setNbPhoneNumberRequests($currentUser->getNbPhoneNumberRequests() + 1);
    

                //at this step, flush is impossible because entitymanager would consider $smsData object as a brand new object
                //then, an exception would be thrown due to unique key constraint error
                //that's why we retrieve the instance right away and refresh it to not flush modifications, as it should be done only
                //after code validation from the second form
                $newPhoneNumber = $smsData->getPhoneNumber(); //let's save it before refresh

                if($previousPhoneNumber){ //otherwise, nothing to refresh
                    //detach is necessary for serialization of associated entities before setting it in session
                    $em->detach($smsData);
                    $session->set('smsData',$smsData);
                    $session->set('hasPreviousPhoneNumber',true);
                    $smsData = $em->merge($session->get('smsData'));
                    $em->refresh($smsData);
                }else{
                    $session->set('hasPreviousPhoneNumber',false);
                    $session->set('smsData',$this->get('cairn_user.api')->serialize($smsData));
                }

                // send SMS with validation code to current user's new phone number
                $this->get('cairn_user.message_notificator')->sendSMS($newPhoneNumber,'Code de validation de votre téléphone '.$code);
                $em->flush();

                $existSmsData = $em->getRepository('CairnUserBundle:SmsData')->findOneBy(array('phoneNumber'=>$newPhoneNumber));
                if($existSmsData){
                    $session->getFlashBag()->add('info', 'Ce numéro sera associé à un compte professionnel et un compte particulier. Seul le compte particulier pourra réaliser des opérations par SMS');
                }
    
                $session->getFlashBag()->add('success','Un code vous a été envoyé par SMS au ' .$newPhoneNumber.'. Saisissez-le pour valider vos nouvelles données SMS');
                return $this->render('CairnUserBundle:User:change_sms_data.html.twig',
                    array('formSmsData'=>$formSmsData->createView(),
                          'formCode'=>$formCode->createView()));
    
            }else{//just change ID SMS Means that phoneNumber was already associated and $smsData is not new

                if($smsData->isSmsEnabled() ){
                    $session->getFlashBag()->add('info','Les opérations SMS sont autorisées pour le numéro '.$smsData->getPhoneNumber());
                }else{
                    $session->getFlashBag()->add('info','Les opérations SMS n\'ont pas été autorisées pour le numéro '.$smsData->getPhoneNumber());
                }

                $em->flush();
                $session->getFlashBag()->add('success','Nouvelles données SMS enregistrées ! ');
                return $this->redirectToRoute('cairn_user_profile_view',array('username'=> $user->getUsername()));
            }

         }

        $formCode->handleRequest($request);
        if($formCode->isSubmitted() && $formCode->isValid()){
            $providedCode = $formCode->getData()['code'];
            
            $session_code = $session->get('activationCode');


            //no activation code proposed (means that no phone number association requested)
            if(!$session_code){
                $session->getFlashBag()->add('info','Aucune demande d\'ajout de numéro de téléphone enregistrée');
                return new RedirectResponse($request->getRequestUri());
            }

            //valid code
            if($encoder->encodePassword($providedCode,$user->getSalt()) == $session_code){

                $hasPreviousPhoneNumber = $session->get('hasPreviousPhoneNumber');

                if($hasPreviousPhoneNumber){
                    $smsData = $em->merge($session->get('smsData'));
                   
                }else{
                    $res = $this->get('cairn_user.api')->deserialize($session->get('smsData'),'Cairn\UserBundle\Entity\SmsData');
                   $smsData = $em->merge($res);
                }

                $user->setNbPhoneNumberRequests(0);
                $user->setPhoneNumberActivationTries(0);


                $accessClientVO = $this->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($user->getCyclosID(),array('BLOCKED','ACTIVE'));
                if(! $accessClientVO){
                    $securityService = $this->get('cairn_user.security');
                    $securityService->createAccessClient($user,'client_sms');
                    $accessClientVO = $this->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($user->getCyclosID(),'UNASSIGNED');

                    $smsClient = $securityService->changeAccessClientStatus($accessClientVO,'ACTIVE');
                    $smsClient = $securityService->vigenereEncode($smsClient);
                    $smsData->setSmsClient($smsClient);

                    //by default, access client is blocked and must be unblocked while enabling sms operations
//                    $securityService->changeAccessClientStatus($accessClientVO,'BLOCKED');
                }

                 //if user had a phone number before, we check if there was a pro & personal number associated
                 //if so, we warn the user that, right now, SMS payment is possible for pro
                if($hasPreviousPhoneNumber){
                    $existingUsers = $em->getRepository('CairnUserBundle:User')->findUsersByPhoneNumber($previousPhoneNumber);
                    if(count($existingUsers) == 2){
                        $session->getFlashBag()->add('info','Le compte professionnel associé au numéro '.$previousPhoneNumber. ' peut désormais réaliser des opérations par SMS');
                    }
                }
                $em->persist($user);
                $smsData->setUser($user);
                $user->setSmsData($smsData);
                $em->persist($smsData);
                $em->flush();

                $session->remove('activationCode');
                $session->remove('hasPreviousPhoneNumber');
                $session->getFlashBag()->add('success','Nouvelles données SMS enregistrées ! ');
                return $this->redirectToRoute('cairn_user_profile_view',array('username'=> $user->getUsername()));

            //invalid code
            }else{
                $user->setPhoneNumberActivationTries($user->getPhoneNumberActivationTries() + 1);
                $remainingTries = 3 - $user->getPhoneNumberActivationTries();
                if($remainingTries > 0){
                    $session->getFlashBag()->add('error','Code invalide : Veuillez réessayer. Il vous reste '.$remainingTries.' essais avant le blocage du compte');
                }else{
                    $body = 'Vous avez commis 3 erreurs en tentant de valider votre nouveau de numéro de téléphone. Pour des raisons de sécurité, votre compte e-Cairn a été bloqué. Veuillez contacter l\'Association pour résoudre le problème.';
                    $this->get('cairn_user.access_platform')->disable(array($user),'Compte e-Cairn bloqué',$body);
                    $session->getFlashBag()->add('error','Trop d\'échecs : votre compte a été bloqué.');
                }

                $em->flush();
                return new RedirectResponse($request->getRequestUri());

            }
        }

        return $this->render('CairnUserBundle:User:change_sms_data.html.twig',
            array('formSmsData'=>$formSmsData->createView(),
                  'formCode'=>$formCode->createView()));
    }

    /**
     *Get the list of all users grouped by roles
     *
     */
    public function listUsersAction(Request $request, $_format)
    {
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

        $personVisitingPro = ($currentUser->hasRole('ROLE_PERSON') && $user->hasRole('ROLE_PRO'));

        if(! ( ($user === $currentUser) || $user->hasReferent($currentUser) ) ){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }

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

    public function downloadIdentityDocumentAction(Request $request, User $user)
    {
        $currentUser = $this->getUser();

        if(! ( ($user === $currentUser) || $user->hasReferent($currentUser) ) ){
            throw new AccessDeniedException('Ce document n\'existe pas');
        }

        $id_doc = $user->getIdentityDocument();

        $env = $this->getParameter('kernel.environment');
        return $this->file($id_doc->getWebPath($env), 'piece-identite_'.$user->getUsername().'.'.$id_doc->getUrl());
    }

    /**
     * Set the enabled attribute of user with provided ID to false
     *
     * An email is sent to the user being blocked
     *
     * @throws  AccessDeniedException Current user making request is not a referent of the user being involved
     * @Method("GET")
     */ 
    public function blockUserAction(Request $request, User $user)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $currentUser = $this->getUser();

        if(! ( ($user === $currentUser) || $user->hasReferent($currentUser) ) ){
            throw new AccessDeniedException('Pas les droits nécessaires');
        }elseif(!$user->isEnabled()){
            $session->getFlashBag()->add('info','L\'espace membre de ' . $user->getName() . ' est déjà bloqué.');
            return $this->redirectToRoute('cairn_user_profile_view',array('username'=> $user->getUsername()));
        }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){

                $subject = 'Opposition à votre compte e-Cairn';
                $body = 'Une opposition de votre compte a été réalisée par' .$currentUser->getName();

                $this->get('cairn_user.access_platform')->disable(array($user),$subject,$body);
                $session->getFlashBag()->add('success','L\'opposition du compte de ' . $user->getName() . ' a été effectuée avec succès. Il ne peut plus accéder à la plateforme.');
                $em->flush();
            }

            return $this->redirectToRoute('cairn_user_profile_view',array('username'=> $user->getUsername()));

        }

        $responseArray = array('user' => $user,'form'=> $form->createView());

        return $this->render('CairnUserBundle:User:block.html.twig', $responseArray);
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
        $currentUser = $this->getUser();

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $messageNotificator = $this->get('cairn_user.message_notificator');

        $userRepo = $em->getRepository('CairnUserBundle:User');

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        //check that account balances are all 0 (for adherents -PRO/PERSON only)
        try{ 
            $ownerVO = $this->get('cairn_user_cyclos_user_info')->getUserVO($user->getCyclosID());
        }catch(\Exception $e){                                     
            if( $e->errorCode == 'ENTITY_NOT_FOUND'){ //user has registered but never activated

                $emailTo = $user->getEmail();
                $em->remove($user);
                $em->flush();

                $subject = 'Ouverture de compte [e]-Cairn refusée';
                $from = $messageNotificator->getNoReplyEmail();
                $to = $emailTo;
                $body = $this->renderView('CairnUserBundle:Emails:opening_refused.html.twig');
    
                $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                $session->getFlashBag()->add('success','L\'ouverture de compte de '.$user->getName().' a été refusée. Compte clôturé');
                return $this->redirectToRoute('cairn_user_users_dashboard');
            }else{
                throw $e;
            }
        }

        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id,NULL);

        if($user->hasRole('ROLE_PRO') || $user->hasRole('ROLE_PERSON')){
            foreach($accounts as $account){
                if($account->status->balance != 0){
                    $session->getFlashBag()->add('error','Certains comptes ont un solde non nul. La clôture du compte ne peut aboutir.');
                    return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format,'username'=> $user->getUsername()));
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
                    if($currentUser->isAdmin()){
                        $redirection = 'cairn_user_users_dashboard';
                        $isRemoved = $this->removeUser($user, $currentUser);

                        if($isRemoved){
                            $session->getFlashBag()->add('success','Espace membre supprimé avec succès');
                        }else{
                            $session->getFlashBag()->add('success','La fermeture de compte a échoué. '.$user->getName(). 'a un compte non soldé');
                            return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format,'username'=> $user->getUsername()));
                        }
                    }else{//is ROLE_PRO or ROLE_PERSON : $user == $currentUser
                        $user->setRemovalRequest(true);
                        $this->get('cairn_user.access_platform')->disable(array($user));

                        $redirection = 'fos_user_security_logout';
                        $session->getFlashBag()->add('success','Votre demande de clôture d\'espace membre a été prise en compte');
                    }

                    $em->flush();

                    return $this->redirectToRoute($redirection);
                }
                else{
                    $session->getFlashBag()->add('info','La demande de clôture a été annulée.');
                    return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format,'username'=> $user->getUsername()));
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
        $isPro = $user->hasRole('ROLE_PRO');

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
            $body = $this->renderView('CairnUserBundle:Emails:farwell.html.twig',array('currentUser'=>$currentUser));

            $messageNotificator->notifyByEmail($subject,$from,$to,$body);

            if($isPro){
                $subject = 'Un professionnel a été supprimé de la plateforme';
                $body = $saveName .' a été supprimé de la plateforme par '. $currentUser->getName();
                foreach($referents as $referent){
                    $to = $referent->getEmail();
                    $messageNotificator->notifyByEmail($subject,$from,$to,$body);
                }
            }

            return true;

        }catch(\Exception $e){

            if( ($e instanceof Cyclos\ServiceException) && ($e->errorCode == 'VALIDATION')){

                $errors = $e->error->validation->allErrors;
                for($i = 0; $i < count($errors); $i++){
                    if( strpos( $errors[$i], 'has a non-zero balance') !== false ){
                        return false;
                    }
                }
                throw $e;
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
        $messageNotificator = $this->get('cairn_user.message_notificator');

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
                        $user->setRemovalRequest(false);
                        $notRemovedUsers = $notRemovedUsers.', '.$user->getName();

                        $subject = 'Demande de clôture non aboutie';
                        $from = $messageNotificator->getNoReplyEmail();
                        $to = $user->getEmail();
                        $body = 'Votre demande de clôture de compte [e]-Cairn n\'a pas pu aboutir. Vérifiez que votre compte est bien soldé. Si oui, veuillez prendre contact avec l\'Association.'."\n"."\n".'Le Cairn,';
            
                        $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                    }
                }

                $em->flush();

                if($notRemovedUsers != ''){
                    $session->getFlashBag()->add('info','Les comptes des membres suivants n\'ont pas pu être supprimés : ' .$notRemovedUsers); 
                    $session->getFlashBag()->add('info','Raison : Leurs comptes ne sont probablement plus soldés, même s\'ils l\'étaient au moment de leur demande'); 
                }else{
                    $session->getFlashBag()->add('success','Tous les comptes ont pu être clôturés avec succès'); 
                }
                return $this->redirectToRoute('cairn_user_users_dashboard');

            }else{
                $session->getFlashBag()->add('info','Demande de clôture annulée'); 
                return $this->redirectToRoute('cairn_user_users_dashboard');
            }
        }
        return $this->render('CairnUserBundle:Pro:confirm_remove_pending.html.twig',
            array('form'=>$form->createView(),'listUsers'=>$listUsers));

    }

}
