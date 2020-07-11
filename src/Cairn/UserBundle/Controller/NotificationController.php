<?php
// src/Cairn/UserBundle/Controller/NotificationController.php

namespace Cairn\UserBundle\Controller;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\NotificationData;
use Cairn\UserBundle\Entity\BaseNotification;
use Cairn\UserBundle\Entity\PaymentNotification;
use Cairn\UserBundle\Entity\RegistrationNotification;
use Cairn\UserBundle\Entity\WebPushSubscription;
use Cairn\UserBundle\Entity\PushTemplate;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

//manage Forms
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\NotificationDataType;
use Cairn\UserBundle\Form\PushTemplateType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This class contains actions related to account operations 
 *
 * @Security("is_granted('ROLE_ADHERENT')")
 */
class NotificationController extends BaseController
{   

    public function tokenSubscriptionAction(Request $request, $from)
    {
        $em = $this->getDoctrine()->getManager();
        $apiService = $this->get('cairn_user.api');

        $currentUser = $this->getUser();

        $notificationData = $this->initNotificationData($currentUser);

        $jsonRequest = json_decode($request->getContent(), true);

        if($from == 'mobile'){
            $missingFields = [];
            if(! isset($jsonRequest['device_token'])){
                $missingFields['field_not_found'] = ['device_token'];
            }
            if(! isset($jsonRequest['platform'])){
                $missingFields['field_not_found'] = ['platform'];
            }
            if(! isset($jsonRequest['action'])){
                $missingFields['field_not_found'] = ['action'];
            }

            if(count($missingFields) != 0){
                return $this->getErrorsResponse($missingFields, [] ,Response::HTTP_BAD_REQUEST);
            }

            $deviceToken = $jsonRequest['device_token'];
            $platform = $jsonRequest['platform'];
            $action = strtoupper($jsonRequest['action']);

            if($action == 'POST'){
                $notificationData->addDeviceToken($deviceToken,$platform);
                $em->flush();
                return $this->getRenderResponse(
                    '',
                    [],
                    $notificationData,
                    Response::HTTP_CREATED
                );
            }elseif($action == 'DELETE'){
                $notificationData->removeDeviceToken($deviceToken,$platform);
                $em->flush();
                return $this->getRenderResponse(
                    '',
                    [],
                    $notificationData,
                    Response::HTTP_OK
                );
            }else{
                return $this->getErrorsResponse(['invalid_field_value'=>['action',$action]],[],Response::HTTP_BAD_REQUEST);
            }
        }else{
            $subscription = $jsonRequest['subscription'];

             //validate endpoint exists
             if(! array_key_exists('endpoint',$subscription)){
                 return $this->getErrorsResponse(['field_not_found'=>['endpoint']],[],Response::HTTP_BAD_REQUEST);
             }

             //validate keys because we need payload support
             if(! array_key_exists('keys',$subscription)){
                 return $this->getErrorsResponse(['field_not_found'=>['keys']],[],Response::HTTP_BAD_REQUEST);
             }else{
                 if( (! array_key_exists('p256dh',$subscription['keys'])) || (! array_key_exists('auth',$subscription['keys']))){
                     return $this->getErrorsResponse(['field_not_found'=>['p256dh/auth']],[],Response::HTTP_BAD_REQUEST);
                 }
             }

             //TODO : DEAL WITH MACOS ENDPOINTS
             $isMacOSEndpoint = false;
             $pushSubscription = new WebPushSubscription($subscription['endpoint'],$isMacOSEndpoint,$subscription['keys'] );
             $pushSubscription->setNotificationData($notificationData);
             $notificationData->addWebPushSubscription($pushSubscription);


             $data = array(
                 'title'=>'Notifications [e]-Cairn',
                 'payload' => [
                    'tag' => 'subscription',
                    'body' => 'Ce navigateur est désormais enregistré comme destinataire des notifications'
                 ]
             );
            $this->get('cairn_user.message_notificator')->sendWebPushNotifications(array($pushSubscription),$data,'subscription',0,'normal');

             $em->flush();
             
             return $this->getRenderResponse(
                    '',
                    [],
                    [],
                    Response::HTTP_CREATED
                );

        }
    }

