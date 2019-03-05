<?php

namespace Cairn\UserBundle\Controller;

use Cairn\UserBundle\CairnUserBundle;
use Cairn\UserBundle\Entity\ZipCity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Address;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\BankingManager;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\TextType;                   
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                   
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\RegistrationType;
use Cairn\UserBundle\Form\OperationType;
use Cairn\UserBundle\Form\SimpleOperationType;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;


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

    private $bankingManager;                                                      

    public function __construct()                                              
    {                                                                          
        $this->userManager = new UserManager();
        $this->bankingManager = new BankingManager();
    }   



    /**
     * First step of user's registration
     *
     * The type of user is set in session here because we will need it in our RegistrationEventListener.
     */
    public function registrationAction(Request $request)
    {
        $user = $this->getUser();
        if($user){
            if($user->isAdherent()){
                throw new AccessDeniedException('Vous avez déjà un espace membre.');
            }
        }
        return $this->render('CairnUserBundle:Registration:index.html.twig');
    }

    public function registrationByTypeAction(Request $request, string $type){
        if( ($type == 'person') || ($type=='pro') || ($type == 'localGroup') || ($type=='superAdmin')){
            $checker = $this->get('security.authorization_checker');
            if(($type == 'localGroup' || $type=='superAdmin') && (!$checker->isGranted('ROLE_SUPER_ADMIN')) ){
                throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires.');
            }
            $session = $request->getSession();
            $session->set('registration_type',$type);
            return $this->forward('FOSUserBundle:Registration:register',array('type'=>$type));
        }else{
            return $this->redirectToRoute('cairn_user_registration');
        }
    }

    public function zipCitiesAction(Request $request){
        if ($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $zipCities = $em->getRepository(ZipCity::class)->findAll();
            $returnArray = array();
            foreach ($zipCities as $zipCity){
                $returnArray[] = $zipCity->getName();
            }
            return new JsonResponse($returnArray);
        }
        return new Response("Ajax only",400);
    }

}
