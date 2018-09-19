<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Entities
use Cairn\UserBundle\Entity\Deposit;
use Cairn\UserBundle\Entity\Withdrawal;
use Cairn\UserBundle\Entity\Envelope;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms

use Cairn\UserBundle\Form\DepositType;
use Cairn\UserBundle\Form\WithdrawalType;
use Cairn\UserBundle\Form\EnvelopeType;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BankConnectionController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $depositRepo = $em->getRepository('CairnUserBundle:Deposit');
        $currentUser = $this->getUser();
        $qb = $depositRepo->createQueryBuilder('d')->orderBy('d.date','DESC')
            ->setMaxResults(4);
        if($currentUser->hasRole('ROLE_ExOFF')){
            $qb->andWhere('d.exchangeOffice = :exoff')                           
                ->setParameter('exoff',$currentUser);
        }
        $lastDeposits = $qb->getQuery()->getResult();
        return $this->render('CairnUserBundle:BankConnection:index.html.twig',array('deposits'=>$lastDeposits));
    }

    public function newEnvelopeAction(Request $request)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $envelope = new Envelope();
        $form = $this->createForm(EnvelopeType::class, $envelope);
        //        $currentuser = $em->getrepository('cairnusercyclosbundle:user')->findoneby(array('id'=>$this->getuser()->getid()));
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $envelope->setWithdrawal(new Withdrawal());
                $em->persist($envelope);
                $em->flush();
            }
            else{
                $session->getFlashBag()->add('error','Le formulaire contient des données invalides');
                return $this->redirectToRoute('cairn_user_bankconnection_envelope_new');
            }
        }
        return $this->render('CairnUserBundle:BankConnection:new_envelope.html.twig', array('form' => $form->createView()));
    }

    public function newDepositAction(Request $request)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $deposit = new Deposit();
        $form = $this->createForm(DepositType::class, $deposit);
        //        $currentuser = $em->getrepository('cairnusercyclosbundle:user')->findoneby(array('id'=>$this->getuser()->getid()));
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){

                $session->set('deposit',$deposit);
                return $this->redirectToRoute('cairn_user_bankconnection_deposit_confirm');
            }
            else{
                $session->getFlashBag()->add('error','Le formulaire contient des données invalides');
                return $this->redirectToRoute('cairn_user_bankconnection_deposit_new');
            }
        }
        return $this->render('CairnUserBundle:BankConnection:new_deposit.html.twig', array('form' => $form->createView()));

    }

    public function newWithdrawalAction(Request $request)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $withdrawal = new Withdrawal();

        $statusRepo = $em->getRepository('CairnUserBundle:BanknoteStatus');
        $form = $this->createForm(WithdrawalType::class, $withdrawal);
        //        $currentuser = $em->getrepository('cairnusercyclosbundle:user')->findoneby(array('id'=>$this->getuser()->getid()));
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $status = $statusRepo->findOneBy(array('status'=>'stored','exchangeOffice'=>$withdrawal->getExchangeOffice()));
                foreach($withdrawal->getEnvelopes() as $envelope){
                
                    foreach($envelope->getBanknotes() as $banknote){
                        $banknote->setStatus($status);
                    }
                }
                $em->persist($withdrawal);
                $em->flush();
                $session->getFlashBag()->add('info','L\'approvisionnement du bureau de change a bien été enregistré.');
                return $this->redirectToRoute('cairn_user_bankconnection_withdrawal_view',array('id'=>$withdrawal->getID()));
            }
            else{
                $session->getFlashBag()->add('error','Le formulaire contient des données invalides');
                return $this->redirectToRoute('cairn_user_bankconnection_withdrawal_new');
            }
        }
        return $this->render('CairnUserBundle:BankConnection:new_withdrawal.html.twig', array('form' => $form->createView()));

    }


    public function confirmDepositAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $deposit = $session->get('deposit');

        $currentUser = $em->getRepository('CairnUserBundle:User')->findOneBy(array('id'=>$this->getUser()->getID()));

        $form = $this->createFormBuilder()                                     
            ->add('cancel',    SubmitType::class, array('label' => 'Annulation'))
            ->add('save',      SubmitType::class, array('label' => 'Confirmation'))
            ->getForm();                                                       

        if($request->isMethod('POST')){ //form filled and submitted            

            $form->handleRequest($request);                                    
            if($form->isValid()){                                              
                if($form->get('save')->isClicked()){
                    if(!$deposit->getExchangeOffice()){//means that a ROLE_EXOFF submitted the form
                        $deposit->setExchangeOffice($currentUser);
                        $deposit->getExchangeOffice()->setCyclosID($currentUser->getCyclosID());
                    }

                    $em->persist($deposit);
                    $em->flush();
                    $session->getFlashBag()->add('info','Le dépôt a été validé avec succès.');
                    return $this->redirectToRoute('cairn_user_bankconnection_deposit_view',array('id'=>$deposit->getID()));
                }
                else{
                    $session->getFlashBag()->add('info','Vous avez annulé le dépôt.');
                    return $this->redirectToRoute('cairn_user_bankconnection_deposit_index');

                }
            }
        }
        return $this->render('CairnUserBundle:BankConnection:confirm_deposit.html.twig',array('form'=>$form->createView(),'deposit'=>$deposit));

    }

    public function viewDepositAction(Request $request, $id)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $depositRepo = $em->getRepository('CairnUserBundle:Deposit');

        $deposit = $depositRepo->findOneBy(array('id'=>$id));
        if($deposit){
            return $this->render('CairnUserBundle:BankConnection:view_deposit.html.twig',array('deposit'=>$deposit));
        }
        else{
            $session->getFlashBag()->add('error','Votre recherche ne correspond à aucun dépôt. Veuillez réessayer');
            return $this->redirectToRoute('cairn_user_bank_deposit_index');
        }
    }

    public function editDeposit(Request $request, $id)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $depositRepo = $em->getRepository('CairnUserBundle:Deposit');
        $form = $this->createForm(DepositType::class, $deposit);

        if($form->isSubmitted() && $form->isValid()){
            $form->handleRequest($request);


        }

        return $this->render('CairnUserBundle:BankConnection:edit.html.twig',array('deposit'=>$deposit));
    }

}
