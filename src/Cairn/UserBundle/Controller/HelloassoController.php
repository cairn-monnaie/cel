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
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
class HelloassoController extends BaseController
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

    private function getApiPayment($accessToken,$id)
    {
        $helloassoService = $this->get('cairn_user.helloasso');

        $haConsts = $this->getParameter('helloasso_consts'); 
        $basePaymentsUrl = 'v5/organizations/'.$haConsts['organization']['slug'].'/forms/'.$haConsts['form']['type'].'/'.$haConsts['form']['slug'].'/payments';

        //look for helloassopayment with same id in helloasso data
        $campaign_payments = $helloassoService->get($accessToken,$basePaymentsUrl,array('pageIndex'=>'2','states'=>'Authorized'));

        $nbPages = $campaign_payments['pagination']['totalPages'];

        $api_payment = NULL;
        for($i = 1; $i <= $nbPages; $i++){
            $campaign_payments = $helloassoService->get($accessToken,$basePaymentsUrl,array('pageIndex'=>$i,'states'=>'Authorized'));
            foreach($campaign_payments['data'] as $payment){
                if($payment['id'] == $id ){
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
        $existingPayment = $helloassoRepo->findOneByPaymentID($api_payment['id']);
        if($existingPayment){
            return NULL;
        }

        //BE CAREFUL, THE AMOUNT RETURNED BY THE API IS MULTIPLIED BY 100
        $amount = $api_payment['amount'] / 100;

        //create HelloAsso payment
        $helloasso = new HelloassoConversion();

        $helloasso->setPaymentID($api_payment['id']);
        $helloasso->setDate(new \Datetime($api_payment['date']));
        $helloasso->setAmount($amount);
        $helloasso->setEmail($api_payment['payer']['email']);
        $helloasso->setCreditorName($api_payment['payer']['lastName'].' '.$api_payment['payer']['firstName']);
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
        $apiService = $this->get('cairn_user.api');

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $helloassoRepo = $em->getRepository('CairnUserBundle:HelloassoConversion');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $messageNotificator = $this->get('cairn_user.message_notificator');
        $accountManager =  $this->get('cairn_user.account_manager');
        $helloassoService = $this->get('cairn_user.helloasso');

        //get Helloasso access Token
        $accessToken = $helloassoService->getToken();

        if($request->isMethod('POST')){
            $response = json_decode(htmlspecialchars($request->getContent(),ENT_NOQUOTES), true);
            if(isset($response['data'])){
                $data = $response['data'];
            }else{
                return $this->getErrorsResponse(['key'=>'field_not_found','args'=>['data']],[],Response::HTTP_NOT_FOUND);
            }

            //check campaign slug first
            if(! ((strtoupper($data['order']['formType']) == 'DONATION') && ($data['order']['formSlug'] == '3')) ){
                return $this->getRenderResponse('',[],[],Response::HTTP_OK);
            }

            //the campaign is about crediting account
            $id = $data['id'];

            $api_payment = $this->getApiPayment($accessToken,$id);
            $helloassoService->disconnect($accessToken);

            if(! $api_payment){
                return $this->getErrorsResponse(['key'=>'data_not_found'],[],Response::HTTP_NOT_FOUND);
            }

            //get a new helloassoConversion entity if it is does not exist yet
            $newHelloassoPayment = $this->hydrateHelloassoPayment($api_payment);

            if(! $newHelloassoPayment){
                return $this->getErrorsResponse(['key'=>'operation_already_processed'],[],Response::HTTP_BAD_REQUEST);
            }


            //do cyclos account credit
            $creditorUser = $userRepo->findOneByEmail($newHelloassoPayment->getEmail());
            if(! $creditorUser){
                $subject = 'Crédit de compte [e]-Cairn et virement Helloasso';
                $from = $messageNotificator->getNoReplyEmail();
                $to = $newHelloassoPayment->getEmail();
                $body = $this->renderView('CairnUserBundle:Emails:helloasso.html.twig',array('helloasso'=>$newHelloassoPayment,'reason'=>'unfindable'));

                $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                return $this->getErrorsResponse(['key'=>'data_not_found'],[],Response::HTTP_NOT_FOUND);
            }

            //can be null
            $reason = 'Change numérique par virement Helloasso';
            $operation = $accountManager->creditUserAccount($creditorUser, $newHelloassoPayment->getAmount(),Operation::TYPE_CONVERSION_HELLOASSO,$reason);

            $em->persist($newHelloassoPayment);
            $em->persist($operation);

            $em->flush();

            return $this->getRenderResponse(
                '',
                [],
                $operation,
                Response::HTTP_CREATED,
                ['key'=>'registered_operation']
            );

        }
    }




    /**
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function helloassoSyncAction(Request $request)
    {
        $apiService = $this->get('cairn_user.api');

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $helloassoRepo = $em->getRepository('CairnUserBundle:HelloassoConversion');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $accountManager =  $this->get('cairn_user.account_manager');
        $messageNotificator = $this->get('cairn_user.message_notificator');
        $helloassoService = $this->get('cairn_user.helloasso');

        //get Helloasso access Token
        $accessToken = $session->get('helloasso_token');
        if(! $accessToken){
            $accessToken = $helloassoService->getToken();
            $session->set('helloasso_token',$accessToken);
        }

        $formSearch = $this->createFormBuilder()
            ->add('from', DateType::class, array('label' => 'Date de début','widget' => 'single_text','attr'=>array('class'=>'datepicker_cairn')))
            ->add('to', DateType::class, array('label' => 'Date de fin','widget' => 'single_text','attr'=>array('class'=>'datepicker_cairn')))
            ->add('email', TextType::class, array('label' => 'Email helloasso'))
            ->add('search',      SubmitType::class, array('label' => 'Rechercher'))
            ->getForm();

        $formSync = $this->createFormBuilder()
            ->add('payment_id', TextType::class, array('label' => 'ID du virement helloasso'))
            ->add('email', TextType::class, array('label' => 'Email du compte créditeur'))
            ->add('sync',      SubmitType::class, array('label' => 'Synchroniser'))
            ->getForm();

        $formSearch->handleRequest($request);    
        $formSync->handleRequest($request);    

        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $dataForm = $formSearch->getData();
 
            $from = $dataForm['from'] ;
            $to = $dataForm['to'] ;

            $haConsts = $this->getParameter('helloasso_consts'); 
            $basePaymentsUrl = 'v5/organizations/'.$haConsts['organization']['slug'].'/forms/'.$haConsts['form']['type'].'/'.$haConsts['form']['slug'].'/payments';
            $queryUrl = array('from'=>$from->format('c'),'to'=>$to->format('c'),'userSearchKey'=>$dataForm['email'],'states'=>'Authorized' );

            //look for helloassopayment with same id in helloasso data
            $payments_search = $helloassoService->get($accessToken,$basePaymentsUrl,$queryUrl);
            $nbPages = $payments_search['pagination']['totalPages'];

            $payments_filter = array();

            for($i = 1; $i <= $nbPages; $i++){
                if($nbPages > 1){
                    $queryUrl['pageIndex'] = $i;
                    $payments_search = $helloassoService->get($accessToken,$basePaymentsUrl,$queryUrl);
                }
                foreach($payments_search['data'] as $payment){
                    $payments_filter[] = $payment;
                }
            }

            return $this->render('CairnUserBundle:Admin:helloasso_credit.html.twig',
                array(
                    'formSearch' => $formSearch->createView(), 
                    'formSync' => $formSync->createView(),
                    'payments'=> $payments_filter
                ));


        }elseif ($formSync->isSubmitted() && $formSync->isValid()) {
            $dataForm = $formSync->getData();

            $api_payment = $this->getApiPayment($accessToken,$dataForm['payment_id']);

            $session->remove('helloasso_token');
            $helloassoService->disconnect($accessToken);

            if(! $api_payment){
                $session->getFlashBag()->add('error','Aucun paiement trouvé avec l\'identifiant '.$dataForm['payment_id']);
                return $this->redirectToRoute('cairn_user_electronic_mlc_dashboard');
            }

            $api_payment['payer']['email'] = $dataForm['email'];

            //get a new helloassoConversion entity if it does not exist yet
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

            $operation->setSubmissionDate($newHelloassoPayment->getDate());
            $em->persist($operation);
            $session->getFlashBag()->add('success','Le compte associé à '.$newHelloassoPayment->getEmail().' a été crédité avec succès de '.$operation->getAmount());

            $em->flush();
            return $this->redirectToRoute('cairn_user_banking_transfer_view', array('paymentID' => $operation->getPaymentID() ));            

        }

        return $this->render('CairnUserBundle:Admin:helloasso_credit.html.twig',array('formSearch' => $formSearch->createView(), 'formSync' => $formSync->createView()));

    }
}
