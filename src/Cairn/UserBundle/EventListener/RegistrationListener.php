<?php
// Cairn/UserBundle/EventListener/RegistraationListener.php

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

    public function onProfileEdit(FormEvent $event)
    {
        $form = $event->getForm();
        $user = $form->getData();

        $userVO = $this->container->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        $userDTO = $this->container->get('cairn_user_cyclos_user_info')->getUserDTO($userVO->id);
        $userDTO->name = $user->getName();
        $userDTO->username = $user->getUsername();
        $userDTO->email = $user->getEmail();

        $this->userManager->editUser($userDTO);                          
    }

    /**
     * Applies some actions on new registered user at confirmation
     *
     * By default, at email confirmation, the user is enabled. We want an explicit authorization from referents,so we disable the user.
     * Plus, we add default referents(super_admin) and notify them of this new registration
     *@TODO Send email to user's referent 
     *@TODO : if new user is ROLE_SUPER_ADMIN : assign as referent of all ROLE_PRO and ROLE_ADMIN
     */
    public function onRegistrationConfirm(GetResponseUserEvent $event)
    {
        $messageNotificator = $this->container->get('cairn_user.message_notificator');
        $em = $this->container->get('doctrine.orm.entity_manager');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $superAdmins = $userRepo->myFindByRole(array('ROLE_SUPER_ADMIN'));

        $user = $event->getUser();
        $user->setEnabled(false);
        foreach($superAdmins as $superAdmin){
            $user->addReferent($superAdmin);
        }

        //automatically assigns a local group as referent to a pro if they have same city
        if($user->hasRole('ROLE_PRO')){
            $localGroup = $userRepo->findAdminWithCity($user->getCity());
            if($localGroup){
                if(!$user->hasReferent($localGroup)){
                    $user->addReferent($localGroup);
                }
            }
        }

        $subject = 'Validation de l\'administrateur';                      
        $from = $messageNotificator->getNoReplyEmail();                    
        $to = $user->getEmail();                                                      
        $body = $this->renderView('CairnUserBundle:Emails:pending_validation.html.twig',array('user'=>$user));

        $messageNotificator->notifyByEmail($subject,$from,$to,$body);      
        $event->getRequest()->getSession()->getFlashBag()->add('success','Merci d\'avoir validé votre adresse mail ! Vous recevrez un mail une fois votre inscription validée.');

    }


    /**
     *Set the role of the future user before binding the form
     *
     *User's role is set before binding the form because, depending on it, the registration form will display some fields or not
     *
     */
    public function onRegistrationInitialize(UserEvent $event)
    {
        $session = $event->getRequest()->getSession();
        $type = $session->get('registration_type'); 
        if(!$type){
            $type = 'pro'; 
        }
        $user = $event->getUser();

        $user->setPlainPassword(User::randomPassword());
        switch ($type){
        case 'pro':
            $user->addRole('ROLE_PRO');
            break;
        case 'localGroup':
            $user->addRole('ROLE_ADMIN');
            break;
        case 'superAdmin':
            $user->addRole('ROLE_SUPER_ADMIN');
            break;
        }
    }

    /**
     *Once the registration form is valid, this function sets up the user in Cyclos and Doctrine
     *
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $session = $event->getRequest()->getSession();
        $user = $event->getForm()->getData();

        if($user->hasRole('ROLE_PRO')){
            $groupName = $this->container->getParameter('cyclos_group_pros');
        }else{
            $groupName = $this->container->getParameter('cyclos_group_network_admins');
        }

        $salt = $this->container->get('cairn_user.security')->generateCardSalt($user);
        $card = new Card($user,$this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'),$salt);
        $user->setCard($card);                                         

        //set cyclos ID here to pass the constraint cyclos_id not null
        $cyclosID = rand(1, 1000000000);
        $existingUser = $userRepo->findOneBy(array('cyclosID'=>$cyclosID));
        while($existingUser){
            $cyclosID = $cyclosID + 1; 
            $existingUser = $userRepo->findOneBy(array('cyclosID'=>$cyclosID));
        }
        $user->setCyclosID($cyclosID);
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     * @throws \Exception
     */
    protected function renderView($view, array $parameters = array())
    {
        if ($this->container->has('templating')) {
            return $this->container->get('templating')->render($view, $parameters);
        }

        if (!$this->container->has('twig')) {
            throw new \LogicException('You can not use the "renderView" method if the Templating Component or the Twig Bundle are not available.');
        }

        return $this->container->get('twig')->render($view, $parameters);
    }
} 
