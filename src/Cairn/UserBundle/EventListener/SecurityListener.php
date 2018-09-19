<?php
// src/Cairn/UserBundle/EventListener/SecurityListener.php

namespace Cairn\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse; // N'oubliez pas ce use
use Symfony\Component\HttpFoundation\Response;

use Cairn\UserBundle\Event\InputCardKeyEvent;
use Cairn\UserBundle\Event\InputPasswordEvent;
use Cairn\UserBundle\Event\DisabledUserEvent;

use Cairn\UserBundle\Event\SecurityEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Cairn\UserBundle\Service\Counter;
use Cairn\UserBundle\Service\AccessPlatform;
use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * This class contains called functions when events defined in Event\SecurityEvents are dispatched
 *
 */
class SecurityListener
{
    protected $encoderFactory;
    protected $counter;
    protected $accessPlatform;
    protected $tokenStorage;
    protected $router;
    protected $em;
    protected $templating;
    protected $adminUsername;

    public function __construct(Router $router, TokenStorageInterface $tokenStorage, EncoderFactory $encoderFactory, Counter $counter, AccessPlatform $accessPlatform, EntityManager $em, TwigEngine $templating, $adminUsername)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->encoderFactory = $encoderFactory;
        $this->counter = $counter;
        $this->accessPlatform = $accessPlatform;
        $this->em = $em;
        $this->templating = $templating;
        $this->adminUsername = $adminUsername;
    }


    /**
     *Deals with maintenance of the application
     *
     *If the appplication is in maintenance state, any request is redirected to maintenance page. This action is called before any request
     is achieved
     */
    public function onMaintenance(GetResponseEvent $event)
    {
        //if maintenance.txt exists
        if(is_file('../maintenance.txt')){
            $event->setResponse($this->templating->renderResponse('CairnUserBundle:Security:maintenance.html.twig'));
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
        $request = $event->getRequest();
        $token = $this->tokenStorage->getToken();

        $userRepo = $this->em->getRepository('CairnUserBundle:User');
        $route = $request->get('_route');
        $query = $request->query->all();
        $sensibleRoutes = SecurityEvents::SENSIBLE_ROUTES;
        $sensibleUrls = SecurityEvents::SENSIBLE_URLS;

        $isSensibleUrl = in_array(array($route,$query),$sensibleUrls);
        $isSensibleRoute = in_array($route,$sensibleRoutes);

        $isExceptionCase = false;
        //check if installed admin is asking for a new security card
        if($token){
            $currentUser = $token->getUser();
            if($currentUser instanceof \Cairn\UserBundle\Entity\User){
                if(($currentUser->getUsername() == $this->adminUsername && $route == 'cairn_user_card_generate')){
                    //for itself ? for someone else ?
                    $toUser = $userRepo->findOneBy(array('id'=>$query['id']));
                    if($toUser === $currentUser){
                        $isExceptionCase = true;
                    }
                }
            }
        }

        if(!$isExceptionCase){
            if($isSensibleRoute || $isSensibleUrl){
                if(!$request->getSession()->get('has_input_card_key_valid')){
                    $cardSecurityLayer = $this->router->generate('cairn_user_card_security_layer',array('route'=>$route,'query'=>$query));
                    $event->setResponse(new RedirectResponse($cardSecurityLayer));
                }
            }
        }

    }


    /**
     * Deals with the input password event
     *
     * When a password input is required, we follow the steps below :
     *     _compare the input with the real user's password
     *     _if failure, clear the entityManager from all persistance then increment user's attribute 'passwordTries'
     *     _if 3 passwordTries : disable the user
     *     _if success : reinitialize tries
     *
     *@todo Refresh all changes entities and not just $user. Imagine that $user creates a new entity in a controller action, but fails
     * password input. In the current state of the code, the entity could be added to database if persisted before InputPasswordEvent
     * dispatchment. Moremover, an existing changed entity would be saved in database     
     */
    public function onPasswordInput(InputPasswordEvent $event)
    {
        $user = $event->getUser();
        $password = $event->getPassword();

        $encoder = $this->encoderFactory->getEncoder($user);                         
        $salt = $user->getSalt();                                       

        if(!$encoder->isPasswordValid($user->getPassword(), $password, $salt)){
            $this->em->refresh($user);

            if($user->getPasswordTries() >= 2){
                $this->counter->incrementTries($user,'password');
                $subject = 'Trop d\'essais de mot de passe';
                $body = 'Trois échecs ont été enrigistrés lors de la saisie de votre mot de passe. Votre compte a donc été automatiquement bloqué. Veuillez nous contacter pour trouver une solution.';
                $this->accessPlatform->disable(array($user),$subject,$body);
                $event->setRedirect(true);

            }
            else{
                $this->counter->incrementTries($user,'password');
            }

        }
        else{
            $this->counter->reinitializeTries($user,'password');
        }
        $this->em->flush();
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
        $session = $event->getSession();
        $user = $event->getUser();

        $encoder = $this->encoderFactory->getEncoder($user);                         
        $salt = $user->getSalt();                                       

        $cardKey = $event->getCardKey();
        $position = $event->getPosition();

        $currentCard = $user->getCard();

        $fields = unserialize($currentCard->getFields());                             
        $rows = $currentCard->getRows();                                              

        $pos_row = intdiv($position,$rows);                                 
        $pos_col = $position % $rows;                                       
        $field_value = $fields[$pos_row][$pos_col];

        if($field_value == substr($encoder->encodePassword($cardKey,$salt),0,4)){
            $this->counter->reinitializeTries($user,'cardKey');
            $session->set('has_input_card_key_valid',true);
        }
        else{
            if($user->getCardKeyTries() >= 2){
                $this->counter->incrementTries($user,'cardKey');
                $subject = 'Votre espace membre a été bloqué';
                $body = 'Suite à 3 échecs de validation de votre carte de clés personnelles, votre espace membre a été bloqué par souci de sécurité. \n Veuillez contacter nos services pour plus d\'information';
                $this->accessPlatform->disable(array($user),$subject,$body);
                $event->setRedirect(true);
            }
            else{
                $this->counter->incrementTries($user,'cardKey');
            }
        }
        $this->em->flush();
    }

}