    private function initNotificationData (User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $notificationData = $user->getNotificationData();

        if(! $notificationData){
            $notificationData = new NotificationData($user);
            $em->persist($notificationData);
        }

        if($notificationData->getBaseNotifications()->count() == 0){
            $ppNotif = new PaymentNotification();
            $rpNotif = new RegistrationNotification();

            $ppNotif->setNotificationData($notificationData);
            $rpNotif->setNotificationData($notificationData);

            $notificationData->addBaseNotification($ppNotif);
            $notificationData->addBaseNotification($rpNotif);

            $em->persist($ppNotif);
            $em->persist($rpNotif);
        }

        return $notificationData;
    }

    public function notificationParamsAction(Request $request, User $user)
    {
        $currentUser = $this->getUser(); 
        
        $em = $this->getDoctrine()->getManager();
        $apiService = $this->get('cairn_user.api');
        $isRemoteCall = $apiService->isRemoteCall();

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('not_referent');
        }

        $notificationData = $this->initNotificationData($user);
        
        $form = $this->createForm(NotificationDataType::class,$notificationData);

        if($request->isMethod('POST')){
            if($isRemoteCall){
                $jsonRequest = json_decode($request->getContent(), true);
                $form->submit($jsonRequest);
            }else{
                $form->handleRequest($request);
            }

            if($form->isValid()){
                $em->flush();

                $message = ['notif_params_updated'=>[]];
                return $this->getRedirectionResponse(
                    'cairn_user_profile_view', 
                    ['username' => $user->getUsername()],
                    $notificationData, 
                    Response::HTTP_CREATED,
                    $message
                );

            }
        }else{
            //si l entité vient d être créée, flusher pour toujours avoir des ID même dès le 1er GET
            if(! $notificationData->getBaseNotifications()[0]->getID()){
                $em->flush();
            }
            if($isRemoteCall){
                return $this->getRedirectionResponse(
                    '', 
                    [],
                    $notificationData, 
                    Response::HTTP_OK
                );
            }
        }

        return $this->getFormResponse(
            'CairnUserBundle:Notification:_form.html.twig', 
            ['form' => $form->createView(),'user' => $user],
            $form 
        );
    }

    /**
     * Send Push Notification regarding user as @param
     *
     * @param  User $user  Must have role ROLE_PRO
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function sendProPushNotificationAction(Request $request,User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $messageNotificator = $this->get('cairn_user.message_notificator');

        if(! $user->hasRole('ROLE_PRO')){
            $message = ['not_pro'=>[$user->getName()]];

             return $this->getRedirectionResponse(
                 'cairn_user_profile_view', 
                 ['username' => $user->getUsername()],
                 [], 
                 Response::HTTP_OK,
                 $message
             );
         }

        $pushTemplate = new PushTemplate();
        $url = ($user->getUrl()) ? $user->getUrl() : 'https://'; 
        $pushTemplate->setRedirectionUrl($url);

        $form = $this->createForm(PushTemplateType::class,$pushTemplate);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){
                $messageNotificator->sendRegisterNotifications($user, $pushTemplate);
                $message = ['push_pro_sent'=>[$user->getName()]];

                $em->flush();
            }else{
                $message = ['cancel_button'=>[]];
            }

            return $this->getRedirectionResponse(
                    'cairn_user_profile_view', 
                    ['username' => $user->getUsername()],
                    [], 
                    Response::HTTP_OK,
                    $message
                );
        }

        return $this->getFormResponse(
            'CairnUserBundle:Notification:push_preview.html.twig', 
            ['form' => $form->createView(),'user' => $user],
            $form 
        );


    }

}
