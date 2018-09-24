<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserCyclosBundle\Entity\UserManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserCyclosBundle\Form\UserType;
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cyclos;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * This class contains actions related to user management by administrators
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends Controller
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
     * Set the enabled attribute of user with provided ID to false
     *
     * The ParamConverter is not used to retrieve the User object because this action is preceded by the card security layer
     * in Cairn/UserBundle/CardController/InputCardKey, which needs explicitely all the query parameters in the query array
     * An email is sent to the user being (re)activated
     *
     * @throws  NotFoundHttpException ID in query does not match any user in Doctrine
     * @throws  AccessDeniedException Current user making request is not a referent of the user being involved
     * @Method("GET")
     */ 
    public function blockUserAction(Request $request, User $user)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $currentUser = $this->getUser();

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){

                $subject = 'Votre espace membre Cairn a été désactivé';
                $body = 'Votre espace membre a été bloqué par le groupe local ' .$this->getUser()->getCity();

                $this->get('cairn_user.access_platform')->disable(array($user),$subject,$body);
                $session->getFlashBag()->add('info','L\'utilisateur ' . $user->getName() . ' a été bloqué avec succès. Il ne peut plus accéder à la plateforme.');
                $em->flush();
            }

            return $this->redirectToRoute('cairn_user_profile_view',array('id' => $user->getID()));

        }
        return $this->render('CairnUserBundle:User:block.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Set the enabled attribute of user with provided ID to true
     *
     * The ParamConverter is not used to retrieve the User object because this action is preceded by the card security layer
     * in Cairn/UserBundle/CardController/InputCardKey, which needs explicitely all the query parameters in the query array
     * An email is sent to the user being (re)activated
     *
     * @throws  NotFoundHttpException ID in query does not match any user in Doctrine
     * @throws  AccessDeniedException Current user trying to activate access is not a referent of the user being involved
     * @Method("GET")
     */ 
    public function activateUserAction(Request $request, User $user)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $currentUser = $this->getUser();

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){

                $this->get('cairn_user.access_platform')->enable(array($user));
                $em->flush();

                $session->getFlashBag()->add('info','L\'utilisateur ' . $user->getName() . ' a été activé. Il peut à nouveau accéder à la plateforme.');

                //if first activation : ask if generate card now
                if($user->getLastLogin() == NULL){
                    return $this->render('CairnUserBundle:Card:generate_card.html.twig',array('user'=>$user,'card'=>$user->getCard()));
                }
            }

            return $this->redirectToRoute('cairn_user_profile_view',array('id' => $user->getID()));
        }

        return $this->render('CairnUserBundle:User:activate.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));

    }


    /**
     * Assign a unique local group (ROLE_ADMIN) as a referent of @param
     *
     * @param  User $user  User entity the referent is assigned to
     * @todo : ensure unicity of a ROLE_ADMIN among user's referents : add/replace
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function assignReferentAction(Request $request, User $user)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $choices = $userRepo->myFindByRole(array('ROLE_ADMIN'));

        $form = $this->createFormBuilder()
            ->add('singleReferent', EntityType::class, array(
                'class'=>User::class,
                'constraints'=>array(                              
                    new Assert\NotNull()                           
                ),
                'choice_label'=>'name',
                'choices'=>$choices,
                'expanded'=>true
            ))
            ->add('save', SubmitType::class, array('label'=>'Assigner'))
            ->getForm();

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $referent = $form->get('singleReferent')->getData();
                $user->addReferent($referent);

                $em->flush();
                $session->getFlashBag()->add('success',
                    $referent->getUsername() . ' est désormais référent de '.$user->getUsername());

                return $this->redirectToRoute('cairn_user_profile_view',array('id'=>$user->getID()));
            }
        }

        return $this->render('CairnUserBundle:User:add_referent.html.twig',array('form'=>$form->createView(),'user'=>$user));
    }   

    /**
     * searches new registered users whom emails have not been confirmed, warns them or remove them
     *
     * Everyday, this action is requested to look for registered users who have not validated their email. A delay to do so is defined.
     * If the deadline is missed, the new registered user is automatically removed with an email notification sent, otherwise he is just
     * reminded to validate it 5/2 and 1 day before the deadline
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function checkEmailsValidationAction(Request $request)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $ub = $userRepo->createQueryBuilder('u');
        $ub->where('u.enabled = false')
            ->andWhere('u.confirmationToken is not NULL')
            ;

        $pendingUsers = $ub->getQuery()->getResult();

        $messageNotificator = $this->get('cairn_user.message_notificator');
        $from = $messageNotificator->getNoReplyEmail();

        $today = new \Datetime(date('Y-m-d H:i:s'));

        foreach($pendingUsers as $user){
            $creationDate = $user->getCreationDate();
            $expirationDate = date_modify(new \Datetime($creationDate->format('Y-m-d H:i:s')),'+ 2 days');//.$this->getParameter('cairn_email_activation_delay').' days');
            $interval = $today->diff($expirationDate);
            $diff = $interval->days;
            $nbMonths = intdiv($this->getParameter('cairn_email_activation_delay'),30);
            if($interval->invert == 0){
                if($interval->m == $nbMonths){
                    if(($diff == 5) || ($diff == 2) || ($diff == 1)){
                        $subject = 'Validation de votre adresse email';
                        $confirmationUrl = $this->generateUrl('fos_user_registration_confirm',
                            array('token'=>$user->getConfirmationToken()),
                            UrlGeneratorInterface::ABSOLUTE_URL);

                        $body = $this->renderView('CairnUserBundle:Emails:reminder_email_activation.html.twig',
                            array('email'=>$user->getEmail(),'remainingDays'=>$diff,'confirmationUrl'=>$confirmationUrl));

                        $messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);

                    }
                    elseif($diff == 0){
                        $subject = 'Expiration de validation';
                        $body = $this->renderView('CairnUserBundle:Emails:email_expiration.html.twig',array('diff'=>$diff));

                        $params = new \stdClass();                                             
                        $params->status = 'REMOVED';                                           
                        $params->user = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
                        $this->userManager->changeStatusUser($params);
                        $saveEmail = $user->getEmail();
                        $em->remove($user);
                        $em->flush();
                        $messageNotificator->notifyByEmail($subject,$from,$saveEmail,$body);

                    }

                }
            }
        }

        return new Response('ok');

    }

}
