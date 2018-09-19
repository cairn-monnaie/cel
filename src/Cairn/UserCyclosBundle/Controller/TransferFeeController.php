<?php
// src/Cairn/UserCyclosBundle/Controller/TransferFeeController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\TransferFeeManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//manage Forms
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\FormType;                       
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TransferFeeController extends Controller
{   
    private $transferFeeManager;

    public function __construct()
    {
        $this->transferFeeManager = new TransferFeeManager();
    }

    public function listTransferFeesAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $transferTypeID = $request->query->get('id');
        $transferTypeDTO = $this->get('cairn_user_cyclos_transfertype_info')->getTransferTypeDTOByID($transferTypeID);
        $transferTypeVO = $this->get('cairn_user_cyclos_transfertype_info')->getTransferTypeVO($transferTypeDTO);
        $listTransferFees = $this->get('cairn_user_cyclos_transferfee_info')->getListTransferFees($transferTypeVO);
        return $this->render('CairnUserCyclosBundle:Config/TransferFee:list.html.twig', array('listTransferFees' => $listTransferFees,'fromAccountType' => $transferTypeVO->from)); 

    }

    public function viewTransferFeeAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $id = $request->query->get('id');
        $transferFeeDTO = $this->get('cairn_user_cyclos_transferfee_info')->getTransferFeeDTOByID($id);       
        return $this->render('CairnUserCyclosBundle:Config/TransferFee:view.html.twig', array('transferFee' => $transferFeeDTO)); 

    }

    /*
     *
     */
    public function addTransferFeeAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();


        return new Response('ok');
        $transferFeeDTO = $this->get('cairn_user_cyclos_transferfee_info')->getTransferFeeDTO($this->getParameter('cyclos_transferFee_cairn'));
        unset($transferFeeDTO->id);
        $transferFeeDTO->name   = NULL;
        $transferFeeDTO->symbol = NULL;

        $transferFeeArray = json_decode(json_encode($transferFeeDTO), true);

        $form = $this->createFormBuilder($transferFeeArray)
            ->add('name'          , TextType::class , array('label' => 'nom de la devise'))                                
            ->add('symbol'        , TextType::class , array('label' => 'symbole associé', 'required' => true))                 
            ->add('precision', IntegerType::class , array('label' => 'Nombre de décimales')) 
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       

        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $transferFeeDTO = (object) $dataForm;
                $transferFeeDTO->internalName = preg_replace('/\s+/', '', $transferFeeDTO->name); 
                $transferFeeDTO->suffix  = $transferFeeDTO->symbol;
                //                try{
                $transferFeeID = $this->transferFeeManager->editTransferFee($transferFeeDTO);

                $session->getFlashBag()->add('info','la devise a été ajouté avec succès');
                return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_transferFee_view', array('id' => $transferFeeID));

                //              }catch(\Exception $e){
                //                  echo 'Erreur :' .  $e->getMessage();                                          
                //                  print_r($e->error);
                //              }

            }
        }
        return $this->render('CairnUserCyclosBundle:Config/TransferFee:add.html.twig', array('form' => $form->createView()));
    }

    public function editTransferFeeAction(Request $request, $id)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();

        $transferFeeDTO = $this->get('cairn_user_cyclos_transferfee_info')->getTransferFeeDTOByID($id);

        $listGeneratedTransferTypes = $this->get('cairn_user_cyclos_transferfee_info')->getListGeneratedTransferTypes($transferFeeDTO);

        //from the list of generated transfer types, retrieve the possible sources and receivers
        $sources = array(); $receivers = array();
        foreach($listGeneratedTransferTypes as $transferType)
        {
            $sources[] = $transferType->from;
            $receivers[] = $transferType->to;
        }
        $transferFeeArray = json_decode(json_encode($transferFeeDTO), true);

        $userGroups = $this->get('cairn_user_cyclos_group_info')->getList('MEMBER_GROUP');

        $form = $this->createFormBuilder($transferFeeArray)
            ->add('name'          , TextType::class , array('label' => 'nom du type de transfert'))                                
            ->add('amount'        , PercentType::class , array('label' => 'Pourcentage du transfert', 'required' => true))                 
            ->add('enabled'     ,   CheckboxType::class , array('label' => 'Frais actif','required'=>false)) 
            ->add('fromGroups'   ,  ChoiceType::class, array('label' => 'sources',
                                                             'choice_label' => 'name',
                                                             'choices' => $userGroups,
                                                             'multiple'=>true,
                                                             'expanded' => true
                                                         ))
            ->add('toGroups'   ,  ChoiceType::class, array('label' => 'destinations',
                                                             'choice_label' => 'name',
                                                             'choices' => $userGroups,
                                                             'multiple'=>true,
                                                             'expanded' => true
                                                         ))
            ->add('payer'      , ChoiceType::class, array('label' => 'A la charge de',
                                                          'choices' =>array('receveur'=>'DESTINATION','payeur'=>'SOURCE','système'=>'SYSTEM')))
            ->add('receiver'      , ChoiceType::class, array('label' => 'Reçoit le frais',
                                                          'choices' =>array('receveur'=>'DESTINATION','payeur'=>'SOURCE','système'=>'SYSTEM')))
            ->add('description' , TextareaType::class, array('required' => true))
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       

        if($request->isMethod('POST')){ //form filled and submitted
            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $transferFeeDTO = (object) $dataForm;
                $transferFeeDTO->internalName = preg_replace('/\s/', '', $transferFeeDTO->name); 
                try{
                    $transferFeeID = $this->transferFeeManager->editTransferFee($transferFeeDTO);
                    $session->getFlashBag()->add('info','Le frais de transfert a été mise à jour avec succès');
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_transferFee_view', array('id' => $transferFeeDTO->originalTransferType->id));
                }catch(\Exception $e){
                    echo 'Erreur :' .  $e->getMessage();                                          
                    $session->getFlashBag('error','le type de frais de transfert n\'a pas pu être modifié, pas de type de transfert généré correspondant. Il faut en créer un.');
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_transfertypeview',array('id',$id));
                }
            }
        }

        return $this->render('CairnUserCyclosBundle:Config/TransferFee:edit.html.twig', array('form' => $form->createView()));
    }


    public function removeTransferFeeAction(Request $request, $name, $fromNature, $toNature,$transferFee)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 

        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $transferFeeID = $this->get('cairn_user_cyclos_transferfee_info')->getTransferFeeID($name, $fromNature, $toNature, $transferFee);
        $this->transferFeeManager->removeTransferFee($transferFeeID);

        return $this->render('CairnUserCyclosBundle:Config/TransferFee:index.html.twig');


    }

}
