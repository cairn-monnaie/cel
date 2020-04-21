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
            $deviceToken = $jsonRequest['device_token'];

            if($request->isMethod('POST')){
                $notificationData->addDeviceToken($deviceToken);

                $em->flush();
                return $apiService->getOkResponse($notificationData,Response::HTTP_CREATED);
            }else{
                $notificationData->removeDeviceToken($jsonRequest['device_token']);
                $em->flush();
                return $apiService->getOkResponse($notificationData,Response::HTTP_CREATED);
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

             $pushSubscription = new WebPushSubscription($subscription['endpoint'],$subscription['keys']);
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

    public function editNotificationParamsAction(Request $request, User $user)
    {
        $currentUser = $this->getUser();

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

                return $apiService->getOkResponse($notificationData,Response::HTTP_CREATED);
            }else{
                return $apiService->getFormErrorResponse($form);
            }
        }

        return $this->render('CairnUserBundle:Notification:_form.html.twig',array('form' => $form->createView(),'user'=>$user));
    }


    public function configNotificationsAction(Request $request, User $user)
    {

    }

}
