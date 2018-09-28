<?php
// src/Cairn/UserCyclosBundle/Controller/NetworkController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\NetworkManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\FormType;                       
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Simple CRUD related to Account Types on cyclos side
 *
 *@Security("has_role('ROLE_SUPER_ADMIN')") 
 */
class NetworkController extends Controller
{   
    private $networkManager;

    public function __construct()
    {
        $this->networkManager = new NetworkManager();
    }

    public function indexAction(Request $request)//, $admin)
    {
        if($this->getParameter('kernel.environment' == 'test')){
            Cyclos\Configuration::setRootUrl($this->getParameter('cyclos_root_test_url'));
            Cyclos\Configuration::setAuthentication(
                $this->getParameter('cyclos_global_admin_username'),
                $this->getParameter('cyclos_global_admin_password')); 
        }else{//prod
            Cyclos\Configuration::setRootUrl($this->getParameter('cyclos_root_prod_url'));
            Cyclos\Configuration::setAuthentication($this->getParameter(),$this->getParameter()); 
        }
        $listNetworks = $this->get('cairn_user_cyclos_network_info')->getListNetworks(true);
        return $this->render('CairnUserCyclosBundle:Config/Network:index.html.twig',array('listNetworks' => $listNetworks));
    }

    //    public function listNetworksAction()
    //    {
    //
    //    }

    public function viewNetworkAction(Request $request)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('globalAdmin');
        $networkDTO = $this->get('cairn_user_cyclos_network_info')->getNetworkDTO($this->getParameter('cyclos_network_cairn'));
        return $this->render('CairnUserCyclosBundle:Config/Network:view.html.twig',array('network' => $networkDTO));

    }

    public function addNetworkAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 

        //      $network = new Network();
        $networkDTO = array();
        $initialData = new \stdClass();

        //        $em = $this->getDoctrine()->getManager();
        //        $form = $this->get('form.factory')->create(NetworkType::class, $network);
        $form = $this->createFormBuilder($networkDTO)
            ->add('name'          , TextType::class , array('label' => 'nom du réseau'))                                
            ->add('enabled'       , CheckboxType::class, array('label' => 'Réseau opérationnel', 'required' => false))
            ->add('currencyName'  , TextType::class , array('label' => 'Monnaie'))                                  
            ->add('currencySymbol', TextType::class , array('label' => 'Symbole')) 
            ->add('userAccount'   , TextType::class , array('label' => 'nom du compte utilisateur'))                                  
            ->add('systemAccount' , TextType::class , array('label' => 'nom du compte système')) 
            ->add('unlimitedAccount' , TextType::class , array('label' => 'nom du compte de crédits/débits')) 
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $networkDTO = (object) $dataForm;
                $networkDTO->internalName = preg_replace('/\s\s+/', '', $networkDTO->name); 
                //                $DTO = $network->fromEntityToDTO();
                $initialData->currencyName   = $dataForm['currencyName'];
                $initialData->currencySymbol = $dataForm['currencySymbol'];
                $initialData->userAccount    = $dataForm['userAccount'];
                $initialdata->systemAccount  = $dataform['systemAccount'];
                $initialdata->unlimitedAccount  = $dataform['unlimitedAccount'];


                $networkID = $this->networkManager->addNetwork($networkDTO,$initialData);
                //                $network->setCyclosID($networkID);
                //
                //                $em->persist($network);
                //                $em->flush();
                return $this->render('CairnUserCyclosBundle:Config/Network:index.html.twig');

            }
        }

        return $this->render('CairnUserCyclosBundle:Config/Network:add.html.twig', array('form' => $form->createView()));
    }

    public function editNetworkAction(Request $request, $name)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 

        $networkDTO = $this->get('cairn_user_cyclos_network_info')->getNetworkDTO($name);

        $networkArray = json_decode(json_encode($networkDTO), true);
        $form = $this->createFormBuilder($networkArray)
            ->add('name'          , TextType::class , array('label' => 'nom du réseau'))                                
            ->add('enabled'       , CheckboxType::class, array('label' => 'Réseau opérationnel', 'required' => false))
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $networkDTO = (object) $dataForm;
                $networkDTO->internalName = preg_replace('/\s\s+/', '', $networkDTO->name); 

                $networkID = $this->networkManager->editNetwork($networkDTO);

                return $this->render('CairnUserCyclosBundle:Config/Network:index.html.twig');

            }
        }

        return $this->render('CairnUserCyclosBundle:Config/Network:edit.html.twig', array('form' => $form->createView()));

    }

    public function removeNetworkAction(Request $request, $name)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 

        $networkID = $this->get('cairn_user_cyclos_network_info')->getNetworkID($name);
        $this->networkManager->removeNetwork($networkID);

        return $this->render('CairnUserCyclosBundle:Config/Network:index.html.twig');


    }

}
