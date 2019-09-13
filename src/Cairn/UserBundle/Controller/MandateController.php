<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Cyclos
use Cyclos;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Mandate;
use Cairn\UserBundle\Entity\Operation;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Cairn\UserBundle\Form\MandateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * This class contains all actions related to user experience
 *
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class MandateController extends Controller
{

    public function mandatesDashboardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mandateRepo = $em->getRepository("CairnUserBundle:Mandate");
        $userRepo = $em->getRepository("CairnUserBundle:User");
        $session = $request->getSession();


        //by default, return overdued mandates
        $mandates =$mandateRepo->findByStatus(Mandate::OVERDUE);

        if(! $mandates){
            $mandates =$mandateRepo->findByStatus(Mandate::UP_TO_DATE);
        }

        $form = $this->createFormBuilder()
            ->add('cairn_user', TextType::class, array('label' => 'Compte','attr'=>array('placeholder'=>'email ou nom'),'required'=>false))
            ->add('status',    ChoiceType::class, array(
                 'label' => 'Statut des mandats',
                 'required'=>false,
                 'choices' => Mandate::ARRAY_ALL_STATUS,
                 'choice_label'=> function($choice){
                     return Mandate::getStatusName($choice);
                 },
                 'multiple'=>true,
                 'expanded'=>false
                 ))

            //->add('beginAt', DateType::class, array('label'=> 'Début','widget' => 'single_text','format' => 'yyyy-MM-dd',"attr"=>array('class'=>'datepicker_cairn')))
            //->add('endAt', DateType::class, array('label'=> 'Fin','widget' => 'single_text','format' => 'yyyy-MM-dd',"attr"=>array('class'=>'datepicker_cairn')))
            ->add('forward', SubmitType::class, array('label' => 'Accéder au(x) mandat(s)'))
            ->getForm();

        $form->handleRequest($request);    

        if($form->isSubmitted() && $form->isValid()){
            
            $dataForm = $form->getData();            
            $status = $dataForm['status'];
            $formAutocompleteName = $dataForm['cairn_user'];

            $mb = $mandateRepo->createQueryBuilder('m');

            if($formAutocompleteName){
                preg_match('#\((.*)\)$#',$formAutocompleteName,$matches_email);

                if (! $matches_email){
                    $session->getFlashBag()->add('error','Votre recherche ne contient aucun email');
                    return new RedirectResponse($request->getRequestUri());
                }

                $contractor = $userRepo->findOneByEmail($matches_email[1]);
                $mandateRepo->whereContractor($mb, $contractor);

            }

            if($status){
                $mandateRepo->whereStatus($mb, $status);
            }
            
            $mb->orderBy('m.beginAt','ASC');
            $mandates = $mb->getQuery()->getResult();

            return $this->render('CairnUserBundle:Mandate:dashboard.html.twig',
                array('form'=>$form->createView(),'mandates'=>$mandates)
            );


        }

        return $this->render('CairnUserBundle:Mandate:dashboard.html.twig',
            array('form'=>$form->createView(),'mandates'=>$mandates)
            );


    }

    public function declareMandateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $mandate = new Mandate();
        $form = $this->createForm(MandateType::class, $mandate);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $session->getFlashBag()->add('success','Le nouveau mandat a bien été déclaré');    
            $em->persist($mandate);
            $em->flush();

            return $this->redirectToRoute('cairn_user_mandates_dashboard');
        }

        return $this->render('CairnUserBundle:Mandate:add.html.twig',
            array('form'=>$form->createView())
            );

    }

    public function honourMandateAction(Request $request, Mandate $mandate)
    {
        $session = $request->getSession();

        $today = new \Datetime();
        $dayToday = $today->format('d');

        if(! ( ($dayToday > 28 || $dayToday < 5) || ($mandate->getStatus() == Mandate::OVERDUE ))){
            $session->getFlashBag()->add('info','Les honorations de mandat ont lieu du 28 au 05 du mois suivant, sauf en cas de retard');
            return $this->redirectToRoute('cairn_user_mandates_dashboard');
        } 

        $em = $this->getDoctrine()->getManager();
        $accountManager = $this->get('cairn_user.account_manager');

        $form = $this->createFormBuilder()
            ->add('execute', SubmitType::class, array('label' => 'Honorer'))
            ->getForm();


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $operation = $accountManager->creditUserAccount($mandate->getContractor(), $mandate->getAmount(), Operation::TYPE_MANDATE, 'Règlement de mandat' );

            if(! $operation){
                $session->getFlashBag()->add('error','Coffre [e]-Cairns insuffisant');
                return $this->redirectToRoute('cairn_user_mandates_dashboard');
            }
            $mandate->addOperation($operation);

            if($accountManager->getConsistentOperationsCount($mandate, $mandate->getEndAt()) == $mandate->getOperations()->count()){
                $mandate->setStatus(Mandate::COMPLETE);
                $session->getFlashBag()->add('success','Le mandat est complet');
            }elseif($accountManager->isUpToDateMandate($mandate)){
                $mandate->setStatus(Mandate::UP_TO_DATE);
                $session->getFlashBag()->add('success','Mandat à jour');
            }else{ // in case if there are several operations overdued
                $session->getFlashBag()->add('success','Mandat honoré');
                $session->getFlashBag()->add('info','Mandat toujours pas à jour');
            }

            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('cairn_user_mandates_dashboard');

        }

        return $this->render('CairnUserBundle:Mandate:honour.html.twig',
            array('form'=>$form->createView(),'mandate'=>$mandate)
            );
    }


    public function editMandateAction(Request $request, Mandate $mandate)
    {

    }

    public function cancelMandateAction(Request $request, Mandate $mandate)
    {
        $session = $request->getSession();

        if($mandate->getStatus() == Mandate::COMPLETE ){
            $session->getFlashBag()->add('info','Le mandat est complet et ne peut donc être annulé');
            return $this->redirectToRoute('cairn_user_mandates_dashboard');
        }elseif($mandate->getStatus() == Mandate::CANCELED ){
            $session->getFlashBag()->add('info','Le mandat a déjà été révoqué');
            return $this->redirectToRoute('cairn_user_mandates_dashboard');
        }elseif($mandate->getStatus() == Mandate::OVERDUE ){
            $session->getFlashBag()->add('info','Le mandat a un retard de paiement, et ne peut donc être révoqué');
            return $this->redirectToRoute('cairn_user_mandates_dashboard');
        } 


        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->add('execute', SubmitType::class, array('label' => 'Révoquer'))
            ->getForm();


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mandate->setStatus(Mandate::CANCELED);
            $session->getFlashBag()->add('success','Mandat révoqué avec succès');

            $em->flush();
            return $this->redirectToRoute('cairn_user_mandates_dashboard');

        }

        return $this->render('CairnUserBundle:Mandate:cancel.html.twig',
            array('form'=>$form->createView(),'mandate'=>$mandate)
            );

    }
}
