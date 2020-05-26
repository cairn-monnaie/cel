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
            if(! isset($jsonRequest['action'])){
                return $apiService->getErrorResponse(array('Action field not found'),Response::HTTP_BAD_REQUEST);
            }

            $deviceToken = $jsonRequest['device_token'];
            $platform = $jsonRequest['platform'];
            $action = strtoupper($jsonRequest['action']);

            if($action == 'POST'){
                $notificationData->addDeviceToken($deviceToken,$platform);
                $em->flush();
                return $apiService->getOkResponse($notificationData,Response::HTTP_CREATED);
            }elseif($action == 'DELETE'){
                $notificationData->removeDeviceToken($deviceToken,$platform);
                $em->flush();
                return $apiService->getOkResponse($notificationData,Response::HTTP_OK);
            }else{
                return $apiService->getErrorResponse(array('Action field must be either DELETE or POST'),Response::HTTP_BAD_REQUEST);
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
    public function sendProPushNotificationAction(Request $request,User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $messageNotificator = $this->get('cairn_user.message_notificator');

        if(! $user->hasRole('ROLE_PRO')){
             $session->getFlashBag()->add('error',$user->getName().' n est pas un professionnel');
            return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
         }

        $pushTemplate = new PushTemplate();
        $url = ($user->getUrl()) ? $user->getUrl() : 'https://'; 
        $pushTemplate->setRedirectionUrl($url);

        $form = $this->createForm(PushTemplateType::class,$pushTemplate);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('save')->isClicked()){
                $messageNotificator->sendRegisterNotifications($user, $pushTemplate);
                $session->getFlashBag()->add('success','Push message has been sent');

                $em->flush();
            }else{
                $session->getFlashBag()->add('info','Push message has been canceled');
            }
            return $this->redirectToRoute('cairn_user_profile_view',array('username' => $user->getUsername()));
        }

        return $this->render('CairnUserBundle:Notification:push_preview.html.twig',array('form' => $form->createView(),'user'=>$user));

    }

}
