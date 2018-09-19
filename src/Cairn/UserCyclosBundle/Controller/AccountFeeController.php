<?php
// src/Cairn/UserCyclosBundle/Controller/AccountFeeController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\AccountFeeManager;

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


class AccountFeeController extends Controller
{   
    private $accountFeeManager;

    public function __construct()
    {
        $this->accountFeeManager = new AccountFeeManager();
    }

    public function indexAction()
    {
        return $this->render('CairnUserCyclosBundle:Config/AccountFee:index.html.twig');
    }

    public function listAccountFeesAction()
    {

    }

    public function viewAccountFeeAction($name)
    {


    }



    public function addAccountFeeAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $exempleDTO = $this->get('cairn_user_cyclos_accountfee_info')->getAccountFeeDTO('test');
        $accountFeeDTO                     = new \stdClass();
        $accountFeeDTO->className              = 'org.cyclos.model.banking.accountfees.AccountFeeDTO';
        $accountFeeDTO->accountType        = $this->get('cairn_user_cyclos_accounttype_info')->getAccountTypeData('userAccount','euro','USER');
        $accountFeeDTO->accountType->name  = 'userAccount';
        $accountFeeDTO->amount             = 2;
        $accountFeeDTO->chargeMode         = 'FIXED';
        $accountFeeDTO->balanceHandling    = 'FAIL_WHEN_NOT_LIMIT';
        $accountFeeDTO->dayMonth           = 5;
        $accountFeeDTO->enabled            = true;
        $accountFeeDTO->freeBase           = 0;
        $accountFeeDTO->paymentDirection   = 'TO_SYSTEM';
        $accountFeeDTO->recurrence         = new \stdClass();
        $accountFeeDTO->recurrence->field  = 'MONTHS'; 
        $accountFeeDTO->recurrence->amount = 12; 
        $accountFeeDTO->runMode            = 'SCHEDULED';
        $accountFeeDTO->transferType       = new \stdClass();
        $accountFeeDTO->transferType->name = 'Payment to systemAccount';

        try{
        $this->accountFeeManager->editAccountFee($accountFeeDTO);
        }catch(\Exception $e){
            echo 'Erreur :' .  $e->getMessage();                                          
            print_r($e->error);
        }

        return new Response('Account Fee ajouté');
        //récuperer le DTO d'un accountFee de type $accountFee
        //si la monnaie n'est associée à aucun compte (pas de DTO récupérable), on récupère le DTO d'une monnaie qui existe et on change la         propriété currency
        //
//        $currency = 'yuan'; $nature = 'SYSTEM';
//        $accountFeeDTO = $this->get('cairn_user_cyclos_accountfee_info')->getAccountFeeDTO(NULL,$currency,$nature);
//
//        if($accountFeeDTO == NULL){ //no account type associated with the given currency
//            $accountFeeDTO = $this->get('cairn_user_cyclos_accountfee_info')->getAccountFeeDTO(NULL,NULL,$nature);
//            $listCurrencies = $this->get('cairn_user_cyclos_currency_info')->getListCurrencies(); 
//            foreach($listCurrencies as $currencyVO){
//                if($currencyVO->name == $currency){
//                    $accountFeeDTO->currency = $currencyVO;
//                }
//            }
//        }
//        //unset id
//        unset($accountFeeDTO->id);
//        $accountFeeDTO->name         = NULL;
//
//        $accountFeeArray = json_decode(json_encode($accountFeeDTO), true);
//
//        $form = $this->createFormBuilder($accountFeeArray)
//            ->add('name'     , TextType::class , array('label' => 'nom du nouveau compte'))                                
//            ->add('limitType'     , ChoiceType::class , array('choices' => array('limité' => 'LIMITED' ,'illimité' => 'UNLIMITED')))
//            ->add('creditLimit'   , TextType::class , array('label' => 'débit maximal du compte'))                                
//            ->add('save'     , SubmitType::class)                              
//            ->getForm()
//            ;       
//        if($request->isMethod('POST')){ //form filled and submitted
//
//            $form->handleRequest($request);    
//            if($form->isValid()){
//                $dataForm = $form->getData();
//                $accountFeeDTO = (object) $dataForm;
//                $accountFeeDTO->internalName = preg_replace('/\s\s+/', '', $accountFeeDTO->name); 
//
//                $this->accountFeeManager->editAccountFee($accountFeeDTO);
//                return $this->render('CairnUserCyclosBundle:Config/AccountFee:index.html.twig');
//            }
//        }
//
//
//        return $this->render('CairnUserCyclosBundle:Config/AccountFee:add.html.twig', array('form' => $form->createView()));
    }

    /*
     * Edit only system account types : modify balance limits
     *@TODO : get the value of $currency from a form or retrieve accountfee just with the name 
     *
     */
    public function editAccountFeeAction(Request $request, $name)// ,$currency)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $nature = 'SYSTEM';  
        $currency = 'yuan';
        $accountFeeDTO = $this->get('cairn_user_cyclos_accountfee_info')->getAccountFeeDTO($name,$currency,$nature);
        if($accountFeeDTO == NULL){
            return new Response('Aucun compte n\'est associée à la recherche');
        }
        $accountFeeArray = json_decode(json_encode($accountFeeDTO), true);
//        $accountFeeArray = get_object_vars($accountFeeDTO);
        $form = $this->createFormBuilder($accountFeeArray)
            ->add('name'          , TextType::class , array('label' => 'nom interne du compte'))                                
            ->add('limitType'     , ChoiceType::class , array('choices' => array('limité' => 'LIMITED' ,'illimité' => 'UNLIMITED')))
            ->add('creditLimit'   , TextType::class , array('label' => 'débit maximal du compte'))                                
            ->add('save'          , SubmitType::class)                              
            ->getForm()
                ;       
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $accountFeeDTO = (object) $dataForm;
                $accountFeeDTO->internalName = preg_replace('/\s\s+/', '', $accountFeeDTO->name); 

                try{
                $accountFeeID = $this->accountFeeManager->editAccountFee($accountFeeDTO);
                }catch(\Exception $e){
                    echo 'Erreur :' .  $e->getMessage();                                          
                    print_r($e->error);
                }
                return $this->render('CairnUserCyclosBundle:Config/AccountFee:index.html.twig');

            }
        }

        return $this->render('CairnUserCyclosBundle:Config/AccountFee:edit.html.twig', array('form' => $form->createView()));

    }

    public function removeAccountFeeAction(Request $request, $name, $currency, $nature)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 

        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $accountFeeID = $this->get('cairn_user_cyclos_accountfee_info')->getAccountFeeID($name, $currency, $nature);
        $this->accountFeeManager->removeAccountFee($accountFeeID);

        return $this->render('CairnUserCyclosBundle:Config/AccountFee:index.html.twig');


    }

}
