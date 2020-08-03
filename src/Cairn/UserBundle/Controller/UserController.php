<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Cyclos
use Cyclos;

//manage Events 
use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;

//manage Entities
use Cairn\UserBundle\Entity\File as CairnFile;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Phone;
use Cairn\UserBundle\Entity\Deposit;
use Cairn\UserBundle\Entity\Mandate;

use Cairn\UserCyclosBundle\Entity\UserManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
//manage Forms
use Cairn\UserBundle\Form\SmsDataType;
use Cairn\UserBundle\Form\PhoneType;
use Cairn\UserBundle\Form\ConfirmationType;

use Cairn\UserBundle\Validator\UserPassword;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\PasswordType;                   


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * This class contains all actions related to user experience
 *
 * @Security("is_granted('ROLE_ADHERENT')")
 */
class UserController extends BaseController
{
    private $userManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    public function indexAction(Request $request, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        //last pros registered
        $userRepo = $em->getRepository('CairnUserBundle:User');
        //$user1 = $userRepo->findOneByUsername('admin_network');
        //$address1 = $user1->getAddress();

        //$address2 = $currentUser->getAddress();

        ////set latitude and longitude of new user
        //$extrema = $this->get('cairn_user.geolocalization')->getExtremaCoords($address2->getLatitude(),$address2->getLongitude(), 2);

        //$users = $userRepo->getUsersAround($address2->getLatitude(),$address2->getLongitude(), 2, $extrema);
        $checker = $this->get('security.authorization_checker');

        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        //last pros registered
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $qb = $userRepo->createQueryBuilder('u')
            ->orderBy('u.creationDate','DESC')
            ->andWhere('u.enabled = true')
            ->setMaxResults(5);
        $userRepo->whereRole($qb,'ROLE_PRO');
        $users =  $qb->getQuery()->getResult();

        //accounts of current user
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);

