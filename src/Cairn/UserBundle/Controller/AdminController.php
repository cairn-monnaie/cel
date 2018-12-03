<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserCyclosBundle\Entity\UserManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * An email is sent to the user being (re)activated
     *
     * @throws  AccessDeniedException Current user making request is not a referent of the user being involved
     * @Method("GET")
     */ 
    public function blockUserAction(Request $request, User $user, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $currentUser = $this->getUser();

        if(! $user->hasReferent($currentUser)){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }elseif(!$user->isEnabled()){
            $session->getFlashBag()->add('info','L\'espace membre de ' . $user->getName() . ' est déjà bloqué.');
            return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format, 'id' => $user->getID()));
        }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){

                $subject = 'Votre espace membre Cairn a été désactivé';
                $body = 'Votre espace membre a été bloqué par ' .$currentUser->getName();

                $this->get('cairn_user.access_platform')->disable(array($user),$subject,$body);
                $session->getFlashBag()->add('success','L\'utilisateur ' . $user->getName() . ' a été bloqué avec succès. Il ne peut plus accéder à la plateforme.');
                $em->flush();
            }

            return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format, 'id' => $user->getID()));

        }

        $responseArray = array('user' => $user,'form'=> $form->createView());

        if($_format == 'json'){
            return $this->json($responseArray);
        }
        return $this->render('CairnUserBundle:User:block.html.twig', $responseArray);
    }


    /**
     * Set the enabled attribute of user with provided ID to true
     *
     * An email is sent to the user being (re)activated
     *
     * @throws  AccessDeniedException Current user trying to activate access is not a referent of the user being involved
     * @Method("GET")
     */ 
    public function activateUserAction(Request $request, User $user, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $currentUser = $this->getUser();

        if(! $user->hasReferent($currentUser)){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }elseif($user->isEnabled()){
            $session->getFlashBag()->add('info','L\'espace membre de ' . $user->getName() . ' est déjà accessible.');
            return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format, 'id' => $user->getID()));
        }elseif($user->getConfirmationToken()){
            throw new AccessDeniedException('Email non confirmé, cet utilisateur ne peut être validé');
        }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){

                $messageNotificator = $this->get('cairn_user.message_notificator');

                //if first activation : create user in cyclos and ask if generate card now
                if(! $user->getLastLogin()){
                    try{
                        $userVO = $this->get('cairn_user_cyclos_user_info')->getUserVO($user->getCyclosID());

                        if($userVO){
                            //remove cyclos user if one already exists in order to provide new password
                            //as changing password for current Cyclos user needs current password (we don't know about) 
                            $params = new \stdClass();
                            $params->status = 'REMOVED';                                       
                            $params->user = $this->container->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
                            $this->userManager->changeStatusUser($params);  
                        }

                    }catch(\Exception $e){
                        if(! $e->errorCode == 'ENTITY_NOT_FOUND'){
                            throw $e;
                        }
                    }


                    //create cyclos user
                    $userDTO = new \stdClass();                                    
                    $userDTO->name = $user->getName();                             
                    $userDTO->username = $user->getUsername();                     
                    $userDTO->internalName = $user->getUsername();                 
                    $userDTO->login = $user->getUsername();                        
                    $userDTO->email = $user->getEmail();                           

                    $temporaryPassword = User::randomPassword();
                    $user->setPlainPassword($temporaryPassword);

                    $password = new \stdClass();                                   
                    $password->assign = true;                                      
                    $password->type = 'login';
                    $password->value = $temporaryPassword;                  
                    $password->confirmationValue = $password->value;
                    $userDTO->passwords = $password;                               

                    if($user->hasRole('ROLE_PRO')){                                        
                        $groupName = $this->getParameter('cyclos_group_pros');  
                    }else{                                                                 
                        $groupName = $this->getParameter('cyclos_group_network_admins');
                    }   

                    $groupVO = $this->get('cairn_user_cyclos_group_info')->getGroupVO($groupName);

                    //if webServices channel is not added, it is impossible to update/remove the cyclos user entity from 3rd party app
                    $webServicesChannelVO = $this->get('cairn_user_cyclos_channel_info')->getChannelVO('webServices');

                    $newUserCyclosID = $this->userManager->addUser($userDTO,$groupVO,$webServicesChannelVO);
                    $user->setCyclosID($newUserCyclosID);

                    $body = $this->renderView('CairnUserBundle:Emails:welcome.html.twig',array('user'=>$user));
                    $subject = 'Plateforme numérique du Cairn';
                    $this->get('cairn_user.access_platform')->enable(array($user), $subject, $body);

                    $session->getFlashBag()->add('success','L\'utilisateur ' . $user->getName() . ' a été activé. Il peut accéder à la plateforme.');
                    $em->flush();
                    return $this->render('CairnUserBundle:Card:generate_card.html.twig',array('user'=>$user,'card'=>$user->getCard()));

                }else{
                    $this->get('cairn_user.access_platform')->enable(array($user));
                    $em->flush();
                }
            }

            $session->getFlashBag()->add('success','L\'utilisateur ' . $user->getName() . ' a été activé. Il peut accéder à la plateforme.');
            return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format, 'id' => $user->getID()));
        }

        $responseArray = array('user' => $user,'form'=> $form->createView());

        if($_format == 'json'){
            return $this->json($responseArray);
        }
        return $this->render('CairnUserBundle:User:activate.html.twig', $responseArray);

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
        if(!$user->hasRole('ROLE_PRO')){
            throw new AccessDeniedException('Seuls les professionnels doivent avoir des référents assignés manuellement.');
        }

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $choices = $userRepo->myFindByRole(array('ROLE_ADMIN'));

        $messageNotificator = $this->get('cairn_user.message_notificator');
        $from = $messageNotificator->getNoReplyEmail();

        $form = $this->createFormBuilder()
            ->add('singleReferent', EntityType::class, array(
                'class'=>User::class,
                'constraints'=>array(                              
                    new Assert\NotNull()                           
                ),
                'choice_label'=>'name',
                'choice_value'=>'username',
                'data'=> $user->getLocalGroupReferent(),
                'choices'=>$choices,
                'expanded'=>true,
                'required'=>false
            ))
            ->add('cancel', SubmitType::class, array('label'=>'Annuler'))
            ->add('save', SubmitType::class, array('label'=>'Assigner'))
            ->getForm();

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->get('save')->isClicked()){
                $referent = $form->get('singleReferent')->getData();
                $currentAdminReferent = $user->getLocalGroupReferent();

                if($referent && !$referent->hasRole('ROLE_ADMIN')){
                    throw new AccessDeniedException('Seul un groupe local peut être assigné via ce formulaire.');
                }
                if($referent){
                    if($user->hasReferent($referent)){
                        $session->getFlashBag()->add('info',
                            $referent->getName() . ' est déjà le groupe local référent de '.$user->getName());
                        return new RedirectResponse($request->getRequestUri());
                    }
                }

                if(!$currentAdminReferent && !$referent){
                    $session->getFlashBag()->add('info',$user->getName(). ' n\'avait pas de groupe local référent.');
                    return new RedirectResponse($request->getRequestUri());
                }

                if($currentAdminReferent){
                    $to = $currentAdminReferent->getEmail();
                    $subject = 'Référencement Pro';
                    $body = 'Vous n\'êtes plus référent du professionnel ' . $user->getName();
                    $messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $user->removeReferent($currentAdminReferent);
                }
                if($referent){
                    $user->addReferent($referent);

                    $to = $referent->getEmail();
                    $subject = 'Référencement Pro';
                    $body = 'Vous êtes désormais référent du professionnel ' . $user->getName();
                    $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                    $session->getFlashBag()->add('success',
                        $referent->getName() . ' est désormais référent de '.$user->getName());
                }else{
                    $session->getFlashBag()->add('success',
                        $user->getName(). ' n\'a plus de groupe local référent.');
                }

                $em->flush();
                return $this->redirectToRoute('cairn_user_profile_view',array('id'=>$user->getID()));
            }else{
                return $this->redirectToRoute('cairn_user_profile_view',array('id'=>$user->getID()));
            }
        }
        return $this->render('CairnUserBundle:User:add_referent.html.twig',array('form'=>$form->createView(),'user'=>$user));
    }   


}
