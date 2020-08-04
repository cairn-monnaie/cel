<?php

namespace Cairn\UserBundle\Controller;

use Cairn\UserBundle\CairnUserBundle;
use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\ZipCity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\Address;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\User;

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
 * This class contains actions that need no role at all. Mostly, those can be done before login as anonymous user, or ajax requests 
 */
class DefaultController extends BaseController
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


    public function contactFormAction(Request $request)
    {
        if($request->isMethod('POST')){
            $newPost = array_map('htmlspecialchars',$_POST);

            if(isset($newPost['emailComm'])){
                $newPost['preference'] = 'email';
            }elseif(isset($newPost['callComm'])){
                $newPost['preference'] = 'téléphone';
            }else{
                $newPost['preference'] = 'indifférent';
            }
            $body = $this->get('templating')->render('CairnUserBundle:Emails:contact_form.html.twig',$newPost);
            $messageNotificator = $this->get('cairn_user.message_notificator');

            $from = $messageNotificator->getNoReplyEmail();
            $to = $this->getParameter('cairn_email_management');
            
            switch ($newPost['subject']){
            case "open_account":
                $subject = "Ouverture de compte professionnel";
                break;
            case "infos":
                $subject = "Demande d'informations";
                break;
            case "problem":
                $subject = "Un utilisateur a eu un problème technique";
                break;
            default:
                $subject = "Vous avez été contacté";
                break;
            }

            $subject = '[Ecairn][ContactForm] '.$subject;

            
            $messageNotificator->notifyByEmail($subject,$from,$to,$body);

            $message = ['key'=>'contact_form_message_sent']; 
            $route = ($this->getUser()) ? 'cairn_user_welcome' : 'fos_user_security_login';
            return $this->getRedirectionResponse(
                $route, 
                [],
                [], 
                Response::HTTP_CREATED,
                $message
            );
        }else{
            return $this->getRenderResponse(
                'CairnUserBundle:Default:contact_form.html.twig', 
                [],
                [], 
                Response::HTTP_OK
            );
        }
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

    public function registrationByTypeAction(Request $request, string $type, $_format){
        if( ($type == 'person') || ($type=='pro') || ($type == 'localGroup') || ($type=='superAdmin')){
            $checker = $this->get('security.authorization_checker');
            if(($type == 'localGroup' || $type=='superAdmin') && (!$checker->isGranted('ROLE_SUPER_ADMIN')) ){
                throw new AccessDeniedException('not_access_rights');
            }
            if(($type == 'pro') && (!$checker->isGranted('ROLE_ADHERENT')) ){
                $message = ['key'=>'contact_form_open_account']; 
                return $this->getRedirectionResponse(
                    'cairn_user_contact_form', 
                    [],
                    [], 
                    Response::HTTP_OK,
                    $message
                );
           
            }

            $session = $request->getSession();
            $session->set('registration_type',$type);

            return $this->getRedirectionResponse(
                    'fos_user_registration_register', 
                    ['type'=>$type],
                    [], 
                    Response::HTTP_OK
                );

            //return $this->getRenderResponse(
            //    'FOSUserBundle:Registration:register', 
            //    ['type' => $type],
            //    [], 
            //    Response::HTTP_OK,
            //    $message
            //);

        }else{
            return $this->redirectToRoute('cairn_user_registration');
        }
    }

    /**
     * Returns all cities and their respective zipCodes
     *
     */
    public function zipCitiesAction(Request $request){
        if ($this->get('cairn_user.api')->isRemoteCall()){
            $em = $this->getDoctrine()->getManager();
            $zcRepo = $em->getRepository(ZipCity::class);

            if($search = $request->query->get('search')){
                $zb = $zcRepo->createQueryBuilder('z');

                $zb->andWhere(
                    $zb->expr()->orX(
                        "z.zipCode LIKE '%".$search."%'"
                        ,
                        "z.city LIKE '%".$search."%'"
                    )
                )
                ->orderBy('z.zipCode');
                $zipCities = $zb->getQuery()->getResult();
            }else{
                $zipCities = $zcRepo->findAll();
            }
            $returnArray = array();
            foreach ($zipCities as $zipCity){
                $returnArray[] = $zipCity->getName();
            }
            return $this->getRenderResponse(
                '',
                [],
                $returnArray,
                Response::HTTP_OK
            );
        }
        return new Response("JSON only",400);
    }

    /**
     * Returns accessible user's names and icons if exist
     *
     * If the current user is adherent, he can only access other enabled adherents. If admin, he can access adherents under
     * his responsibility
     *
     */
    public function accountsAction(Request $request){

        $currentUser = $this->getUser();
        $currentUserID = $currentUser->getID();

        if ($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository(User::class);

            $ub = $userRepo->createQueryBuilder('u');

            if($currentUser->isAdherent()){
                $userRepo->whereEnabled($ub,true)->whereRole($ub,'ROLE_PRO')->whereConfirmed($ub);
            }else{
                $userRepo->whereReferent($ub, $currentUserID);
            }

            $users = $ub->getQuery()->getResult();

            $returnArray = array();
            foreach ($users as $user){
                $image = $user->getImage();
                $returnArray[] = array('id'=>$user->getID(),'username'=> $user->getUsername(), 'name' => $user->getAutocompleteLabel(true) ,'icon' => (($image && $image->getId()) ? '/'.$image->getWebPath() : '')) ;
            }
            return new JsonResponse($returnArray);
        }
        return new Response("Ajax only",400);
    }

    public function beneficiaryImageAction(Request $request){
        if ($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $beneficiary = $em->getRepository(Beneficiary::class)->findBy( array('ICC'=>$request->get('number') ));
            $returnArray = array() ;
            if ($beneficiary && $image = $beneficiary->getUser()->getImage()){
                $returnArray = array(
                    'name' => $beneficiary->getUser()->getAutocompleteLabel(false) ,
                    'icon' => (($image && $image->getUrl()) ? '/'.$image->getWebPath() : ''),
                    'alt' => $beneficiary->getUser()->getName()) ;
            }
            return new JsonResponse($returnArray);
        }
        return new Response("Ajax only",400);
    }

}
