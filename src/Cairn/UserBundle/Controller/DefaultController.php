<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Address;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserCyclosBundle\Entity\UserManager;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\TextType;                   
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                   
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\RegistrationType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


use Cyclos;

/**
 * This class contains actions that need no role at all. Mostly, those can be done before login as anonymous user. 
 */
class DefaultController extends Controller
{
    /**
     * Deals with all user management actions to operate on Cyclos-side
     *@var UserManager $userManager
     */
    private $userManager;                                                      

    public function __construct()                                              
    {                                                                          
        $this->userManager = new UserManager();                                
    }   

    /**
     * First step of user's registration
     *
     * The type of user is set in session here because we will need it in our RegistrationEventListener.
     */
    public function registrationAction(Request $request)
    {
        $session = $request->getSession();
        $checker = $this->get('security.authorization_checker');

        $user = $this->getUser();
        if($user){
            if($user->hasRole('ROLE_PRO')){
                throw new AccessDeniedException('Vous avez déjà un espace membre.');
            }
        }

        $type = $request->query->get('type'); 
        if($type == NULL){
            return $this->render('CairnUserBundle:Registration:index.html.twig');
        }
        elseif( ($type == 'pro') || ($type == 'localGroup') || ($type == 'superAdmin')){
            if( ($type == 'localGroup' || $type=='superAdmin') && (!$checker->isGranted('ROLE_SUPER_ADMIN')) ){
                throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires.');
            }
            return $this->redirectToRoute('fos_user_registration_register',array('type'=>$type));
        }elseif($type == 'adherent'){
            return $this->render('CairnUserBundle:Registration:register_adherent_content.html.twig');
        }else{
            return $this->redirectToRoute('cairn_user_registration');
        }
    }    


}
