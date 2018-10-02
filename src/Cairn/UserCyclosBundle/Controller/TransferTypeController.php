<?php
// src/Cairn/UserCyclosBundle/Controller/TransferTypeController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\TransferTypeManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\FormType;                       
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\IntegerType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;                                          
use Symfony\Component\Form\FormEvents;


/**
 * This class is a simple CRUD for transfer type Cyclos objects
 *
 */
class TransferTypeController extends Controller
{   
    /**
     * Deals with all transfer type actions to operate 
     *@var TransferType $transferTypeManager
     */
    private $transferTypeManager;

    public function __construct()
    {
        $this->transferTypeManager = new TransferTypeManager();
    }

    public function indexAction(Request $request)
    {
    }

    public function listTransferTypesAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();

        $accountType = $session->get('accountTypeDTO');
        //        $accountType = $request->query->get('accountType');
        //        $accountType = json_decode(json_encode($accountType));//convert recursively array into object
        $accountTypeVO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeVO($accountType);//because not good format(unrecognized by cyclos);
        //
        $listTransferTypes = $this->get('cairn_user_cyclos_transfertype_info')->getListTransferTypes($accountTypeVO);
        return $this->render('CairnUserCyclosBundle:Config/TransferType:list.html.twig', array('listTransferTypes' => $listTransferTypes)); 

    }

    public function viewTransferTypeAction(Request $request, $id, $_format)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $transferTypeDTO = $this->get('cairn_user_cyclos_transfertype_info')->getTransferTypeDTOByID($id);       
        $transferTypeVO = $this->get('cairn_user_cyclos_transfertype_info')->getTransferTypeVO($transferTypeDTO);       
