<?php 

// src/Cairn/UserBundle/Controller/HelloassoController.php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Cairn\UserCyclosBundle\Entity\LoginManager;
use Cairn\UserCyclosBundle\Entity\BankingManager;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Deposit;
use Cairn\UserBundle\Entity\HelloassoConversion;
use Cairn\UserBundle\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

/**
 * This class contains actions related to other applications as webhooks 
 */
class HelloassoController extends Controller
{
    /**
     * Deals with all account actions to operate on Cyclos-side
     *@var BankingManager $bankingManager
     */
    private $bankingManager;

    public function __construct()
    {
        $this->bankingManager = new BankingManager();
    }

    private function getApiPayment($id)
    {
        $campaignID = $this->getParameter('helloasso_campaign_id');                     

        //look for helloassopayment with same id in helloasso data
        $campaign_payments = $this->get('cairn_user.helloasso')->get('campaigns/'.$campaignID.'/payments',array('page'=>'2'));

        $nbPages = $campaign_payments->pagination->max_page;

        $api_payment = NULL;
        for($i = $nbPages; $i >= 1; $i--){
            $campaign_payments = $this->get('cairn_user.helloasso')->get('campaigns/'.$campaignID.'/payments',array('page'=>$i));
            foreach($campaign_payments->resources as $payment){
                if($payment->id == $id ){
                    $api_payment = $payment;
                    break 2;
                }
            }
        }
        return $api_payment;
    }

    private function hydrateHelloassoPayment($api_payment)
    {
        $em = $this->getDoctrine()->getManager();
        $helloassoRepo = $em->getRepository('CairnUserBundle:HelloassoConversion');

        //look for helloassopayment with same id in db
        $existingPayment = $helloassoRepo->findOneByPaymentID($api_payment->id);
        if($existingPayment){
            return NULL;
        }

        //create HelloAsso payment
        $helloasso = new HelloassoConversion();

        $helloasso->setPaymentID($api_payment->id);
        $helloasso->setDate(new \Datetime($api_payment->date));
        $helloasso->setAmount($api_payment->amount);
        $helloasso->setEmail($api_payment->payer_email);
        $helloasso->setCreditorName($api_payment->payer_last_name.' '.$api_payment->payer_first_name);
        return $helloasso;
    }


    /**
     * Helloasso webhook used to notify our app at each payment received by the association in order to credit adherent (electronic change)
     *
     * Once a Helloassopayment is received, our app is notified through this webhook with relevant data (amount / payer info)
     * Then, we credit the identified adherent account with the same amount to achieve the electronic change.
     * If the received payment amount is greater than the currently available money safe balance, a deposit is created with the difference
     * Plus, the association is warned by email that the money safe is empty, and the adherent is notified about the asynchronous deposit
     * by email
     *
     * Example : 
     *  1) the adherent executes a helloasso payment of 1000 units but the money safe balance is 400 units.
     *  2) the adherent is credited of 400 units (cyclos-side)
     *  3) a deposit of 600 units is persisted in db to remind that there is still 600 units to credit
     *  4) email sent to association and to adherent
     */
    public function helloassoNotificationAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $helloassoRepo = $em->getRepository('CairnUserBundle:HelloassoConversion');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $messageNotificator = $this->get('cairn_user.message_notificator');
        $accountManager =  $this->get('cairn_user.account_manager');

        if($request->isMethod('POST')){
             $data = htmlspecialchars($request->getContent(),ENT_NOQUOTES) ;

             preg_match('#^id=(\d+)#',$data,$match_id);

             $api_payment = $this->getApiPayment($match_id[1]);

             if(! $api_payment){
                 $response = new Response('No payment found');
                 $response->headers->set('Content-Type', 'application/json');
                 $response->setStatusCode(Response::HTTP_NOT_FOUND);
                 return $response;
             }

             //get a new helloassoConversion entity if it is does not exist yet
             $newHelloassoPayment = $this->hydrateHelloassoPayment($api_payment);

             if(! $newHelloassoPayment){
                $response = new Response('Helloasso payment already handled');
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_FORBIDDEN);
                return $response;
             }


