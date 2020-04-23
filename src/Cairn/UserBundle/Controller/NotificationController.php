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

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

//manage Forms
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\NotificationDataType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\LogicException;

/**
 * This class contains actions related to account operations 
 *
 * @Security("is_granted('ROLE_ADHERENT')")
 */
class NotificationController extends Controller
{   

    public function tokenSubscriptionAction(Request $request, $from)
    {
        $em = $this->getDoctrine()->getManager();
        $apiService = $this->get('cairn_user.api');

        $currentUser = $this->getUser();

        $notificationData = $this->initNotificationData($currentUser);

        $jsonRequest = json_decode($request->getContent(), true);

        if($from == 'mobile'){
            if(! isset($jsonRequest['device_token'])){
                return $apiService->getErrorResponse(array('Body field device_token not found'),Response::HTTP_BAD_REQUEST);
            }
            if(! isset($jsonRequest['platform'])){
                return $apiService->getErrorResponse(array('Body field platform not found'),Response::HTTP_BAD_REQUEST);
            }

            $deviceToken = $jsonRequest['device_token'];
            $platform = $jsonRequest['platform'];

            if($request->isMethod('POST')){
                $notificationData->addDeviceToken($deviceToken,$platform);

                $em->flush();
                return $apiService->getOkResponse($notificationData,Response::HTTP_CREATED);
            }else{
                $notificationData->removeDeviceToken($deviceToken,$platform);
                $em->flush();
                return $apiService->getOkResponse($notificationData,Response::HTTP_OK);
            }
        }else{
            $subscription = $jsonRequest['subscription'];

             //validate endpoint exists
             if(! array_key_exists('endpoint',$subscription)){
                 return $apiService->getErrorResponse(array('Subscription must have an endpoint'),Response::HTTP_BAD_REQUEST);
             }

             //validate keys because we need payload support
             if(! array_key_exists('keys',$subscription)){
                 return $apiService->getErrorResponse(array('Subscription must have encryption keys'),Response::HTTP_BAD_REQUEST);
             }else{
                 if( (! array_key_exists('p256dh',$subscription['keys'])) || (! array_key_exists('auth',$subscription['keys']))){
                     return $apiService->getErrorResponse(array('Subscription must have valid encryption keys'),Response::HTTP_BAD_REQUEST);
                 }
             }

             //TODO : DEAL WITH MACOS ENDPOINTS
             $isMacOSEndpoint = false;
             $pushSubscription = new WebPushSubscription($subscription['endpoint'],$subscription['keys'],$isMacOSEndpoint );
             $pushSubscription->setNotificationData($notificationData);
             $notificationData->addWebPushSubscription($pushSubscription);


             //ADD MOZILLA, CHROME, ... TO THE MESSAGE
             $endpoint = $subscription['endpoint'];
             $navigator = '';

             if(strpos($endpoint,'mozilla') !== false){
                $navigator = 'Mozilla Firefox';
             }elseif(strpos($endpoint,'googleapis') !== false){
                $navigator = '(Chrome ou Opera)';
             }
             

             $data = array(
                'title'=>'Notifications [e]-Cairn',
                'body'=>'Ce navigateur '.$navigator.' est désormais enregistré comme destinataire des notifications',
                'payload'=>['tag'=>'subscription']
             );
            $this->get('cairn_user.message_notificator')->sendWebPushNotifications(array($pushSubscription),$data,'subscription',0,'normal');

             $em->flush();

             
             return $apiService->getOkResponse(array('OK'),Response::HTTP_CREATED);


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

        $this->get('cairn_user.message_notificator')->sendRegisterNotifications($currentUser);

        $em = $this->getDoctrine()->getManager();
        $apiService = $this->get('cairn_user.api');
        $isRemoteCall = $apiService->isRemoteCall();

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
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

                if($isRemoteCall){
                    return $apiService->getOkResponse($notificationData,Response::HTTP_CREATED);        
                }else{
                    $request->getSession()->getFlashBag()->add('success','Les paramètres des notifications ont été mis à jour');
                    return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
                }
            }else{
                return $apiService->getFormErrorResponse($form);
            }
        }else{
            if($isRemoteCall){
                return $apiService->getOkResponse($notificationData,Response::HTTP_OK);        
            }
        }

        return $this->render('CairnUserBundle:Notification:_form.html.twig',array('form' => $form->createView(),'user'=>$user));
    }

    /**
     * Send Push Notification regarding user as @param
     *
     * @param  User $user  Must have role ROLE_PRO
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function sendProPushAction(Request $request,User $user)
    {
        $session = $request->getSession();
        $messageNotificator = $this->get('cairn_user.message_notificator');

        if(! $user->hasRole('ROLE_PRO')){
             $session->getFlashBag()->add('error',$user->getName().' n est pas un professionnel');
            return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
         }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){
                $messageNotificator->sendRegisterNotifications($user);
            }else{//push not sent, redirect to profile
                return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
            }
        }
    }

}
