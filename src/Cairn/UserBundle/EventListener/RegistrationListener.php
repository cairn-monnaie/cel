<?php
// Cairn/UserBundle/EventListener/RegistrationListener.php

namespace Cairn\UserBundle\EventListener;

use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Event\GetResponseUserEvent;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\Config\Definition\Exception\Exception;

use Cairn\UserBundle\Entity\User;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\Card;

/**
 * This class contains called functions when FOSUserBundle events related to registration are dispatched
 *
 * Overriding the whole Registration controller can be discussed here, instead of listening to all steps of registration events
 */
class RegistrationListener 
{

    protected $userManager;
    protected $container;

    public function __construct(Container $container)                                              
    {                                                                          
        $this->userManager = new UserManager();                                
        $this->container = $container;
    }       

    /**
     * On profile edit, the Cyclos user profile is edited accordingly
     *
     */
    public function onProfileEditSuccess(FormEvent $event)
    {
        $form = $event->getForm();
        $user = $form->getData();

        $userVO = $this->container->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        if($userVO){
            $userDTO = $this->container->get('cairn_user_cyclos_user_info')->getUserDTO($userVO->id);
            $userDTO->name = $user->getName();
            $userDTO->username = $user->getUsername();
            $userDTO->email = $user->getEmail();

            $this->userManager->editUser($userDTO);                          
        }

        $response = $this->container->get('cairn_user.api')->getRedirectionResponse('cairn_user_profile_view',['username'=>$user->getUsername()], $user,Response::HTTP_CREATED);
        $event->setResponse($response);
    }


    /**
     * Applies some actions on new registered user at confirmation
     *
     * By default, at email confirmation, the user is enabled. We want an explicit authorization from referents,so we disable the user.
     * Plus, we add default referents(super_admin) 
     *@TODO : if new user is ROLE_SUPER_ADMIN : assign as referent of all ROLE_PRO and ROLE_ADMIN
     */
    public function onRegistrationConfirm(GetResponseUserEvent $event)
    {
        $messageNotificator = $this->container->get('cairn_user.message_notificator');

        $security = $this->container->get('cairn_user.security');

        $user = $event->getUser();
        $user->setEnabled(false);

        //this should be unnecessary
        $user->setConfirmationToken(null);

        $subject = 'Adresse mail [e]-Cairn confirmée';                      
        $from = $messageNotificator->getNoReplyEmail();                    
        $to = $user->getEmail();                                                      
        $body = $this->container->get('templating')->render('CairnUserBundle:Emails:pending_validation.html.twig',
            array('user'=>$user));

        $messageNotificator->notifyByEmail($subject,$from,$to,$body);      

        $messages = ['key'=>'email_validation','args'=>[$user->getEmail()]];

        $response = $this->container->get('cairn_user.api')->getRedirectionResponse('fos_user_security_login',[], $user,Response::HTTP_CREATED,$messages);
        $event->setResponse($response);
    }


    /**
     *Sets the role of the future user before binding the form
     *
     *User's role is set before binding the form because, depending on it, the registration form will display some fields or not
     *
     */
    public function onRegistrationInitialize(UserEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        $type = NULL;
        $apiService = $this->container->get('cairn_user.api');

        $isRemoteCall = $apiService->isRemoteCall();

        if($isRemoteCall){
            $type = $request->query->get('type');
        }else{
            $type = ($request->query->get('type')) ? $request->query->get('type') : $session->get('registration_type') ;
        }

        $session->set('registration_type',$type);

        $currentUser = $this->container->get('cairn_user.security')->getCurrentUser();

        if($currentUser && !$currentUser->isAdmin()){
            $response = $apiService->getErrorsResponse([],[],Response::HTTP_FORBIDDEN);
            $event->setResponse($response);
            return;
        }

        if(!$currentUser && ($type != 'person') && ($type != 'pro')  ){
            $session->set('registration_type','person');
            $type = 'person';
        }

        $user = $event->getUser();

        $user->setPlainPassword(User::randomPassword());
        switch ($type){
        case 'person':
            $user->addRole('ROLE_PERSON');
            break;
        case 'pro':
            $user->addRole('ROLE_PRO');
            break;
        case 'localGroup':
            $user->addRole('ROLE_ADMIN');
            break;
        case 'superAdmin':
            $user->addRole('ROLE_SUPER_ADMIN');
            break;
        default:
            $session->set('registration_type','person');
            break;
        }
    }

    /**
     *Once the registration form is valid, this function sets up a fake Cyclos ID and Doctrine user
     *
     * Note: FOSUserBundle EmailConfirmationListener is also listening to this event. Then, as we want to master the response in case of
     * API call, this function must be called in the end (piority defined in services.yml)
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $router = $this->container->get('router');          

        $user = $event->getForm()->getData();

        $user->setMainICC(null);

        $security = $this->container->get('cairn_user.security');
        $security->assignDefaultReferents($user);

        $currentUser = $this->container->get('cairn_user.security')->getCurrentUser();

        $apiService = $this->container->get('cairn_user.api');
        if($currentUser && $currentUser->hasRole('ROLE_SUPER_ADMIN')){
            //very important to let it to false in order to create cyclos user at activation
            $user->setEnabled(false);

            //this should be unnecessary
            $user->setConfirmationToken(null);
            $response = $apiService->getRedirectionResponse('cairn_user_profile_view',['username'=>$user->getUsername()], $user,Response::HTTP_CREATED);

            $event->setResponse($response);
            return;
        }

        if($event->getRequest()->get('_format') == 'json'){
            $apiData = '{ "data": '.$apiService->serialize($user).'}';
            $event->setResponse($apiService->getApiResponse($apiData,Response::HTTP_CREATED));
        }
    }

    public function onRegistrationFailure(FormEvent $event)
    {
        $apiService = $this->container->get('cairn_user.api');

        if($apiService->isRemoteCall()){
            $response = $apiService->getFormResponse('',['form' => $event->getForm()->createView()],$event->getForm());
            $event->setResponse($response);
        }
    }

} 
