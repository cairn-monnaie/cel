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

    public function onResetPassword(GetResponseNullableUserEvent $event)
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

        if(!$user->getLastLogin() ){
            $session->getFlashBag()->add('error','Vous ne pouvez pas changer de mot de passe car aucune connexion n\'a été enregistrée. Votre compte a été bloqué car il peut s\'agir d\'une tentative d\'usurpation .');
            $logoutUrl = $router->generate('fos_user_security_logout');
            $event->setResponse(new RedirectResponse($logoutUrl));

            $body = 'Une demande de changement de mot de passe a été effectuée avant même votre première connexion sur votre compte. Pour des raisons de sécurité, votre compte a été bloqué car il peut s\'agir d\'une tentative d\'usurpation . Veuillez contacter l\'Association';
            $this->container->get('cairn_user.access_platform')->disable(array($user),'Changement de mot de passe',$body);
            $this->container->get('doctrine.orm.entity_manager')->flush();

            return;
        }
    }

    public function onChangePassword(FormEvent $event)
    {
        $passwordManager = new PasswordManager();
        $form = $event->getForm();
        $user = $form->getData();

        $router = $this->container->get('router');          
        $profileUrl = $router->generate('cairn_user_profile_view',array('username'=>$user->getUsername()));

        $currentPassword = $form->get('current_password')->getData(); 
        $newPassword = $user->getPlainPassword();

        try{
            $passwordManager->changePassword($currentPassword, $newPassword, $user->getCyclosID());
        }catch(\Exception $e){
            if($e instanceof Cyclos\ServiceException){
                if($e->errorCode == 'VALIDATION'){
                    for($i = 0; $i < count($e->error->validation->allErrors); $i++){
                        if(strpos($e->error->validation->allErrors[$i],'only these characters') !== false){
                            //retourner à la page précédente
                            $request = $this->container->get('request_stack')->getCurrentRequest();
                            $request->getSession()->getFlashBag()->add('error','Votre mot de passe contient un caractère non traité');
                            $event->setResponse(new RedirectResponse($request->getRequestUri()));
                            return;
                        }
                    }
                }
            }
            throw $e;
        }
        if($user->isFirstLogin()){
            $user->setFirstLogin(false);
        }

        if($this->container->get('cairn_user.api')->isApiCall()){
            $response = new Response('Change password : ok !');
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            $event->setResponse($response);
        }else{
            $event->setResponse(new RedirectResponse($profileUrl));
        }
    }


    public function onLogin(InteractiveLoginEvent $event)
    {
        $networkInfo = $this->container->get('cairn_user_cyclos_network_info');          
        $networkName= $this->container->getParameter('cyclos_currency_cairn');          

        $loginManager = new LoginManager();

        $request = $event->getRequest();

        $username = $request->request->all()['_username'];
        $password = $request->request->all()['_password'];

        $session = $request->getSession();

        //get username and password from login request
        $credentials = array('username'=>$username,'password'=>$password);
        $networkInfo->switchToNetwork($networkName,'login',$credentials);

        $dto = new \stdClass();
        $dto->amount = $this->container->getParameter('session_timeout');
        $dto->field = 'SECONDS';
        //get cyclos token and set in session
        $loginResult = $loginManager->login($dto);
        $session->set('cyclos_token',$this->container->get('cairn_user.security')->vigenereEncode($loginResult->sessionToken)); 

    }


    public function onKernelController(FilterControllerEvent $event)
    {
        $networkInfo = $this->container->get('cairn_user_cyclos_network_info');          
        $networkName=$this->container->getParameter('cyclos_currency_cairn');          
        $securityService = $this->container->get('cairn_user.security');

        if($this->container->get('cairn_user.api')->isApiCall()){
            $cyclos_token = $securityService->vigenereDecode($event->getRequest()->request->get('cyclos_token'));
        }else{
            $session = $event->getRequest()->getSession();
            $cyclos_token = $securityService->vigenereDecode($session->get('cyclos_token'));
        }

        $networkInfo->switchToNetwork($networkName,'session_token',$cyclos_token);
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

        //if maintenance.txt exists
        if(is_file('maintenance.txt')){
            $event->setResponse($templating->renderResponse('CairnUserBundle:Security:maintenance.html.twig'));
        }
    }

    public function onFirstLogin(FilterResponseEvent $event)
    {
        $security = $this->container->get('cairn_user.security');          
        $router = $this->container->get('router');          

        $currentUser = $security->getCurrentUser();

        if($currentUser instanceof \Cairn\UserBundle\Entity\User){
            if($currentUser->isFirstLogin() && ($event->getRequest()->get('_route') != 'fos_user_change_password')){
                $editPwdUrl = $router->generate('fos_user_change_password');
                $event->setResponse(new RedirectResponse($editPwdUrl));
            }
        }
    }

    public function onDisabledUser(GetResponseEvent $event)
    {
        $security = $this->container->get('cairn_user.security');          
        $router = $this->container->get('router');          

        $currentUser = $security->getCurrentUser();

        if($currentUser instanceof \Cairn\UserBundle\Entity\User){
            if(!$currentUser->isEnabled()){
                $logoutUrl = $router->generate('fos_user_security_logout');
                $event->setResponse(new RedirectResponse($logoutUrl));
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
                $subject = 'Votre espace membre a été bloqué';
                $body = 'Suite à 3 échecs de validation de votre carte de clés personnelles, votre espace membre a été bloqué par souci de sécurité. \n Veuillez contacter nos services pour plus d\'information';
                $accessPlatform->disable(array($user),$subject,$body);
                $event->setRedirect(true);
            }
        }
        $em->flush();
    }

}
