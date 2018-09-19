<?php
// src/Cairn/UserCyclosBundle/Controller/SystemConfigController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\Network;
use Cairn\UserCyclosBundle\Entity\NetworkManager;
use Cairn\UserCyclosBundle\Entity\GroupManager;
use Cairn\UserCyclosBundle\Entity\TransferTypeManager;
use Cairn\UserCyclosBundle\Entity\BankingManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//manage Forms
use Cairn\UserCyclosBundle\Form\NetworkType;
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\FormType;                       
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SystemConfigController extends Controller
{   
    private $networkManager;
    private $groupManager;
    private $transferTypeManager;
    private $bankingManager;

    public function __construct()
    {
        $this->networkManager = new NetworkManager();
        $this->groupManager = new GroupManager();
        $this->transferTypeManager = new TransferTypeManager();
        $this->bankingManager = new BankingManager();
    }

    public function indexAction()
    {
        return $this->render('CairnUserCyclosBundle:Config/Network:index.html.twig');
    }

    public function listNetworksAction()
    {

    }

    public function viewNetworkAction($name)
    {


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
                $initialData->systemAccount  = $dataForm['systemAccount'];


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
    public function switchToNetworkAction($name)
    {
        if ($name == 'globalAdmin'){
            $internalName = 'global';
        }
        else{
            $network = $this->get('cairn_user_cyclos_network_info')->getNetworkData($name);
            $internalName = $network->dto->internalName;
        }
        Cyclos\Configuration::setRootUrl('http://127.0.0.1:8080/cyclos/' . $internalName);
        return new Response('Switch to network ' . $name);
    }


    public function addGroupAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        //org.cyclos.model.users.groups.AdminGroupDTO 
        $groupID = $this->get('cairn_user_cyclos_group_info')->getGroupID('groupTest');
        $group = new \stdClass();
        $group->id = $groupID;
        $group->type = 'admin';
        try{
            $groupData = $this->groupManager->addGroup($groupID);
            $groupData->dto->nature = 'MEMBER_GROUP'; 
            $groupData->dto->internalName = 'groupTest2';
            $groupData->dto->name = $groupData->dto->internalName;
            $groupData->dto->id = $groupData->dto->id + 10; 
            $this->groupManager->saveGroup($groupData->dto);
            //        $group->id = $id;

            //        $group->managedNetworks = array('Test2','Test');
            //        $group->name = 'Tullins';
            //        $group->internalName = $group->name;
            //        $group->enabled = true;
            //        $group->nature = "adminGroup";
            //        $group->adminType = 'network';
            //        $group->canRegisterNetworks = false;

        }catch(\Exception $e){
            //  print_r($gd);
            echo 'Erreur :' .  $e->getMessage();                                          
            print_r($e->error);
        }
        return new Response(print_r($groupData->dto) . 'Groupe ajouté');

    }

    public function removeGroupAction($name)
    {
    }

    /*
     *technique to add new entity : get entityData with getDataFromNew then retrieve dto, fill properties and save
     *
     */
    public function addTransferTypeAction()
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->switchToNetworkAction('Test5');

        $dataParams = new \stdClass();
        $dataParams->nature = 'PAYMENT';
        $dataParams->fromAccountType = 'organization';

        try{
            $data = $this->transferTypeManager->dataForNew($dataParams); 
            $newDTO = $data->dto;
            $newDTO->name = 'testTransferType';
            $newDTO->channels[] = 'webServices';
            $newDTO->allowsRecurringPayments = true;    
            $newDTO->to = new \stdClass();
            $newDTO->to->nature = 'USER';
            $newDTO->to->internalName = 'test';

            $this->transferTypeManager->saveTransferType($newDTO);
        }catch(\Exception $e){
            echo 'Erreur :' .  $e->getMessage();                                          
            print_r($e->error);
        }
        return new Response(print_r($data->dto) . 'TransferType récupéré');

    }

    /*
     *configure all the transfer types by allowing WebServices channel
     *
     */
    public function configureTransferTypesAction()
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->switchToNetworkAction('Test5');

        try{
            $this->transferTypeManager->configureTransferTypes();
        }catch(\Exception $e){
            echo 'Erreur :' .  $e->getMessage();                                          
            print_r($e->error);
        }
        return new Response('TransferTypes configurés pour les WebServices');

    }
    /*
     *makes payment from debit account to $userAccount
     *@param string $name
     *
     */
    public function makePaymentAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->switchToNetworkAction('Test5');

        $from   = $request->query->get('from');
        $to     = $request->query->get('to');
        $type   = $request->query->get('type');
        $amount = $request->query->get('amount');
        $transferType = $this->get('cairn_user_cyclos_banking_info')->getTransferType($type);

        $bankingService = $this->get('cairn_user_cyclos_banking_info');
        try{
            if(($type == 'credit') || ($type == 'systemToUser')){
                $paymentData = $bankingService->getPaymentData('SYSTEM',array('username' => $to),$transferType);
            }
            elseif(($type == 'debit') || ($type == 'userToSystem')){
                $paymentData = $bankingService->getPaymentData(array('username' => $from),'SYSTEM',$transferType);
            }else{ //userToUser : only remaining option
                $paymentData = $bankingService->getPaymentData(array('username' => $from),array('username' => $to),$transferType);
            }
            $res = $this->bankingManager->makePayment($paymentData,$amount,$transferType);

        }catch(\Exception $e){
            echo 'Erreur :' .  $e->getMessage();                                          
            print_r($e->error);
        }

        return new Response(print_r($res) . 'crédit effectué');
    }



}