//        var_dump($transferTypeVO);
//        $transferFees = $this->get('cairn_user_cyclos_transferfee_info')->getListTransferFeesVO($transferTypeVO);
//        var_dump($transferFees); 
////        //only one transferFee per transfertype : configuration rule
//        if($transferFees){
//            $transferFee = $transferFees[0] ;
//            $transferFeeDTO = $this->get('cairn_user_cyclos_transferfee_info')->getTransferFeeDTOByID($transferFee->id);
//        }
//        else{
//            $transferFeeDTO = NULL;
//        }
        
        if($_format == 'json'){
            return $this->json(array('transferType' => $transferTypeDTO));
        }
        return $this->render('CairnUserCyclosBundle:Config/TransferType:view.html.twig', array('transferType' => $transferTypeDTO));

    }

    /*
     *technique to add new entity : get entityData with getDataFromNew then retrieve dto, fill properties and save
     * $nature = PAYMENT/GENERATED
     *@TODO : maxInstallments et toName doivent dépendre de la réponse précédente
     */
    public function addTransferTypeAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();

        $accountTypeDTO = $session->get('accountTypeDTO');

        $dataParams = new \stdClass();
        $accountTypeVO = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeVO($accountTypeDTO);
        $dataParams->fromAccountType = $accountTypeVO;


        $form = $this->createFormBuilder()
            ->add('nature'   , ChoiceType::class , array('label' => 'nature du type de transfert', 'choices' => array('géneration' => 'GENERATED' ,'paiement' => 'PAYMENT'))) 
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       
        if($request->isMethod('POST')){ 

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();

                $dataParams->nature = $dataForm['nature'];

                $data = $this->transferTypeManager->dataForNew($dataParams); 
                $transferTypeDTO = $data->dto;
                return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_transfertype_complete',array('transferTypeDTO' => $transferTypeDTO));
            }
        }
        return $this->render('CairnUserCyclosBundle:Config/TransferType:add.html.twig', array('form' => $form->createView()));
    }

    /*
     *@TODO : update form listeners to adapt beneficiaries list from user input in toNature field
     */

    public function fillTransferTypeAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();
        $transferTypeDTO = $request->query->get('transferTypeDTO');

        $transferTypeArray = json_decode(json_encode($transferTypeDTO), true);

        $builder = $this->createFormBuilder($transferTypeArray)
            ->add('name'          , TextType::class , array('label' => 'nom du type de transfert'))                                
            ->add('allowsRecurringPayments'  , CheckboxType::class , array('label' => 'Virements automatiques réguliers'))                 
            ->add('toNature'   , ChoiceType::class , array('label' => 'nature du bénéficiaire', 'choices' => array('utilisateur' => 'USER' ,'système' => 'SYSTEM'))); 



        $formModifier = function (FormInterface $form,  $toNature = NULL){
            $session = new Session();
            if($toNature == NULL){
                $beneficiaries = array();
            }else{
                $beneficiaries = $this->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes(NULL,$session->get('currency'),$toNature);
            }
            $form->add('toName'   , ChoiceType::class , array('label' => 'nom du compte bénéficiaire',
                'choice_label' => 'name',
                'choices' => $beneficiaries));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. $transferTypeArray
                $data = $event->getData();
                if(null == $data){
                    return;
                }
                if(!array_key_exists('toNature',$data)){
                    $formModifier($event->getForm(),NULL);               
                }else{
                    $formModifier($event->getForm(),$data['toNature']);
                }
            }
        );

        $builder->get('toNature')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $toNature = $event->getForm()->getData();
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $toNature);
            }
        );

        $builder->add('save'          , SubmitType::class);                              
        $form = $builder->getForm();

        if($request->isMethod('POST')){ 

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $transferTypeDTO = (object) $dataForm;
                $transferTypeDTO->internalName = preg_replace('/\s+/', '', $transferTypeDTO->name); 

                $transferTypeDTO->channels[] = 'webServices';
                $transferTypeDTO->to               = new \stdClass();
                $transferTypeDTO->to->nature       = $dataForm['toNature'];
                $transferTypeDTO->to->internalName = $dataForm['toName'];
                //                try{
                $transferTypeID =  $this->transferTypeManager->editTransferType($transferTypeDTO);

                $session->getFlashBag()->add('info','le transfert type a bien été ajouté');
                return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_transfertype_view', array('id' => $transferTypeID));

                //              }catch(\Exception $e){
                //                  echo 'Erreur :' .  $e->getMessage();                                          
                //                  print_r($e->error);
                //              }
            }
        }

        return $this->render('CairnUserCyclosBundle:Config/TransferType:add.html.twig', array('form' => $form->createView()));

    }
    public function editTransferTypeAction(Request $request, $id)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();

        $transferTypeDTO = $this->get('cairn_user_cyclos_transfertype_info')->getTransferTypeDTOByID($id);
        $transferTypeArray = json_decode(json_encode($transferTypeDTO), true);

        $form = $this->createFormBuilder($transferTypeArray)
            ->add('name'          , TextType::class , array('label' => 'nom du type de transfert'))
            ->add('enabled'       , CheckboxType::class,array('label' => 'Actif', 'required'=>false))
            ->add('allowsRecurringPayments'  , CheckboxType::class , array('label' => 'Virements automatiques réguliers', 'required' => false))                 
            //            ->add('allowsScheduledPayments'  , CheckboxType::class , array('label' => 'Virements automatiques à date fixée', 'required' => false))              
            //            ->add('maxInstallments', IntegerType::class , array('label' => 'Nombre maximal de dates fixées')) 
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       

        if($request->isMethod('POST')){ //form filled and submitted
            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $transferTypeDTO = (object) $dataForm;
                $transferTypeDTO->internalName = preg_replace('/\s+/', '', $transferTypeDTO->name); 
                try{
                    $transferTypeID = $this->transferTypeManager->editTransferType($transferTypeDTO);
                    $session->getFlashBag()->add('info','Le type de transfert a été mis à jour');
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_transfertype_view', array('id' => $transferTypeID));
                }catch(\Exception $e){
                    echo 'Erreur :' .  $e->getMessage();                                          
                    print_r($e->error);
                }
            }
        }

        return $this->render('CairnUserCyclosBundle:Config/TransferType:edit.html.twig', array('form' => $form->createView(),'transferType'=>$transferTypeDTO));
    }


    public function removeTransferTypeAction(Request $request, $name, $fromNature, $toNature,$currency)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 

        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $transferTypeID = $this->get('cairn_user_cyclos_transfertype_info')->getTransferTypeID($name, $fromNature, $toNature, $currency);
        $this->transferTypeManager->removeTransferType($transferTypeID);

        return $this->render('CairnUserCyclosBundle:Config/TransferType:index.html.twig');


    }

}
