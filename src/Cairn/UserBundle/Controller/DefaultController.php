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

use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

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

    public function indexAction()
    {
        return $this->render('CairnUserBundle:Default:index.html.twig');
    }


    public function redirectToLoginAction(Request $request, $message)
    {
        $session = $request->getSession();
        $session->getFlashBag()->add('info',$message);
        return $this->redirectToRoute('fos_user_security_logout');
    }



    public function installAction(Request $request)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('globalAdmin');

        $adminUsername = $this->getParameter('cyclos_global_admin_username');
        $adminPassword = $this->getParameter('cyclos_global_admin_password');

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $main_admin = $userRepo->findOneBy(array('username'=>$adminUsername));
        if (!$main_admin){
            $response = $this->get('cairn_user_cyclos_user_info')->getUserVOByName($adminUsername);
            $id = $response->id;
            $new_admin = new User();
            $new_admin->setCyclosID($id);
            $new_admin->setUsername($adminUsername);
            $new_admin->setName('main admin');

            if($this->getParameter('kernel.environment') == 'test'){
                $adminPassword = '@@bbccdd';
            }
            $new_admin->setPlainPassword($adminPassword);
            $new_admin->setEnabled(true);
            $new_admin->setEmail($this->getParameter('cyclos_global_admin_email'));

            $new_admin->addRole('ROLE_SUPER_ADMIN');

            $zip = $em->getRepository('CairnUserBundle:ZipCity')->findOneBy(array('zipCode'=>'38000','city'=>'Grenoble'));
            $address = new Address();
            $address->setZipCity($zip);
            $address->setStreet1('7 rue Très Cloître');

            $new_admin->setAddress($address);
            $new_admin->setDescription('main user admin for app');

            //set auto-referent
            $new_admin->addReferent($new_admin);

            $em = $this->getDoctrine()->getManager();


            $em->persist($new_admin);

            //generer puis encoder la carte
            $encoder = $this->get('security.encoder_factory')->getEncoder($new_admin);


            if ($encoder instanceof BCryptPasswordEncoder) {                       
                $salt = NULL;                                              
            } else {                                                               
                $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            }       

            $card = new Card($new_admin,$this->getParameter('cairn_card_rows'),$this->getParameter('cairn_card_cols'),$salt);

            $new_admin->setCard($card);

            $card->generateCard($this->getParameter('kernel.environment'));
            $fields = unserialize($card->getFields());

            $html =  $this->renderView('CairnUserBundle:Pdf:card.html.twig',
                array('card'=>$card,'fields'=>$fields));

            $card->setGenerated(true);

            $nbRows = $card->getRows();
            $nbCols = $card->getCols();

            for($row = 0; $row < $nbRows; $row++){
                for($col = 0; $col < $nbCols; $col++){
                    $encoded_field = $encoder->encodePassword($fields[$row][$col],$card->getSalt());
                    $fields[$row][$col] = substr($encoded_field,0,4);
                }
            }

            $card->setFields(serialize($fields));

            $em->persist($new_admin);

            $session = $request->getSession();
            $session->getFlashBag()->add('success','admin user is created !');

            $em->flush();
            $filename = sprintf('carte-sécurité-cairn-'.$card->getNumber().'-%s.pdf',$new_admin->getName());
            $this->addFlash('success','Supprimer le fichier de votre ordinateur dès que la carte a été imprimée !');
            return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ]
            );

        }else{
            $session = $request->getSession();
            $session->getFlashBag()->add('info','admin user already exists, please login');
        }
        return $this->redirectToRoute('cairn_user_welcome');
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

            $session->set('registration_type',$type);
            return $this->redirectToRoute('fos_user_registration_register',array('type'=>$type));
        }elseif($type == 'adherent'){
            return $this->render('CairnUserBundle:Registration:register_adherent_content.html.twig');
        }else{
            return $this->redirectToRoute('cairn_user_registration');
        }
    }    


}
