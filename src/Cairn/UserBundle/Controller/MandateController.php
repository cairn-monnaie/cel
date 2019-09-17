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

    public function indexAction(Request $request)
    {
        return $this->render('CairnUserBundle:Mandate:index.html.twig');
    }

    /**
     * View specific mandate together with its executed operations
     *
     */
    public function viewMandateAction(Request $request, Mandate $mandate)
    {

        return $this->render('CairnUserBundle:Mandate:view.html.twig',array('mandate'=>$mandate));

    }

    public function mandatesDashboardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mandateRepo = $em->getRepository("CairnUserBundle:Mandate");
        $userRepo = $em->getRepository("CairnUserBundle:User");
        $session = $request->getSession();


        //by default, return overdued mandates
        $mandates = $mandateRepo->findByStatus(Mandate::OVERDUE);

        if(! $mandates){
            $mandates = $mandateRepo->findByStatus(Mandate::UP_TO_DATE);
        }

        //get amount of mandates honored this month, based on their operation's submission dates

        $today = new \Datetime();
        $month = $today->format('m');
        $todayDay = $today->format('d');

        if(! $todayDay >= 27){
            $month -= $todayMonth;
        }

        $begin = new \Datetime(date('Y-'.$month.'-01'));
        $end = new \Datetime(date('Y-'.$month.'-t'));

        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $ob = $operationRepo->createQueryBuilder('o');
        $operationRepo->whereType($ob, Operation::TYPE_MANDATE)->whereSubmissionDateBetween($ob, $begin,$end);

        $totalAmount = $operationRepo->countTotalAmount($ob);


        $formMandate = $this->createFormBuilder()
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
            ->add('forward', SubmitType::class, array('label' => 'Rechercher le(s) mandat(s)'))
            ->getForm();

        $formOperations = $this->createFormBuilder()
            ->add('cairn_user', TextType::class, array('label' => 'Compte','attr'=>array('placeholder'=>'email ou nom'),'required'=>false))
            ->add('date',    DateType::class, array(
                 'label' => 'Mois à honorer',
                 'required'=>false,
                 'widget'=> 'choice',
                 'days'=>array(1),
                 'years'=> range(date('Y'), date('Y') + 4)
                 ))
            ->add('forward', SubmitType::class, array('label' => 'Rechercher le(s) opération(s)'))
            ->getForm();

        $formMandate->handleRequest($request);    
        $formOperations->handleRequest($request);    

        if($formMandate->isSubmitted() && $formMandate->isValid()){
            
            $dataForm = $formMandate->getData();            
            $status = $dataForm['status'];
            $formMandateAutocompleteName = $dataForm['cairn_user'];

            $mb = $mandateRepo->createQueryBuilder('m');

            if($formMandateAutocompleteName){
                preg_match('#\((.*)\)$#',$formMandateAutocompleteName,$matches_email);

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
                array('formMandate'=>$formMandate->createView(),'formOperations'=>$formOperations->createView(),
                'mandates'=>$mandates,
                'totalAmount'=>$totalAmount
            )
        );


        }elseif($formOperations->isSubmitted() && $formOperations->isValid()){
            
            $dataForm = $formOperations->getData();            
            $date = $dataForm['date'];
            $formOperationsAutocompleteName = $dataForm['cairn_user'];

            $ob = $operationRepo->createQueryBuilder('o');
            $operationRepo->whereType($ob,Operation::TYPE_MANDATE);

            if($formOperationsAutocompleteName){
                preg_match('#\((.*)\)$#',$formOperationsAutocompleteName,$matches_email);

                if (! $matches_email){
                    $session->getFlashBag()->add('error','Votre recherche ne contient aucun email');
                    return new RedirectResponse($request->getRequestUri());
                }

                $contractor = $userRepo->findOneByEmail($matches_email[1]);
                $operationRepo->whereCreditor($ob, $contractor);

            }

            if($date){
                $begin = new \Datetime($date->modify('first day of this month')->format('Y-m-d'));
                $end = new \Datetime($date->modify('last day of this month')->format('Y-m-d'));

                $operationRepo->whereSubmissionDateBetween($ob, $begin, $end);
            }
            
            $ob->orderBy('o.submissionDate','ASC');
            $operations = $ob->getQuery()->getResult();

            $totalAmount = 0;
            foreach($operations as $operation){
                $totalAmount += $operation->getAmount();
            }

            return $this->render('CairnUserBundle:Mandate:dashboard.html.twig',
                array('formMandate'=>$formMandate->createView(),'formOperations'=>$formOperations->createView(),
                'totalAmount'=>$totalAmount,
                'operations'=> $operations)
            );

        }


        return $this->render('CairnUserBundle:Mandate:dashboard.html.twig',
            array('formMandate'=>$formMandate->createView(),'formOperations'=>$formOperations->createView(),'mandates'=>$mandates,'totalAmount'=>$totalAmount)
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

        if(! ($mandate->getStatus() == Mandate::OVERDUE) ){
            $session->getFlashBag()->add('info','Ce mandat est à jour');
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

            //deal with submission date (date which operation is supposed to be executed)
            //submission date allows to know for which month the operation is done
            //TODO : tester 28-02-2020
            $count =  $mandate->getOperations()->count();
            if($count == 0){
                $month = $mandate->getBeginAt()->format('m');
                $operation->setSubmissionDate(new \Datetime( date('Y-'.$month.'-28')  ));
            }else{
                $lastExecutionDate = $mandate->getOperations()[$count -1]->getSubmissionDate();
                $nextDate = date_modify($lastExecutionDate, '+1 month');
                $operation->setSubmissionDate($nextDate);
            }

            
            $mandate->addOperation($operation);
            $operation->setMandate($mandate);

            if($accountManager->getConsistentOperationsCount($mandate, $mandate->getEndAt()) <= $mandate->getOperations()->count()){
                $mandate->setStatus(Mandate::COMPLETE);
                $session->getFlashBag()->add('success','Le mandat est complet');
            }else{
                if($accountManager->isUpToDateMandate($mandate)){
                    $mandate->setStatus(Mandate::UP_TO_DATE);
                    $session->getFlashBag()->add('success','Mandat à jour');
                }else{ // in case if there are several operations overdued
                    $session->getFlashBag()->add('success','Mandat honoré');
                    $session->getFlashBag()->add('info','Mandat toujours pas à jour');
                }
            }

            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('cairn_user_mandates_dashboard');

        }

        return $this->render('CairnUserBundle:Mandate:honour.html.twig',
            array('form'=>$form->createView(),'mandate'=>$mandate)
            );
    }


//    public function editMandateAction(Request $request, Mandate $mandate)
//    {
//
//    }
//

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
