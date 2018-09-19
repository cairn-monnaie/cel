<?php
// src/Cairn/UserCyclosBundle/Controller/ProductController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\ProductManager;

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


class ProductController extends Controller
{   
    private $productManager;

    public function __construct()
    {
        $this->productManager = new ProductManager();
    }

    public function listProductsAction()
    {

    }

    public function viewProductAction($name)
    {


    }

    /*
     *technique to add new entity : get entityData with getDataFromNew then retrieve dto, fill properties and save
     *@TODO : maxInstallments et toName doivent répondre de la réponse précédente
     */
    public function addProductAction(Request $request)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $productDTO = $this->get('cairn_user_cyclos_product_info')->getProductDTO('members', array('Users'));
       $this->productManager->editProduct($productDTO); 
        unset($productDTO->id);
        $productArray = json_decode(json_encode($productDTO), true);

        $form = $this->createFormBuilder($productArray)
            ->add('name'          , TextType::class , array('label' => 'nom du type de transfert'))                                
            ->add('allowsRecurringPayments'  , CheckboxType::class , array('label' => 'Virements automatiques réguliers'))                 
            ->add('allowsScheduledPayments'  , CheckboxType::class , array('label' => 'Virements automatiques à date fixée'))              
            ->add('maxInstallments', IntegerType::class , array('label' => 'Nombre maximal de dates fixées')) 
            ->add('toNature'   , ChoiceType::class , array('label' => 'nature du bénéficiaire', 'choices' => array('utilisateur' => 'USER' ,'système' => 'SYSTEM'))) 
            ->add('toName' , TextType::class , array('label' => 'nom du compte bénéficiaire')) 
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $productDTO = (object) $dataForm;
                $productDTO->internalName = preg_replace('/\s\s+/', '', $productDTO->name); 

                $productDTO->channels[] = 'webServices';
                $productDTO->to               = new \stdClass();
                $productDTO->to->nature       = $dataForm['toNature'];
                $productDTO->to->internalName = $dataForm['toName'];

                //                try{
                $this->productManager->saveProduct($productDTO);
                return $this->render('CairnUserCyclosBundle:Config/Product:index.html.twig');

                //              }catch(\Exception $e){
                //                  echo 'Erreur :' .  $e->getMessage();                                          
                //                  print_r($e->error);
                //              }

            }
        }

        return $this->render('CairnUserCyclosBundle:Config/Product:add.html.twig', array('form' => $form->createView()));

    }

    public function editProductAction(Request $request, $name)//, $fromNature, $toNature)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $productDTO = $this->get('cairn_user_cyclos_product_info')->getProductDTO($name,'SYSTEM','USER','euro');// $fromNature, $toNature);

        $productArray = json_decode(json_encode($productDTO), true);

        $form = $this->createFormBuilder($productArray)
            ->add('name'          , TextType::class , array('label' => 'nom du type de transfert'))                                
            ->add('allowsRecurringPayments'  , CheckboxType::class , array('label' => 'Virements automatiques réguliers', 'required' => false))                 
            ->add('allowsScheduledPayments'  , CheckboxType::class , array('label' => 'Virements automatiques à date fixée', 'required' => false))              
            ->add('maxInstallments', IntegerType::class , array('label' => 'Nombre maximal de dates fixées')) 
            ->add('save'          , SubmitType::class)                              
            ->getForm()
            ;       

        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $productDTO = (object) $dataForm;
                $productDTO->internalName = preg_replace('/\s\s+/', '', $productDTO->name); 
                $productDTO->channels[]   = 'webServices';
                $productID = $this->productManager->editProduct($productDTO);

                return $this->render('CairnUserCyclosBundle:Config/Product:index.html.twig');

            }
        }

        return $this->render('CairnUserCyclosBundle:Config/Product:edit.html.twig', array('form' => $form->createView()));
    }

    /*
     *configure all the transfer types by allowing WebServices channel
     *
     */
//    public function configureProductsAction()
//    {
//        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
//        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 
//        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');
//
//        try{
//            $this->productManager->configureProducts();
//        }catch(\Exception $e){
//            echo 'Erreur :' .  $e->getMessage();                                          
//            print_r($e->error);
//        }
//        return new Response('Products configurés pour les WebServices');
//
//    }



    public function removeProductAction(Request $request, $name, $fromNature, $toNature,$currency)
    {
        Cyclos\Configuration::setRootUrl("http://localhost:8080/cyclos/global");
        Cyclos\Configuration::setAuthentication("mazouthm", "admin"); 

        $this->get('cairn_user_cyclos_network_info')->switchToNetwork('Test5');

        $productID = $this->get('cairn_user_cyclos_product_info')->getProductID($name, $fromNature, $toNature, $currency);
        $this->productManager->removeProduct($productID);

        return $this->render('CairnUserCyclosBundle:Config/Product:index.html.twig');


    }

}
