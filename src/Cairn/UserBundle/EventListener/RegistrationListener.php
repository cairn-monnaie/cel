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

    /**
     * Applies some actions on new registered user at confirmation
     *
     * By default, at email confirmation, the user is enabled. We want an explicit authorization from referents,so we disable the user.
     * Plus, we add default referents(super_admin) and notify them of this new registration
     *@todo Send email to user's referent 
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
        $body = $this->container->get('templating')->render('CairnUserBundle:Emails:pending_validation.html.twig',
            array('user'=>$user));

        $messageNotificator->notifyByEmail($subject,$from,$to,$body);      
        $event->getRequest()->getSession()->getFlashBag()->add('info','Merci d\'avoir validé votre adresse mail ! Vous recevrez un mail une fois votre inscription validée.');

        $subject = 'Demande d\'inscription';                               
        $from = $messageNotificator->getNoReplyEmail();                    
        //        $to = $admin->getEmail();                                         
        //        $body = $this->render('CairnUserBundle:Emails:submit_alert.html.twig',array('user'=>$user));
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
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $em = $this->container->get('doctrine.orm.entity_manager');
        $session = $event->getRequest()->getSession();
        $user = $event->getForm()->getData();

        if($user->hasRole('ROLE_PRO')){
            $groupName = $this->container->getParameter('cyclos_group_pros');
        }else{
            $groupName = $this->container->getParameter('cyclos_group_network_admins');
        }

        //add an equivalent user in cyclos (we retrieve the id only)   
        $userDTO = new \stdClass();                                    
        $userDTO->name = $user->getName();                             
        $userDTO->username = $user->getUsername();                     
        $userDTO->internalName = $user->getUsername();                 
        $userDTO->login = $user->getUsername();                        
        $userDTO->email = $user->getEmail();                           


        $password = new \stdClass();                                   
        $password->assign = true;                                      
        $password->type = 'login';//in Cyclos : System -> User config -> password types -> click on login Password
        $password->value = $user->getPlainPassword();                  
        $password->confirmationValue = $user->getPlainPassword();//control already done in Symfony
        $userDTO->passwords = $password;                               


        $groupVO = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($groupName);

        //if the webServices channel is not added, it will be impossible to update/remove the cyclos user entity from the code
        $webServicesChannelVO = $this->container->get('cairn_user_cyclos_channel_info')->getChannelVO('webServices');

        $newUserCyclosID = $this->userManager->addUser($userDTO,$groupVO,$webServicesChannelVO);

        $user->setCyclosID($newUserCyclosID);

        $card = new Card($user,$this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'));
        $user->setCard($card);                                         

        //        //finally, deal with logo                                      
        //        if(!$user->getImage()){                                        
        //            $imageRepo = $em->getRepository('CairnUserBundle:Image');  
        //            $unknown = $imageRepo->findOneBy(array('alt'=>'purple-unknown.png'));
        //            $user->setImage($unknown);                                 
        //        }
    }


} 