             //do cyclos account credit
             $creditorUser = $userRepo->findOneByEmail($newHelloassoPayment->getEmail());
             if(! $creditorUser){
                 $subject = 'Crédit de compte [e]-Cairn et virement Helloasso';
                 $from = $messageNotificator->getNoReplyEmail();
                 $to = $newHelloassoPayment->getEmail();
                 $body = $this->renderView('CairnUserBundle:Emails:helloasso.html.twig',array('helloasso'=>$newHelloassoPayment,'reason'=>'unfindable'));

                 $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                 $response = new Response('creditor user not found');
                 $response->headers->set('Content-Type', 'application/json');
                 $response->setStatusCode(Response::HTTP_NOT_FOUND);

                 return $response;
             }

             //can be null
             $reason = 'Change numérique par virement Helloasso';
             $operation = $accountManager->creditUserAccount($creditorUser, $newHelloassoPayment->getAmount(),Operation::TYPE_CONVERSION_HELLOASSO,$reason);

             $em->persist($newHelloassoPayment);

             if($operation){
                $em->persist($operation);
             }

             $em->flush();

             $response = new Response('helloasso payment handled');
             $response->headers->set('Content-Type', 'application/json');
             $response->setStatusCode(Response::HTTP_OK);

             return $response;

        }
    }


    

    /**
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function helloassoSyncAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $helloassoRepo = $em->getRepository('CairnUserBundle:HelloassoConversion');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $accountManager =  $this->get('cairn_user.account_manager');
        $messageNotificator = $this->get('cairn_user.message_notificator');

        $form = $this->createFormBuilder()
            ->add('payment_id', TextType::class, array('label' => 'ID du virement helloasso'))
            ->add('email', TextType::class, array('label' => 'Email du compte créditeur'))
            ->add('save',      SubmitType::class, array('label' => 'Synchroniser'))
            ->getForm();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $dataForm = $form->getData();

            $api_payment = $this->getApiPayment($dataForm['payment_id']);
            
             if(! $api_payment){
                 $session->getFlashBag()->add('error','Aucun paiement trouvé avec l\'identifiant '.$dataForm['payment_id']);
                 return $this->redirectToRoute('cairn_user_electronic_mlc_dashboard');
             }

            $api_payment->payer_email = $dataForm['email'];

             //get a new helloassoConversion entity if it is does not exist yet
             $newHelloassoPayment = $this->hydrateHelloassoPayment($api_payment);

             if(! $newHelloassoPayment){
                 $session->getFlashBag()->add('info','Le virement Helloasso d\'identifiant '.$dataForm['payment_id'].' a déjà été traité');
                 return $this->redirectToRoute('cairn_user_electronic_mlc_dashboard');
             }


             //do cyclos account credit
             $creditorUser = $userRepo->findOneByEmail($newHelloassoPayment->getEmail());
             if(! $creditorUser){

                 $session->getFlashBag()->add('error','Le virement Helloasso d\'identifiant '.$dataForm['payment_id'].' ne correspond à aucun compte [e]-Cairn : '.$newHelloassoPayment->getEmail());
                 return new RedirectResponse($request->getRequestUri());
             }

             $reason = 'Change numérique par virement Helloasso';
             $operation = $accountManager->creditUserAccount($creditorUser, $newHelloassoPayment->getAmount(),Operation::TYPE_CONVERSION_HELLOASSO,$reason);

             $em->persist($newHelloassoPayment);
             
             if(! $operation ){
                 $session->getFlashBag()->add('error','Le crédit de compte n\'a pas été exécuté. Le coffre [e]-Cairns est probablement vide !');
             }else{
                 $em->persist($operation);
                 $session->getFlashBag()->add('success','Le compte associé à '.$newHelloassoPayment->getEmail().' a été crédité avec succès de '.$operation->getAmount());
             }

            $em->flush();
            return $this->redirectToRoute('cairn_user_electronic_mlc_dashboard');

        }

        return $this->render('CairnUserBundle:Admin:helloasso_credit.html.twig',array('form' => $form->createView() ));

    }
}
