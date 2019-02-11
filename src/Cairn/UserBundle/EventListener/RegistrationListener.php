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
        $router = $this->container->get('router');          

        $form = $event->getForm();
        $user = $form->getData();

        $userVO = $this->container->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        $userDTO = $this->container->get('cairn_user_cyclos_user_info')->getUserDTO($userVO->id);
        $userDTO->name = $user->getName();
        $userDTO->username = $user->getUsername();
        $userDTO->email = $user->getEmail();

        $this->userManager->editUser($userDTO);                          

        if($this->container->get('cairn_user.api')->isApiCall()){
            $serializedUser = $this->container->get('cairn_user.api')->serialize($user, array('localGroupReferent','singleReferent','referents','beneficiaries','card','plainPassword'));
            $response = new Response($serializedUser);
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            $event->setResponse($response);
        }else{
            $profileUrl = $router->generate('cairn_user_profile_view',array('id'=>$user->getID()));
            $event->setResponse(new RedirectResponse($profileUrl));
        }
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

        //we set referent roles
        foreach($superAdmins as $superAdmin){
            $user->addReferent($superAdmin);
        }

        //if user is a person, any local group is referent
        if($user->hasRole('ROLE_PERSON')){
            $admins = $userRepo->myFindByRole(array('ROLE_ADMIN'));
            foreach($admins as $admin){
                $user->addReferent($admin);
            }
        }

        //if user is a local group, he is referent of any individual adherent
        if($user->hasRole('ROLE_ADMIN')){
            $persons = $userRepo->myFindByRole(array('ROLE_PERSON'));
            foreach($persons as $person){
                $person->addReferent($user);
            }
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
        $event->getRequest()->getSession()->getFlashBag()->add('success','Merci d\'avoir validé votre adresse mail ! Vous recevrez un mail une fois votre inscription validée par l\'association.');

        $router = $this->container->get('router');          
        $loginUrl = $router->generate('fos_user_security_login');
        $event->setResponse(new RedirectResponse($loginUrl));

    }


    /**
     *Set the role of the future user before binding the form
     *
     *User's role is set before binding the form because, depending on it, the registration form will display some fields or not
     *
     */
    public function onRegistrationInitialize(UserEvent $event)
    {
        $request = $event->getRequest();
        $type = $request->query->get('type'); 
        if(!$type){
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
            break;
        }
    }

    /**
     *Once the registration form is valid, this function sets up the user in Cyclos and Doctrine
     *
     * Note: FOSUserBundle EmailConfirmationListener is also listening to this event. Then, as we want to master the response in case of
     * API call, this function must be called in the end (piority defined in services.yml)
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $user = $event->getForm()->getData();

        //set cyclos ID here to pass the constraint cyclos_id not null
        $cyclosID = rand(1, 1000000000);
        $existingUser = $userRepo->findOneBy(array('cyclosID'=>$cyclosID));
        while($existingUser){
            $cyclosID = $cyclosID + 1; 
            $existingUser = $userRepo->findOneBy(array('cyclosID'=>$cyclosID));
        }
        $user->setCyclosID($cyclosID);

        if($this->container->get('cairn_user.api')->isApiCall()){
            $serializedUser = $this->container->get('cairn_user.api')->serialize($user, array('localGroupReferent','singleReferent','referents','beneficiaries','card','plainPassword'));
            $response = new Response($serializedUser);
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_CREATED);
            $event->setResponse($response);
        }
    }


} 
