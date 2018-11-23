<?php
// src/Cairn/UserCyclosBundle/Controller/GroupController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\GroupManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//manage Forms
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\FormType;                       
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Simple CRUD related to groups on cyclos side
 *
 */
class GroupController extends Controller
{   
    private $groupManager;

    public function __construct()
    {
        $this->groupManager = new GroupManager();
    }

    public function listGroupsAction()
    {

    }

    public function viewGroupAction($name)
    {


    }


    public function addGroupAction(Request $request,$type)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $groupDTO = $this->get('cairn_user_cyclos_group_info')->getGroupDTO(NULL,$type);

        //unset id
        unset($groupDTO->id);
        $groupDTO->name         = NULL;

        $groupArray = json_decode(json_encode($groupDTO), true);

        $form = $this->createFormBuilder($groupArray)
            ->add('name'     , TextType::class , array('label' => 'nom du groupe'))                                
            ->add('save'     , SubmitType::class)                              
            ->getForm()
            ;       
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $groupDTO = (object) $dataForm;
                $groupDTO->internalName = preg_replace('/\s/', '', $groupDTO->name); 

                $this->groupManager->editGroup($groupDTO);
                return $this->render('CairnUserCyclosBundle:Config/Group:index.html.twig');
            }
        }


        return $this->render('CairnUserCyclosBundle:Config/Group:add.html.twig', array('form' => $form->createView()));
    }

    public function editGroupAction(Request $request, $name)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $groupDTO = $this->get('cairn_user_cyclos_group_info')->getGroupDTO($name);

        $groupArray = json_decode(json_encode($groupDTO), true);
        $form = $this->createFormBuilder($groupArray)
            ->add('name'          , TextType::class , array('label' => 'nom du groupe'))                                
            ->add('nature'   , ChoiceType::class , array('label' => 'Type de groupe', 'choices' => array('Administrateurs du réseau' => 'ADMIN_GROUP' ,'Adhérents' => 'MEMBER_GROUP'))) 
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $groupDTO = (object) $dataForm;
                $groupDTO->internalName = preg_replace('/\s\s+/', '', $groupDTO->name); 

                $groupID = $this->groupManager->editGroup($groupDTO);
            }
            return $this->render('CairnUserCyclosBundle:Config/Group:index.html.twig');

        }

        return $this->render('CairnUserCyclosBundle:Config/Group:edit.html.twig', array('form' => $form->createView()));

    }

    public function removeGroupAction(Request $request, $name)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $groupDTO = $this->get('cairn_user_cyclos_group_info')->getGroupDTO($name);

        //to remove a group, must not contain users
        if($this->get('cairn_user_cyclos_group_info')->isEmpty($groupDTO->name)){
            $this->groupManager->removeGroup($groupDTO->id);
        }
        else{ 
            return new Response('Can\'t remove the group, must not contain users');
        }

        return $this->render('CairnUserCyclosBundle:Config/Group:index.html.twig');

    }

}
