<?php
// src/Cairn/UserBundle/EventListener/SecurityListener.php

namespace Cairn\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse; 
use Symfony\Component\HttpFoundation\Response;

use Cairn\UserBundle\Event\InputCardKeyEvent;
use Cairn\UserBundle\Event\InputPasswordEvent;

use Symfony\Component\DependencyInjection\Container;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\LoginManager;
use Cairn\UserCyclosBundle\Entity\PasswordManager;

use Cairn\UserBundle\Entity\User;

use Cairn\UserBundle\Event\SecurityEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Cyclos;

/**
 * This class contains called functions when events defined in Event\SecurityEvents are dispatched
 *
 */
class SecurityListener
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * On password resetting, we check if user is enabled or not
     *
     * A disabled user cannot ask for password resetting. 
     * Plus, if the user is enabled but has never logged in, it means that he lost the password sent by email. This is considered as a 
     * sensible case and the user is disabled 
     */
    public function onResetPasswordInit(GetResponseNullableUserEvent $event)
    {
        $userManager = new UserManager();
        $user = $event->getUser();
        $session = $event->getRequest()->getSession();
        $router = $this->container->get('router');          

        if(! $user){ return;}

        if(! $user->isEnabled()){
            $session->getFlashBag()->add('error','Ce compte est bloqué. L\'opération ne peut donc être poursuivie.');
            $logoutUrl = $router->generate('fos_user_security_logout');
            $event->setResponse(new RedirectResponse($logoutUrl));
            return;
        }
    }

    public function changeCyclosPassword($old, $new, $user)
    {
        $passwordManager = new PasswordManager();
        $anonymous = $this->container->getParameter('cyclos_anonymous_user');

        try{
            $credentials = array('username'=>$anonymous,'password'=>$anonymous);
            $this->loginPaymentApp('login',$credentials);

            $passwordManager->changePassword($old, $new, $user->getCyclosID());
            return true;
        }catch(\Exception $e){
            if($e instanceof Cyclos\ServiceException){
                if($e->errorCode == 'VALIDATION'){
                    for($i = 0; $i < count($e->error->validation->allErrors); $i++){
                        if(strpos($e->error->validation->allErrors[$i],'only these characters') !== false){
                            //retourner à la page précédente
                            $request = $this->container->get('request_stack')->getCurrentRequest();
                            $request->getSession()->getFlashBag()->add('error','Votre mot de passe contient un caractère non traité');
                            $event->setResponse(new RedirectResponse($request->getRequestUri()));
                        }
                    }
                }
            }
            throw $e;
        }
    }


    /**
     * Reset user password on Cyclos side after it has been changed in our app
     *
     */
    public function onResetPasswordSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $user = $form->getData();

        $session = $event->getRequest()->getSession();
        $templating = $this->container->get('templating');          

        $apiService = $this->container->get('cairn_user.api');
        $router = $this->container->get('router');          
        $profileUrl = $router->generate('cairn_user_profile_view',array('username'=>$user->getUsername()));
        $smsUrl = $router->generate('cairn_user_sms_presentation');


        $anonymous = $this->container->getParameter('cyclos_anonymous_user');

        //get username and password from form request
        $credentials = array('username'=>$anonymous,'password'=>$anonymous);

        $networkInfo = $this->container->get('cairn_user_cyclos_network_info');          
        $networkName= $this->container->getParameter('cyclos_currency_cairn');          
        $networkInfo->switchToNetwork($networkName,'login',$credentials);

        $newPassword = $form->get('plainPassword')->getData();

        if(! $user->getCyclosToken()){
            $changed = $this->changeCyclosPassword(NULL, $newPassword, $user);
        }
        $this->createAccessClient($user,$user->getUsername(),$newPassword);

        if($user->isFirstLogin()){
            $user->setFirstLogin(false);
            $session->set('is_first_connection',true); 
        }
        
        if($apiService->isRemoteCall()){
            $response = $apiService->getOkResponse(array('Password resetted successfully'),Response::HTTP_OK);
            $event->setResponse($response);
        }else{
            if($session->get('is_first_connection')){
                $event->setResponse(new RedirectResponse($smsUrl));
            }else{
                $event->setResponse(new RedirectResponse($profileUrl));
            } 
        }
    }

    /**
     * Changes user password on Cyclos side after it has been changed in our app
     *
     */
    public function onChangePassword(FormEvent $event)
    {
        $form = $event->getForm();
        $user = $form->getData();

        $apiService = $this->container->get('cairn_user.api');
        $isRemoteCall = $apiService->isRemoteCall();

        $templating = $this->container->get('templating');          
        $router = $this->container->get('router');

        $profileUrl = $router->generate('cairn_user_profile_view',array('username'=>$user->getUsername()));

        if($isRemoteCall){
            if($user->isFirstLogin()){
                $user->setFirstLogin(false);
            }
            $response = $apiService->getOkResponse(array('Password updated successfully'),Response::HTTP_OK);
            $event->setResponse($response);
        }else{
             if($user->isFirstLogin()){
                $user->setFirstLogin(false);
                $event->setResponse($templating->renderResponse('CairnUserBundle:Default:howto_sms_page.html.twig'));
             }else{
                $event->setResponse(new RedirectResponse($profileUrl));
             }
        }
    }

    public function createAccessClient(User $currentUser,$username, $password)
    {
        $securityService = $this->container->get('cairn_user.security');

        if(! $currentUser->getCyclosToken()){
                $cyclosClientName = 'main';
                $securityService = $this->container->get('cairn_user.security');

                $this->loginPaymentApp('login',array('username'=>$username,'password'=>$password));

                $securityService->createAccessClient($currentUser,$cyclosClientName);
                $accessClientVO = $this->container->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($currentUser->getCyclosID(),$cyclosClientName, 'UNASSIGNED');

                $mainClient = $securityService->changeAccessClientStatus($accessClientVO,'ACTIVE');
                $mainClient = $securityService->vigenereEncode($mainClient);
                $currentUser->setCyclosToken($mainClient);
        }
    }

    public function onLogin(InteractiveLoginEvent $event)
    {
        $request = $event->getRequest();
        $securityService = $this->container->get('cairn_user.security');
        $currentUser = $securityService->getCurrentUser();

        //in case of authentication with API token
        if(! $this->container->get('cairn_user.api')->isApiCall()){

            $username = $request->request->all()['_username'];
            $password = $request->request->all()['_password'];

            //if there is an access client, connect with it. Otherwise create one
            $this->createAccessClient($currentUser,$username,$password);
            
            $this->loginPaymentApp('access_client',$securityService->vigenereDecode($currentUser->getCyclosToken()));
        }
    }

    protected function loginPaymentApp($type,$credentials)
    {
        $networkInfo = $this->container->get('cairn_user_cyclos_network_info');          
        $networkName= $this->container->getParameter('cyclos_currency_cairn');          

        $networkInfo->switchToNetwork($networkName,$type,$credentials);
    }

    /**
     *On each controller action, we reconnect to Cyclos using the cyclos session token saved
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $securityService = $this->container->get('cairn_user.security');
        $currentUser = $securityService->getCurrentUser();
        
        if($currentUser){
            $this->loginPaymentApp('access_client',$securityService->vigenereDecode($currentUser->getCyclosToken()));
        }
    }

    
    /**
     *Deals with maintenance of the application
     *
     *If the appplication is in maintenance state, any request is redirected to maintenance page. This action is called before any request
     is achieved
     */
    public function onMaintenance(GetResponseEvent $event)
    {
        $templating = $this->container->get('templating');          

        $request = $event->getRequest();

        $apiService = $this->container->get('cairn_user.api');

        //if maintenance.txt exists
        if(is_file('maintenance.txt')){
            if($apiService->isRemoteCall()){
                $event->setResponse($apiService->getErrorResponse(array('Server in maintenance state'),Response::HTTP_SERVICE_UNAVAILABLE));
                return;
            }
            $event->setResponse($templating->renderResponse('CairnUserBundle:Security:maintenance.html.twig'));
            return;
        }

        if($apiService->isMobileCall()){
            $authHeader = $request->headers->get('authorization');
            
            if(! $authHeader){
                $authHeader = $request->server->get('HTTP_AUTHORIZATION');

                if(! $authHeader){
                    $event->setResponse($apiService->getErrorResponse(array('Missing Authorization header with Signature'),Response::HTTP_UNAUTHORIZED));
                    return;
                }
            }

            $parsedHeader = $this->container->get('cairn_user.security')->parseAuthorizationHeader($authHeader);

            if(! $parsedHeader){
                $event->setResponse($apiService->getErrorResponse(array('Wrong Authorization Header Format'),Response::HTTP_UNAUTHORIZED));
                return;
            }

            $data = $parsedHeader['timestamp'].$request->getMethod().$request->getRequestURI();

            $body = preg_replace('/\s+/','',$request->getContent());
            if($body){
                $deterBody = $apiService->fromArrayToStringDeterministicOrder(json_decode($body,true));

                $digest = hash('md5',$deterBody);
                $data .= $digest;
            }
            
            $rightKey = hash_hmac($parsedHeader['algo'],trim($data),$this->container->getParameter('api_secret'));

            if($rightKey != $parsedHeader['signature']){
                $event->setResponse($apiService->getErrorResponse(array('Wrong Authorization Header provided'),Response::HTTP_UNAUTHORIZED));
                return;
            }
        }
    }

    /**
     * If user never logged in, he is automatically redirected to change password page
     *
     */
    public function onFirstLogin(GetResponseEvent  $event)
    {
        $security = $this->container->get('cairn_user.security');          
        $router = $this->container->get('router');          

        $currentUser = $security->getCurrentUser();

        if($currentUser instanceof \Cairn\UserBundle\Entity\User){
            if($currentUser->isFirstLogin() && (!in_array($event->getRequest()->get('_route'),['fos_user_change_password','cairn_user_api_users_change_password']))){
                $session = $event->getRequest()->getSession();

                $session->set('is_first_connection',true);
                $editPwdUrl = $router->generate('fos_user_change_password');
                $event->setResponse(new RedirectResponse($editPwdUrl));
            }
        }
    }

    /**
     *A disabled user is redirected to logout page
     *
     */
    public function onDisabledUser(FilterResponseEvent $event)
    {
        $security = $this->container->get('cairn_user.security');          
        $router = $this->container->get('router');          

        $currentUser = $security->getCurrentUser();

        if($currentUser instanceof \Cairn\UserBundle\Entity\User){
            if(!$currentUser->isEnabled()){
                $apiService = $this->container->get('cairn_user.api');
                
                $route = $event->getRequest()->get('_route');

                if( (! $apiService->isRemoteCall()) && ($route != 'fos_user_security_login')){
                    $loginUrl = $router->generate('fos_user_security_login');
                    $event->setResponse(new RedirectResponse($loginUrl));
                }
            }
        }
    }

    /**
     * Checks if current request matches a sensible operation or not
     *
     *Compares the current request with sensible routes and urls defined in Event/SecurityEvents. If a match is found, a redirection 
     *to card security input is operated. The only case where redirection should not be done is if the admin created at installation needs
     *a new security card : he would need his own card(which he is asking for..)  to authentificate, and he has no referent but himself.
     *
     */
    public function onSensibleOperations(GetResponseEvent $event)
    {
        $security = $this->container->get('cairn_user.security');          
        $em = $this->container->get('doctrine.orm.entity_manager');          
        $router = $this->container->get('router');          

        $request = $event->getRequest();

        $currentUser = $security->getCurrentUser();

        $userRepo = $em->getRepository('CairnUserBundle:User');
        $route = $request->get('_route');

        $attributes = $request->attributes->all();
        $route_parameters = key_exists('_route_params', $attributes) ? $attributes['_route_params'] : array();
        $query_parameters = $request->query->all();
        $parameters = array_merge($route_parameters, $query_parameters);

        $isExceptionCase = false;
        //check if installed admin is asking for a new security card
        if($currentUser instanceof \Cairn\UserBundle\Entity\User){
            if(($currentUser->hasRole('ROLE_SUPER_ADMIN') && $route == 'cairn_user_card_download')){
                //for himself ? for someone else ?
                $toUser = $userRepo->findOneBy(array('username'=>$parameters['username']));
                if($toUser === $currentUser){
                    $isExceptionCase = true;
                }
            }
            if(($currentUser->isFirstLogin() && $route == 'fos_user_change_password')){
                $isExceptionCase = true;
            }
            if( ($route == 'cairn_user_users_phone_add') &&  $request->getSession()->get('is_first_connection')){
                $isExceptionCase = true;
            }

        }

        if(!$isExceptionCase){
            if($security->isSensibleOperation($route, $parameters)){
                if(!$request->getSession()->get('has_input_card_key_valid')){
                    $cardSecurityLayer = $router->generate('cairn_user_card_security_layer',array('url'=>$request->getRequestURI()));
                    $event->setResponse(new RedirectResponse($cardSecurityLayer));
                }
            }
        }

    }


    /**
     * Deals with the input card key event
     *
     * When a card key input is required, we follow the steps below :
     *     _compare the input with the real user's card key
     *     _if failure, clear the entityManager from all persistance then increment user's attribute 'cardKeyTries'
     *     _if 3 cardKeyTries : disable the user
     *     _if success : reinitialize tries
     */
    public function onCardKeyInput(InputCardKeyEvent $event)
    {
        $encoderFactory = $this->container->get('security.encoder_factory');          
        $counter = $this->container->get('cairn_user.counter');          
        $accessPlatform = $this->container->get('cairn_user.access_platform');          
        $em = $this->container->get('doctrine.orm.entity_manager');          

        $session = $event->getSession();
        $user = $event->getUser();
        $currentCard = $user->getCard();
        $salt = $currentCard->getSalt();                                       

        $encoder = $encoderFactory->getEncoder($user);                         

        $cardKey = $event->getCardKey();
        $position = $event->getPosition();


        $field_value = $currentCard->getKey($position);

        if($field_value == substr($encoder->encodePassword($cardKey,$salt),0,4)){
            $counter->reinitializeTries($user,'cardKey');

            if($session){
                $session->set('has_input_card_key_valid',true);
            }
        }
        else{
            $counter->incrementTries($user,'cardKey');

            if($user->getCardKeyTries() > 2){
                $accessPlatform->disable(array($user),'card_tries_exceeded');
                $event->setRedirect(true);
            }
        }
        $em->flush();
    }

}
