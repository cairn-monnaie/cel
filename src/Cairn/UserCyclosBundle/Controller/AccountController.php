<?php
// src/Cairn/UserCyclosBundle/Controller/AccountController.php

namespace Cairn\UserCyclosBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserCyclosBundle\Entity\BankingManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//manage forms
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class contains all Cyclos actions related to Account management 
 *
 */
class AccountController extends Controller
{   
    /**
     * Deals with all banking actions to operate on 
     *@var BankingManager $bankingManager
     */
    private $bankingManager;

    public function __construct()
    {
        $this->bankingManager = new BankingManager();
    }

    public function indexAction()
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        return $this->render('CairnUserCyclosBundle:Config/Account:index.html.twig');
    }


    /**
     *Edits a specific account
     *
     *This action is considered as a sensible operation in UserBundle. If the key input is incorrect, user's attribute 'cardKeyTries'
     * is incremented. 3 failures leads to disable the user.
     * 
     * The credit limit can be changed, go back to the default value(set to all accounts of the same type) and a description can be added
     * It can be done only for User accounts(not system accounts)
     */
    public function editAccountAction(Request $request, $id)
    {
        $this->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $session = $request->getSession();

        $accountVO = $this->get('cairn_user_cyclos_account_info')->getAccountByID($id);
        $accountTypeVO = $accountVO->type;

        if($accountTypeVO->nature == 'USER'){
            $productDTO = $this->get('cairn_user_cyclos_product_info')->getAccountProductDTO($accountTypeVO);
        }else{
            $session->getFlashBag()->add('error','Les comptes Systèmes sont les mêmes pour tous les administrateurs. Vous ne pouvez modifier ce compte individuellement.');
            return $this->redirectToRoute('cairn_user_banking_accounts_overview',array('id'=>$id));
        }
        $form = $this->createFormBuilder()
            ->add('creditLimit'   , IntegerType::class , array('label' => 'débit maximal du compte',
                'required' => false,
                'constraints'=> new Assert\Range(array('min'=>0,               
                                                       'minMessage'=>'Une valeur positive est attendue. "100" signifie un plancher de -100'))))         
                ->add('defaultLimit' , CheckboxType::class, array('label'=>'limite par défaut : ' .$productDTO->defaultCreditLimit ,
                    'required'=>false))
                    ->add('description'   , TextType::class , array('label' => 'Description'))                                
                    ->add('save'     , SubmitType::class)                              
                    ->getForm()
                    ;       


        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                $dataForm = $form->getData();
                $accountLimitDTO = new \stdClass();
                $accountLimitDTO->creditLimit = $dataForm['creditLimit'];
                $accountLimitDTO->account = $accountVO;
                $accountLimitDTO->currency = $accountVO->currency;
                $accountLimitDTO->creditLimitFromProduct = $dataForm['defaultLimit'];
                $accountLimitDTO->description = $dataForm['description'];

                if(($accountLimitDTO->creditLimit == NULL) && (!$accountLimitDTO->creditLimitFromProduct)){
                    $session->getFlashBag()->add('error','Si la limite par défaut n\'est pas sélectionnée, indiquez une valeur limite.');
                    return $this->redirectToRoute('cairn_user_cyclos_accountsconfig_account_edit',array('id'=>$id));

                }
                $this->bankingManager->editLimitAccount($accountLimitDTO);
                $session->getFlashBag()->add('info','Le plancher du compte de ' .$accountVO->owner->display . ' a été modifié avec succès.');
                $owner = $this->getDoctrine()->getManager()->getRepository('CairnUserBundle:User')->findOneBy(array('cyclosID'=>$accountVO->owner->id));
                return $this->redirectToRoute('cairn_user_profile_view',array('id'=>$owner->getID()));
            }
        }
        return $this->render('CairnUserCyclosBundle:Config/Account:edit_account.html.twig',array('form'=>$form->createView()));
    }


}
