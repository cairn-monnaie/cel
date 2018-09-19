<?php
// src/Cairn/UserCyclosBundle/Controller/CurrencyController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\CurrencyManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//manage Forms
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\FormType;                       
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\IntegerType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class CurrencyController extends Controller
{   
    private $currencyManager;

    public function __construct()
    {
        $this->currencyManager = new CurrencyManager();
    }

    public function listCurrenciesAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));


        $listCurrencies = $this->get('cairn_user_cyclos_currency_info')->getListCurrencies();
        return $this->render('CairnUserCyclosBundle:Config/Currency:list.html.twig', array('listCurrencies' => $listCurrencies)); 

    }

    public function viewCurrencyAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $id = $request->query->get('id');
        $currencyDTO = $this->get('cairn_user_cyclos_currency_info')->getCurrencyDTOByID($id);       
        return $this->render('CairnUserCyclosBundle:Config/Currency:view.html.twig', array('currency' => $currencyDTO)); 

    }

    /*
     *
     */
    public function addCurrencyAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();

        $currencyDTO = $this->get('cairn_user_cyclos_currency_info')->getCurrencyDTO($this->container->getParameter('cyclos_currency_cairn'));
        unset($currencyDTO->id);
        $currencyDTO->name   = NULL;
        $currencyDTO->symbol = NULL;

        $currencyArray = json_decode(json_encode($currencyDTO), true);

        $form = $this->createFormBuilder($currencyArray)
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
                $currencyDTO = (object) $dataForm;
                $currencyDTO->internalName = preg_replace('/\s+/', '', $currencyDTO->name); 
                $currencyDTO->suffix  = $currencyDTO->symbol;
                //                try{
                $currencyID = $this->currencyManager->editCurrency($currencyDTO);

                $session->getFlashBag()->add('info','la devise a été ajouté avec succès');
                return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_currency_view', array('id' => $currencyID));

                //              }catch(\Exception $e){
                //                  echo 'Erreur :' .  $e->getMessage();                                          
                //                  print_r($e->error);
                //              }

            }
        }

        return $this->render('CairnUserCyclosBundle:Config/Currency:add.html.twig', array('form' => $form->createView()));

    }

    public function editCurrencyAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();
        $id = $request->query->get('id');

        $currencyDTO = $this->get('cairn_user_cyclos_currency_info')->getCurrencyDTOByID($id);
        $currencyArray = json_decode(json_encode($currencyDTO), true);

        $form = $this->createFormBuilder($currencyArray)
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
                $currencyDTO = (object) $dataForm;
                $currencyDTO->internalName = preg_replace('/\s+/', '', $currencyDTO->name); 
                try{
                    $currencyID = $this->currencyManager->editCurrency($currencyDTO);
                    $session->getFlashBag()->add('info','La devise a été mise à jour');
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_currency_view', array('id' => $currencyID));
                }catch(\Exception $e){
                    echo 'Erreur :' .  $e->getMessage();                                          
                    print_r($e->error);
                }
            }
        }

        return $this->render('CairnUserCyclosBundle:Config/Currency:edit.html.twig', array('form' => $form->createView()));
    }


    public function removeCurrencyAction(Request $request, $name, $fromNature, $toNature,$currency)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 

        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $currencyID = $this->get('cairn_user_cyclos_currency_info')->getCurrencyID($name, $fromNature, $toNature, $currency);
        $this->currencyManager->removeCurrency($currencyID);

        return $this->render('CairnUserCyclosBundle:Config/Currency:index.html.twig');


    }

}