        if($currentUser->isAdherent()){
            $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id);
        }else{
            $accounts = array();
            $accounts[] = $this->get('cairn_user_cyclos_account_info')->getDebitAccount();
        }
        $accountNumbers = $this->get('cairn_user_cyclos_account_info')->getAccountNumbers($ownerVO->id);

        $executedTypes = Operation::getExecutedTypes(true,$currentUser->hasRole('ROLE_PRO'));
        //last operations
        $ob = $operationRepo->createQueryBuilder('o');
        $processedTransactions = $ob->where(
             $ob->expr()->orX(
                 $ob->expr()->andX(
                     $ob->expr()->in('o.fromAccountNumber', $accountNumbers),
                     $ob->expr()->in('o.type',$executedTypes)
                 ),
                 $ob->expr()->andX(
                     $ob->expr()->in('o.toAccountNumber', $accountNumbers),
                     $ob->expr()->in('o.type',$executedTypes)
                 )
             ))
            ->andWhere('o.paymentID is not NULL')
            ->orderBy('o.executionDate','DESC')
            ->setMaxResults(15)
            ->getQuery()->getResult();

        if($checker->isGranted('ROLE_ADHERENT')){
            if($_format == 'json'){
                $response = $this->get('serializer')->serialize($users[0], 'json');
                //$response = array('accounts'=>$accounts,'lastTransactions'=>$processedTransactions, 'lastUsers'=>$users);
                $response = new Response($response);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
                return $this->json($response);
            }
            return $this->render('CairnUserBundle:User:index.html.twig',array('accounts'=>$accounts,'lastTransactions'=>$processedTransactions,'lastPros'=>$users));
        }
    }

    /**
     *
     *@Security("is_granted('ROLE_PRO')")
     */
    public function editSmsDataAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        $apiService = $this->get('cairn_user.api');

        if(! $currentUser->isAdherent() ){
            throw new AccessDeniedException('Réserver aux comptes adhérents');
        }

        $smsData = $currentUser->getSmsData();

        $formSmsData = $this->createForm(SmsDataType::class, $smsData);

        if($request->isMethod('POST')){

            if( $apiService->isRemoteCall()){
                $formSmsData->submit(json_decode($request->getContent(), true));
            }else{
                $formSmsData->handleRequest($request);
            }

            if($formSmsData->isValid()){
                $em->flush();

                if( $apiService->isRemoteCall()){
                    $res = $apiService->serialize($currentUser->getSmsData());
                    $response = new Response($res);
                    $response->setStatusCode(Response::HTTP_CREATED);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;

                }


                $session->getFlashBag()->add('success','Vos systèmes de notification de paiement ont été mis à jour avec succès');

                return $this->redirectToRoute('cairn_user_profile_view',array('username' => $currentUser->getUsername()));
            }

        }

        return $this->render('CairnUserBundle:User:change_sms_data.html.twig',
            array('formSmsData'=>$formSmsData->createView())
            );

    }

     /**
     * Add user's sms data
     *
     * This action permits to change current user's sms data, such as phone number, or status enabled/disabled
     */
    public function addPhoneAction(Request $request, User $user, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        $apiService = $this->get('cairn_user.api');

        //****************** All cases where add phone is not allowed ****************//
        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('not_referent');
        }

        if(! $user->isAdherent() ){
            throw new AccessDeniedException('reserved_for_members');
        }

        if(! $user->isEnabled() ){
            throw new AccessDeniedException('user_account_disabled');
        }


        $encoder = $this->get('security.encoder_factory')->getEncoder($currentUser);

        if(!$user->getSmsData()){
            $smsData = new SmsData($user);
            $user->setSmsData($smsData);
        }else{
            $smsData = $user->getSmsData();
        }

        $phone = new Phone($smsData);
        $phone->setPhoneNumber('+33');

        $previousPhoneNumber = NULL;

        if($currentUser->getNbPhoneNumberRequests() >= 3 && !$session->get('activationCode')){
            $message = ['key'=>'too_many_tries_cancel'];
            return $this->getErrorsResponse($message, [] ,Response::HTTP_OK,$this->generateUrl('cairn_user_profile_view',['username'=>$user->getUsername()]));
        }

        //************************ end of cases where edit phone is disallowed *************************//

        $formPhone = $this->createForm(PhoneType::class, $phone);

        if($request->isMethod('POST')){
            if($_format == 'json'){
                $formPhone->submit(json_decode($request->getContent(), true));
            }else{
                $formPhone->handleRequest($request);
            }

            // POST request is an activation code to validate a new phone number
            if($formPhone->has('activationCode')){
                return $this->checkActivationCode($formPhone,$request, $user);
            }

            if($formPhone->isValid()){

                $dataForm = $formPhone->getData();

                // POST request is a new phone number for an existing entity smsData
                if($previousPhoneNumber != $phone->getPhoneNumber()){
                    $messages = $this->sendActivationCode(true,$session, $phone);

                    $validationUrl = $this->generateUrl('cairn_user_api_phone_add',array('remote'=>'mobile','id'=>$user->getID()));

                    return $this->getRedirectionResponse(
                        'cairn_user_users_phone_add', 
                        ['username'=>$user->getUsername()],
                        ['validation_url'=>$validationUrl,'phone'=>$phone], 
                        Response::HTTP_OK,
                        $messages
                    );

                }
            }
        }

        return $this->getFormResponse(
            'CairnUserBundle:User:phone.html.twig', 
            ['formPhone' => $formPhone->createView()],
            $formPhone
        );
    }
   

    public function sendActivationCode($isNewEntity,$session, Phone $phone)
    {
        $currentUser = $this->getUser();
        $targetUser = $phone->getUser();
        $em = $this->getDoctrine()->getManager();
        $encoder = $this->get('security.encoder_factory')->getEncoder($currentUser);

        if($currentUser->getNbPhoneNumberRequests() >= 3){
            $message = ['key'=>'wrong_code_cancel'];
            return $this->getErrorsResponse($message, [] ,Response::HTTP_OK,$this->generateUrl('cairn_user_profile_view',['username'=>$user->getUsername()]));
        }

        $currentUser->setNbPhoneNumberRequests($currentUser->getNbPhoneNumberRequests() + 1);

        //at this step, flush is impossible because entitymanager would consider $smsData object as a brand new object
        //then, an exception would be thrown due to unique key constraint error
        //that's why we retrieve the instance right away and refresh it to not flush modifications, as it should be done only
        //after code validation
        $newPhoneNumber = $phone->getPhoneNumber(); //let's save it before refresh

        if(!$isNewEntity){ //otherwise, nothing to refresh
            //detach is necessary for serialization of associated entities before setting it in session

            $em->detach($phone);
            $targetUser->getSmsData()->removePhone($phone);
            $session->set('phone',$phone);
            $phone = $em->merge($session->get('phone'));

            $em->refresh($phone);
        }else{
            $session->set('phone',$this->get('cairn_user.api')->serialize($phone));
        }

        //give a new code to be validated
        if($this->getParameter('kernel.environment') != 'prod'){
            $code = 1111;
        }else{
            $code = rand(1000,9999);
        }

        // send SMS with validation code to current user's new phone number
        $sms = $this->get('cairn_user.message_notificator')->sendSMS($newPhoneNumber,'Code de validation de votre téléphone '.$code);
        $em->persist($sms);
        $em->flush();

        $session_code = $encoder->encodePassword($code,$currentUser->getSalt());
        $session->set('activationCode', $session_code);

        $existPhone = $em->getRepository('CairnUserBundle:Phone')->findOneBy(array('phoneNumber'=>$newPhoneNumber));

        $messages = [];
        if($existPhone){
            $messages[] = ['key'=>'pro_and_person_assoc_phone','args'=>[$newPhoneNumber]]; 
        }

        $messages[] = ['key'=>'sms_code_sent','args'=>[$newPhoneNumber]];

        return $messages;
    }



    private function setSmsAccessClient(User $user)
    {
        $securityService = $this->get('cairn_user.security');
        $currentUser = $this->getUser();

        $accessClientVO = $this->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($user->getCyclosID(), 'client_sms' ,array('BLOCKED','ACTIVE'));

        if( (! $accessClientVO) && ($user === $currentUser)){
            $securityService = $this->get('cairn_user.security');
            $securityService->createAccessClient($user,'client_sms');
            $accessClientVO = $this->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($user->getCyclosID(), 'client_sms' ,'UNASSIGNED');

            $smsClient = $securityService->changeAccessClientStatus($accessClientVO,'ACTIVE');
            $smsClient = $securityService->vigenereEncode($smsClient);
            $user->getSmsData()->setSmsClient($smsClient);
        }
    }


    private function setNewPhoneNumber(User $user, Phone $phone, $previousPhoneNumber)
    {

        $em = $this->getDoctrine()->getManager();
        $smsData = $user->getSmsData();
        //we check if the new number was associated to a personal and professional account
        //if so, send message
        if($previousPhoneNumber){
            $existingUsers = $em->getRepository('CairnUserBundle:User')->findUsersByPhoneNumber($previousPhoneNumber);
            if(count($existingUsers) == 2){
                if($user->hasRole('ROLE_PERSON')){
                    $session->getFlashBag()->add('info','Le compte professionnel associé au numéro '.$previousPhoneNumber. ' peut désormais réaliser des opérations par SMS');
                }
            }
        }

        $smsData->setUser($user);
        $phone->setSmsData($smsData);
        $smsData->addPhone($phone);

        $em->flush();

    }


    public function checkActivationCode($formPhone, Request $request, User $user, $previousPhoneNumber = NULL)
    {
        $session = $request->getSession();
        $currentUser = $this->getUser();
        $smsData = $user->getSmsData();
        $em = $this->getDoctrine()->getManager();
        $encoder = $this->get('security.encoder_factory')->getEncoder($currentUser);
        $apiService = $this->get('cairn_user.api');

        $providedCode = $formPhone->get('activationCode')->getData();
        $session_code = $session->get('activationCode');

        if($formPhone->get('cancel')->isClicked()){
            $session->remove('activationCode');
            $session->remove('phone');

            return $this->getRedirectionResponse(
                'cairn_user_profile_view', 
                ['username' => $user->getUsername()],
                [], 
                Response::HTTP_OK,
                ['key'=>'cancel_button']
            );
        }



        if($providedCode != NULL){
            //valid code
            if($encoder->encodePassword($providedCode,$currentUser->getSalt()) == $session_code){

                $messages = [];
                if(! $previousPhoneNumber){
                    $res = $apiService->deserialize($session->get('phone'),'Cairn\UserBundle\Entity\Phone');
                    $phone = $em->merge($res);
                }else{
                    $phone = $em->merge($session->get('phone'));
                }

                $currentUser->setNbPhoneNumberRequests(0);
                $currentUser->setPhoneNumberActivationTries(0);

                if(! $user->getSmsData()->getSmsClient()){
                    $this->setSmsAccessClient($user);
                }

                //we check if the new number was associated to a personal and professional account
                //if so, send message
                if($previousPhoneNumber){
                    $existingUsers = $em->getRepository('CairnUserBundle:User')->findUsersByPhoneNumber($previousPhoneNumber);
                    if(count($existingUsers) == 2){
                        if($user->hasRole('ROLE_PERSON')){
                            $messages[] = ['key'=>'account_still_assoc_phone','args'=> [$previousPhoneNumber]];
                        }
                    }
                }

                $smsData->setUser($user);
                $phone->setSmsData($smsData);
                $smsData->addPhone($phone);

                $em->flush();
                $messages[] = ['key'=>'registered_operation'];


                $session->remove('activationCode');
                $session->remove('phone');
                $session->remove('is_first_connection');

                if( ($currentUser === $user) && $phone->isPaymentEnabled()){
                    return $this->getRenderResponse(
                        'CairnUserBundle:Default:howto_sms_page.html.twig',
                        [],
                        $phone,
                        Response::HTTP_CREATED,
                        $messages
                    );
                }else{
                    return $this->getRedirectionResponse(
                        'cairn_user_profile_view', 
                        ['username' => $user->getUsername()],
                        $phone, 
                        Response::HTTP_CREATED,
                        $messages
                    );
                }

                //invalid code
            }else{
                $messages = [];
                $errors = [];
                $currentUser->setPhoneNumberActivationTries($currentUser->getPhoneNumberActivationTries() + 1);
                $remainingTries = 3 - $currentUser->getPhoneNumberActivationTries();
                if($remainingTries > 0){
                    $errors[] = ['key'=>'wrong_code'];
                    $messages[] = ['key'=>'remaining_tries','args'=> [$remainingTries]];
                }else{
                    $errors[] = ['key'=>'wrong_code'];
                    $messages[] = ['key'=>'too_many_errors_block'];
                    $this->get('cairn_user.access_platform')->disable(array($currentUser),'phone_tries_exceeded');
                }

                $em->flush();

                return $this->getErrorsResponse($errors, $messages ,Response::HTTP_OK,$request->getRequestUri());
            }

        }else{//provided code is NULL
            return $this->getErrorsResponse(['key'=>'field_not_found','args'=>['activationCode']], [] ,Response::HTTP_BAD_REQUEST,$request->getRequestUri());
        }
    }

    /**
     * Changes the current user's sms data
     *
     * This action permits to change current user's sms data, such as phone number, or status enabled/disabled
     */
    public function editPhoneAction(Request $request, Phone $phone)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $currentUser = $this->getUser();
        $user = $phone->getUser();
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);

        $apiService = $this->get('cairn_user.api');
        $isRemoteCall = $apiService->isRemoteCall();

        //****************** All cases where edit sms is not allowed ****************//
        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('not_referent');
        }

        if(! $user->isAdherent() ){
            throw new AccessDeniedException('reserved_for_members');
        }

        //if(! $user->getCard()){
        //    $session->getFlashBag()->add('error','Pas de carte de sécurité associée !');
        //    return $this->redirectToRoute('cairn_user_profile_view',array('username' => $currentUser->getUsername()));
        //}

        $previousPhoneNumber = $phone->getPhoneNumber();


        if($currentUser->getNbPhoneNumberRequests() >= 3 && !$session->get('activationCode')){
            $message = ['key'=>'too_many_tries_cancel'];
            return $this->getErrorsResponse($message,[] ,Response::HTTP_OK,$this->generateUrl('cairn_user_profile_view',['username'=>$user->getUsername()]));
        }

        //************************ end of cases where edit sms is disallowed *************************//

        $formPhone = $this->createForm(PhoneType::class, $phone);

        if($request->isMethod('POST')){
            if($isRemoteCall){
                $formPhone->submit(json_decode($request->getContent(), true));
            }else{
                $formPhone->handleRequest($request);
            }

            // POST request is an activation code to validate a new phone number
            if($formPhone->has('activationCode')){
                return $this->checkActivationCode($formPhone,$request, $user, $previousPhoneNumber);
            }

            if($formPhone->isValid()){
                $dataForm = $formPhone->getData();
            
                // POST request is a new phone number for an existing entity smsData
                if($previousPhoneNumber != $phone->getPhoneNumber()){
                    //if($user !== $currentUser ){
                    //    throw new AccessDeniedException('Action réservée à '.$user->getName());
                    //}
                    $messages = $this->sendActivationCode(false,$session, $phone);
                    
                    $validationUrl = $this->generateUrl('cairn_user_api_phone_edit',array('remote'=>'mobile','id'=>$phone->getID()));

                    return $this->getRedirectionResponse(
                        'cairn_user_users_phone_edit', 
                        ['id'=>$phone->getID()],
                        ['validation_url'=>$validationUrl,'phone'=>$phone], 
                        Response::HTTP_OK,
                        $messages
                    );


                }else{// POST request does not concern a new phone number

                    $em->flush();

                    if($phone->isPaymentEnabled() ){
                        $message = ['key'=>'sms_payment_authorized','args'=>[$phone->getPhoneNumber()]];
                    }else{
                        $message = ['key'=>'sms_payment_unauthorized','args'=>[$phone->getPhoneNumber()]];
                    }

                    return $this->getRedirectionResponse(
                        'cairn_user_profile_view', 
                        ['username' => $user->getUsername()],
                        $phone,
                        Response::HTTP_OK,
                        $message
                    );

                }

            }
        }

        return $this->getFormResponse(
                'CairnUserBundle:User:phone.html.twig',
                ['formPhone'=>$formPhone->createView()],
                $formPhone
            );
    }

    /**
     *
     *@TODO : protect behind post request
     */
    public function deletePhoneAction(Request $request, Phone $phone)
    {
        $session = $request->getSession();
        $apiService = $this->get('cairn_user.api');

        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        $user = $phone->getUser();

        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        //****************** All cases where delete sms is not allowed ****************//
        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('not_referent');
        }

        if(! ($user->hasRole('ROLE_PRO') || $user->hasRole('ROLE_PERSON')) ){
            throw new AccessDeniedException('reserved_for_members');
        }

        $phoneNumber = $phone->getPhoneNumber();
        $em->remove($phone);
        $em->flush();

        
        $message = ['key'=>'phone_removal_success','args'=>[$phoneNumber]];
        $phones = $user->getPhones(); 
        $phones = is_array($phones) ? $phones : $phones->getValues();

        return $this->getRedirectionResponse(
            'cairn_user_profile_view', 
            ['username'=>$user->getUsername()],
            $phones, 
            Response::HTTP_OK,
            $message
        );
    }


    /**
     * List API options related to user URI 
     *
     */
    public function optionsAction(Request $request)                           
    {   
        $template = array(
            'notes'=> '',
            'path'=> '',
            'method'=> '',
            'Parameters'=> array(
                'query'=>array(),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(),
                'access denied'=> array(),
                'error'=> array()
            )
        );

        $options = array();
        $options['get users list'] = array(
            'notes'=> 'Request list of users',
            'path'=> 'api/users',
            'method'=> 'GET',
            'Parameters'=> array(
                'query'=>array(),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful request'
                ),
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'you do not have access to these users'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['get user profile'] = array(
            'notes'=> 'Request profile data for an user with id {id}',
            'path'=> "api/users/{id}",
            'method'=> 'GET',
            'Parameters'=> array(
                'query'=>array(
                    'id'=>array(
                        'description'=> 'user id (required)',
                        'data_type'=> 'int'
                    )
                ),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful request'
                ),
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'forbidden access'
                 ),
                'undefined user'=> array(
                     'status code'=> '404',
                     'reason'=> 'no user with given id'
                 ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['post'] = array(
            'notes'=> 'Create a new user using provided data',
            'path'=> 'api/users',
            'method'=> 'POST',
            'Parameters'=> array(
                'query'=>array(
                    'type'=>array(
                        'description'=> 'kind of user to add',
                        'data_type'=> 'string',
                        'values'=> array('pro','localGroup','superAdmin')
                    )
                ),
                'body'=>array(
                    'name'=>array(
                        'description'=> 'User name (required)',
                        'data_type'=> 'string'
                    ),
                    'username'=>array(
                        'description'=> 'User login (required)',
                        'data_type'=> 'string'
                    ),
                    'address'=>array(
                        'address1'=> array(
                            'description'=> 'User main address (required)',
                            'data_type'=> 'string'
                        ),
                        'address2'=> array(
                            'description'=> 'User complement address',
                            'data_type'=> 'string'
                        ),
                        'city'=> array(
                            'description'=> 'User current city (required)',
                            'data_type'=> 'string'
                        ),
                        'zipcode'=> array(
                            'description'=> 'Zipcode of the city (required)',
                            'data_type'=> 'int'
                        ),

                    ),
                    'description'=>array(
                        'description'=> 'Provide a few words on user\'s activity',
                        'data_type'=> 'text'
                    ),
                    'logo'=>array(
                        'description'=> 'User avatar',
                        'data_type'=> 'image',
                        'mimeTypes'=> array('image/jpeg','image/jpg','image/png','image/gif')
                    )
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '201',
                     'reason'=> 'successful user creation'
                ),
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'Given current user\'s role, creating a user is not authorized'
                 ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );        

        $options['put'] = array(
            'notes'=> ' Update user with id {id} using provided properties',
            'path'=> 'api/users/{id}',
            'method'=> 'PUT',
            'Parameters'=> array(
                'query'=>array(
                    'id'=>array(
                        'description'=> 'user id (required)',
                        'data_type'=> 'int'
                    )
                ),
                'body'=>array(
                    'name'=>array(
                        'description'=> 'User name',
                        'data_type'=> 'string'
                    ),
                    'username'=>array(
                        'description'=> 'User login',
                        'data_type'=> 'string'
                    ),
                    'address'=>array(
                        'address1'=> array(
                            'description'=> 'User main address',
                            'data_type'=> 'string'
                        ),
                        'address2'=> array(
                            'description'=> 'User complement address',
                            'data_type'=> 'string'
                        ),
                        'city'=> array(
                            'description'=> 'User current city',
                            'data_type'=> 'string'
                        ),
                        'zipcode'=> array(
                            'description'=> 'Zipcode of the city',
                            'data_type'=> 'int'
                        ),

                    ),
                    'description'=>array(
                        'description'=> 'Provide a few words on user\'s activity',
                        'data_type'=> 'text'
                    ),
                    'logo'=>array(
                        'description'=> 'User avatar',
                        'data_type'=> 'image',
                        'mimeTypes'=> 'image/jpeg','image/jpg','image/png','image/gif'
                    )
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user update'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['delete'] = array(
            'notes'=> 'Request delete user with id {id}',
            'path'=> 'api/users/{id}',
            'method'=> 'DELETE',
            'Parameters'=> array(
                'query'=>array(
                    'id'=> array(
                      'description'=> 'user id (required)',
                      'data_type'=> 'int'
                  )
                ),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user update'
                ),
                'access denied'=> array(
                 'status code'=> '403',
                 'reason'=> 'forbidden removal on given user => not referent or not the user himself'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );
        $options['Change_password'] = array(
            'notes'=> 'Change password',
            'path'=> 'api/users/change-password',
            'method'=> 'PATCH',
            'Parameters'=> array(
                'query'=> array(),
                'body'=> array(
                    'current password'=> array(
                      'description'=> 'User current password (required)',
                      'data_type'=> 'string'
                    ),
                    'new password'=> array(
                      'description'=> 'User requested password (required)',
                      'data_type'=> 'string'
                    ),
                    'confirm new password'=> array(
                      'description'=> 'Confirm requested password (required)',
                      'data_type'=> 'string'
                    ),
                ),
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful password change'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );
        $options['associate_card'] = array(
            'notes'=> 'Associate a security card to a user',
            'path'=> 'api/users/{id}/associate-card',
            'method'=> 'PATCH',
            'Parameters'=> array(
                'query'=>array(
                    'id'=> array(
                        'description'=> 'user id (required)',
                        'data_type'=> 'int'
                    ),
                ),
                'body'=>array(
                    'current password'=> array(
                        'description'=> 'User current password (required)',
                        'data_type'=> 'string'
                    ),
                    'new password'=> array(
                        'description'=> 'User requested password (required)',
                        'data_type'=> 'string'
                    ),
                    'confirm new password'=> array(
                        'description'=> 'Confirm requested password (required)',
                        'data_type'=> 'string'
                    ),
             
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user creation'       
                ),
                'access denied'=> array(
                 'status code'=> '403',
                 'reason'=> 'forbidden removal on given user => not referent or not the user himself'
                ),
                'error'=> array(
                      'status code'=> '500',
                     'reason'=> 'error on server-side'           
                ),
            )
        );

        return new Response(json_encode($options));
    }                      


    /**
     * View the profile of $user
     *
     * What will be displayed on the screen will depend on the current user
     *
     * @param User $user User with profile to view
     */
    public function viewProfileAction(Request $request , User $user, $_format)                           
    {                                                                          
        $currentUser = $this->getUser();

        //$personVisitingPro = ($currentUser->hasRole('ROLE_PERSON') && $user->hasRole('ROLE_PRO'));

        if(! ( ($user === $currentUser) || $user->hasReferent($currentUser) ) ){
            throw new AccessDeniedException('not_access_rights');
        }

        if( (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) && $user->hasRole('ROLE_SUPER_ADMIN')){
            throw new AccessDeniedException('not_access_rights');
        } 

        $accounts = NULL;
        if($user->hasReferent($currentUser)){
            if($user->getMainICC()){
                $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($user->getCyclosID());
            }
        }

        $form = $this->createForm(ConfirmationType::class);

        return $this->getRenderResponse(
                'CairnUserBundle:Pro:view.html.twig',
                ['form'=>$form->createView(), 'user'=>$user,'accounts'=>$accounts],
                $user,
                Response::HTTP_OK
            );
    }                      

    public function downloadUserDocumentAction(Request $request, CairnFile $file)
    {
        $currentUser = $this->getUser();
        $userRepo = $this->getDoctrine()->getManager()->getRepository('CairnUserBundle:User');

        if( preg_match('#logo#',$request->get('_route')) ){
            $user = $userRepo->findOneByImage($file);
        }elseif(preg_match('#iddoc#',$request->get('_route'))){
            $user = $userRepo->findOneByIdentityDocument($file);
        }else{
            $session->getFlashBag()->add('Type de fichier demandé incorrect');
            return $this->redirectToRoute('cairn_user_profile_view',array('username' => $currentUser->getUsername()));
        }

        if(! ( ($user === $currentUser) || $user->hasReferent($currentUser) ) ){
            throw new AccessDeniedException('Ce document n\'existe pas');
        }

        if( preg_match('#logo#',$request->get('_route')) && (! $user->hasRole('ROLE_PRO')) ){
            $session->getFlashBag()->add('Réservé aux professionnels');
            return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
        }

        $env = $this->getParameter('kernel.environment');
        return $this->file($file->getWebPath($env), 'piece-identite_'.$user->getUsername().'.'.$file->getUrl());
    }

    /**
     * Set the enabled attribute of user with provided ID to false
     *
     * An email is sent to the user being blocked
     *
     * @throws  AccessDeniedException Current user making request is not a referent of the user being involved
     * @Method("GET")
     */ 
    public function blockUserAction(Request $request, User $user)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $apiService = $this->get('cairn_user.api');

        $currentUser = $this->getUser();

        if(! ( ($user === $currentUser) || $user->hasReferent($currentUser) ) ){
            throw new AccessDeniedException('not_access_rights');
        }elseif(!$user->isEnabled()){

            $message = ['key'=>'account_already_blocked','args'=>[$user->getName()]];

            return $this->getRedirectionResponse(
                'cairn_user_profile_view', 
                ['username' => $user->getUsername()],
                $user,
                Response::HTTP_OK,
                $message
            );
        }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST')){
            if($apiService->isRemoteCall()){
                $jsonRequest = json_decode($request->getContent(), true);
                $form->submit($jsonRequest);
            }else{
                $form->handleRequest($request);
            }
           
            if($form->get('save')->isClicked()){
                
                $subject = 'Opposition de compte [e]-Cairn';

                $reason = ($user === $currentUser) ? 'self' : 'admin';
                $this->get('cairn_user.access_platform')->disable(array($user),$reason,$subject);
                $em->flush();

                $message = ['key'=>'registered_operation','args'=>[$user->getName()]];

                return $this->getRedirectionResponse(
                    'cairn_user_profile_view', 
                    ['username' => $user->getUsername()],
                    $user,
                    Response::HTTP_OK,
                    $message
                );

            }
        }

        $responseArray = array('user' => $user,'form'=> $form->createView());
        return $this->render('CairnUserBundle:User:block.html.twig', $responseArray);
    }


    /**
     *Removes a user with id given in query
     *
     * This operation is considered as super sensible. It needs the security layer + the password input
     * If the input is incorrect, user's attribute 'cardKeyTries' or 'passwordTries' is incremented. 3 failures leads to disable the user.
     *
     * A user can remove its own member area, or an admin can do it if he is a referent.
     *
     * If the user to remove is a ROLE_ADHERENT, we ensure that all his accounts have a balance to zero
     *
     * @Method("GET")
     *
     * Can only be done by an admin on Cyclos side. 
     * Solution 1 : create a specific screen for all users with attribute "ask_removal = true" and remove them all in once in admin's
     * action. 
     *
     * Problem : If a user asks for removal and is accepted, it means that all his accounts are at 0. But if he receives a payment
     * between this and validation by admin, his account won't be 0 so he will not be removed. But there is no way to check (from an ICC provided) that the corresponding Symfony User has this attrbute, because an user can't look for the account of soemone else.
     *
     * Solution 1 : récupérer un compte via un account_number (configurer un account_number : script de génération +  service d'interception 'création de compte' + visiblité dans les Produits). Ainsi on pourrait récupérer, à partir du account_number, le propriétaire du compte puis son équivalent Symfony pour dire s'il est en instance de suppression ou non. (Long car beaucoup de nouvelles choses à voir)
     *
     * Solution 2 : vérifier le message d'erreur à la suppression(ConstraintViolatedOnRemoveException) et, si la suppression ne peut avoir lieu à cause d'un compte non nul, avertir l'utilisateur
     *
     */
    public function confirmRemoveUserAction(Request $request, User $user, $_format)
    {
        $currentUser = $this->getUser();

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $messageNotificator = $this->get('cairn_user.message_notificator');

        $userRepo = $em->getRepository('CairnUserBundle:User');

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        //check that account balances are all 0 (for adherents -PRO/PERSON only)
        try{ 
            $ownerVO = $this->get('cairn_user_cyclos_user_info')->getUserVO($user->getCyclosID());
        }catch(\Exception $e){                                     
            if( $e->errorCode == 'ENTITY_NOT_FOUND'){ //user has registered but never activated

                $emailTo = $user->getEmail();
                $em->remove($user);
                $em->flush();

                $subject = 'Ouverture de compte [e]-Cairn refusée';
                $from = $messageNotificator->getNoReplyEmail();
                $to = $emailTo;
                $body = $this->renderView('CairnUserBundle:Emails:opening_refused.html.twig');
    
                $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                $session->getFlashBag()->add('success','L\'ouverture de compte de '.$user->getName().' a été refusée. Compte clôturé');
                return $this->redirectToRoute('cairn_user_users_dashboard');
            }else{
                throw $e;
            }
        }

        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id,NULL);

        if($user->hasRole('ROLE_PRO') || $user->hasRole('ROLE_PERSON')){
            foreach($accounts as $account){
                if($account->status->balance != 0){
                    $session->getFlashBag()->add('error','Certains comptes ont un solde non nul. La clôture du compte ne peut aboutir.');
                    return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format,'username' => $user->getUsername()));
                }
            }
        }

        $form = $this->createForm(ConfirmationType::class);
        $form->add('current_password', PasswordType::class,array('label'=>'Mot de passe','required'=>false,
            'constraints'=> new UserPassword() ));
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                if($form->get('save')->isClicked()){
                    if($currentUser->isAdmin()){
                        $redirection = 'cairn_user_users_dashboard';
                        $isRemoved = $this->removeUser($user, $currentUser);

                        if($isRemoved){
                            $session->getFlashBag()->add('success','Espace membre supprimé avec succès');
                        }else{
                            $session->getFlashBag()->add('error','La fermeture de compte a échoué. ');
                            return $this->redirectToRoute('cairn_user_profile_view',array('username'=> $user->getUsername()));
                        }
                    }else{//is ROLE_PRO or ROLE_PERSON : $user == $currentUser
                        $user->setRemovalRequest(true);
                        $this->get('cairn_user.access_platform')->disable(array($user),'removal_request');

                        $redirection = 'fos_user_security_logout';
                        $session->getFlashBag()->add('success','Votre demande de clôture d\'espace membre a été prise en compte');
                    }

                    $em->flush();

                    return $this->redirectToRoute($redirection);
                }
                else{
                    $session->getFlashBag()->add('info','La demande de clôture a été annulée.');
                    return $this->redirectToRoute('cairn_user_profile_view',array('username'=> $user->getUsername()));
                }
            }
        }

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'user'=>$user));
        }
        return $this->render('CairnUserBundle:Pro:confirm_remove.html.twig',array('form'=>$form->createView(),'user'=>$user));
    }

    /**
     *Does remove the user on both the Cyclos and Doctrine sides and sends email to removed user and their referents
     *
     * It removes the user from Doctrine and all related Beneficiary entities with it.
     *@param User $user User to be removed
     *@todo : test query builder request using a join to get beneficiaries associated to user to see if it is faster
     */
    public function removeUser(User $user, User $currentUser)
    {
        $em = $this->getDoctrine()->getManager();
        $depositRepo = $em->getRepository('CairnUserBundle:Deposit');
        $mandateRepo = $em->getRepository('CairnUserBundle:Mandate');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $messageNotificator = $this->get('cairn_user.message_notificator');

        $referents = $user->getReferents();

        $saveName = $user->getName();
        $isPro = $user->hasRole('ROLE_PRO');

        $params = new \stdClass();
        $params->status = 'REMOVED';
        $params->user = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        try{
            $emailTo = $user->getEmail();


            //if deposit scheduled, cancel removal
            $scheduledDeposits = $depositRepo->findBy(array('creditor'=>$user,'status'=> Deposit::STATE_SCHEDULED));

            if($scheduledDeposits){ return false; }

            $deposits = $depositRepo->findBy(array('creditor'=>$user));

            foreach($deposits as $deposit){
                $em->remove($deposit);
            }

            //if mandate ongoing and overdued, cancel removal
            $mb = $mandateRepo->createQueryBuilder('m');
            $mandateRepo->whereContractor($mb, $user);

            $status = array(Mandate::OVERDUE);
            $mandateRepo->whereStatus($mb, $status);

            $mandates = $mb->getQuery()->getResult();

            if($mandates){ return false; }

            $scheduledOperations = $operationRepo->findBy(array('debitor'=>$user, 'type'=>Operation::TYPE_TRANSACTION_SCHEDULED));
            foreach($scheduledOperations as $operation){
                $operation->setType(Operation::TYPE_SCHEDULED_FAILED);
            }

            $subject = 'Compte [e]-Cairn clôturé';
            $from = $messageNotificator->getNoReplyEmail();
            $to = $emailTo;
            $body = $this->renderView('CairnUserBundle:Emails:farwell.html.twig',array('receiver'=>'user','removedUser'=>$user));

            $em->remove($user);
            $this->userManager->changeStatusUser($params);

            //send email AFTER user has effectively been removed
            $messageNotificator->notifyByEmail($subject,$from,$to,$body);

            if($isPro){
                $subject = 'Compte [e]-Cairn pro fermé';
                $body = $saveName .' a été supprimé de la plateforme par '. $currentUser->getName();
                $body = $this->renderView('CairnUserBundle:Emails:farwell.html.twig',array('receiver'=>'admin','user_name'=>$saveName));
                foreach($referents as $referent){
                    $to = $referent->getEmail();
                    $messageNotificator->notifyByEmail($subject,$from,$to,$body);
                }
            }

            return true;

        }catch(\Exception $e){

            if( ($e instanceof Cyclos\ServiceException) && ($e->errorCode == 'VALIDATION')){

                $errors = $e->error->validation->allErrors;
                for($i = 0; $i < count($errors); $i++){
                    if( strpos( $errors[$i], 'has a non-zero balance') !== false ){
                        return false;
                    }
                }
                throw $e;
            }else{
                throw $e;
            }
        }

    }

    /**
     * Removes definitively users that requested to be removed
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removePendingUsersAction(Request $request, $_format)
    {
        $session = $request->getSession();
        $messageNotificator = $this->get('cairn_user.message_notificator');

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $currentUser = $this->getUser();

        //members on pending removal status
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereReferent($ub, $currentUser->getID());
        $listUsers = $ub
            ->andWhere('u.removalRequest = true')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        $form = $this->createForm(ConfirmationType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($form->get('save')->isClicked()){
                $notRemovedUsers = '';
                foreach($listUsers as $user){
                    $isRemoved = $this->removeUser($user, $currentUser);
                    if(!$isRemoved){
                        $user->setRemovalRequest(false);
                        $notRemovedUsers = $notRemovedUsers.', '.$user->getName();

                        $subject = 'Demande de clôture non aboutie';
                        $from = $messageNotificator->getNoReplyEmail();
                        $to = $user->getEmail();
                        $body = 'Votre demande de clôture de compte [e]-Cairn n\'a pas pu aboutir. Vérifiez que votre compte est bien soldé. Si oui, veuillez prendre contact avec l\'Association.'."\n"."\n".'Le Cairn,';
            
                        $messageNotificator->notifyByEmail($subject,$from,$to,$body);

                    }
                }

                $em->flush();

                if($notRemovedUsers != ''){
                    $session->getFlashBag()->add('info','Les comptes des membres suivants n\'ont pas pu être supprimés : ' .$notRemovedUsers); 
                    $session->getFlashBag()->add('info','Raison : Leurs comptes ne sont probablement plus soldés, même s\'ils l\'étaient au moment de leur demande'); 
                }else{
                    $session->getFlashBag()->add('success','Tous les comptes ont pu être clôturés avec succès'); 
                }
                return $this->redirectToRoute('cairn_user_users_dashboard');

            }else{
                $session->getFlashBag()->add('info','Demande de clôture annulée'); 
                return $this->redirectToRoute('cairn_user_users_dashboard');
            }
        }
        return $this->render('CairnUserBundle:Pro:confirm_remove_pending.html.twig',
            array('form'=>$form->createView(),'listUsers'=>$listUsers));

    }

}
