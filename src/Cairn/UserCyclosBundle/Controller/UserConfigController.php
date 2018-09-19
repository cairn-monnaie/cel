<?php
// src/Cairn/UserCyclosBundle/Controller/UserController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

use Doctrine\ORM\QueryBuilder;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\User;
use Cairn\UserCyclosBundle\Entity\UserManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Cairn\UserCyclosBundle\Form\UserType;
use Cairn\UserCyclosBundle\Form\RegistrationType;
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\FormType;                       
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserConfigController extends Controller
{   

    private $userManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    /*
     *index for a given network
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listUsers = $em->getRepository('CairnUserBundle:User')->myFindByRole(array('ROLE_PRO'));
        return $this->render('CairnUserCyclosBundle:Config/User:index.html.twig',array('listUsers' => $listUsers));
    }

    public function listUsersAction()
    {

    }

    public function viewUserAction(Request $request)
    {
        $session = new Session();
        $userID = $request->query->get('userID');
        $user = $this->getDoctrine()->getManager()->getRepository('CairnUserBundle:User')->findOneBy(array('id' => $userID));

        $session->set('user',$user);
        return $this->render('CairnUserCyclosBundle:Config/User:view.html.twig',array('user' =>$user));

    }



    public function editUserAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $id = $request->query->get('id');
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $user = $userRepo->findOneBy(array('id' => $id));

        if($user == NULL){
            $session->getFlashBag()->add('erreur', 'L\'utilisateur recherché n\'est pas enregistré !');
            return $this->redirectToRoute('cairn_user_cyclos_usermanagement_home');
        }
        $form = $this->get('form.factory')->create(UserType::class, $user);

        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $session = $request->getSession();
                $em->persist($user);
                $em->flush();
                $session->getFlashBag()->add('Info', 'Le profil a bien été mis à jour !');
                return $this->redirectToRoute('cairn_user_cyclos_usermanagement_view',array('userID' => $user->getID()));

            }
        }

        return $this->render('CairnUserCyclosBundle:Config/User:edit.html.twig', array('form' => $form->createView()));

    }


    /*
     *@TODO : error case : the group validated is the current one
     */ 
    public function changeGroupUserAction(Request $request, $name, $groupType)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        //get the list of possible groups
        //        $groupType = 'MEMBER_GROUP';
        $listGroups = $this->get('cairn_user_cyclos_group_info')->getList($groupType);
        $listNames = array();
        foreach($listGroups as $group)
        {
            $listNames[$group->name] = $group->name;
        }

        $form = $this->createFormBuilder()
            ->add('groupName'   , ChoiceType::class , array('label' => 'Type', 'choices' => $listNames)) 
            ->add('save',      SubmitType::class)
            ->getForm();
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $changeGroupDTO = new \stdClass();

                $groupVO = $this->get('cairn_user_cyclos_group_info')->getGroupData($dataForm['groupName']);
                $userVO  = $this->get('cairn_user_cyclos_user_info')->getUserVO($name);
                $changeGroupDTO->group = $groupVO;
                $changeGroupDTO->user  = $userVO;

                try{
                    $res = $this->userManager->changeGroupUser($changeGroupDTO);

                }catch(\Exception $e){
                    echo 'Erreur :' .  $e->getMessage();                       
                    print_r($e->error);}
                    return new Response('Bien ouej');

            }
        }
        return $this->render('CairnUserCyclosBundle:Config/User:changeGroup.html.twig', array('form' => $form->createView()));


    }



}
