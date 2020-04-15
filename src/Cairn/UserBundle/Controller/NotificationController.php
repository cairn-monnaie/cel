<?php
// src/Cairn/UserBundle/Controller/NotificationController.php

namespace Cairn\UserBundle\Controller;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\PushNotification;
use Cairn\UserBundle\Entity\PaymentPushNotification;
use Cairn\UserBundle\Entity\RegistrationPushNotification;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

//manage Forms
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\PaymentPushNotificationType;
use Cairn\UserBundle\Form\RegistrationPushNotificationType;


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

    public function registerTokenAction(Request $request)
    {
        if($request->isMethod('POST')){ 
            $currentUser = $this->getUser();

            $em = $this->getDoctrine()->getManager();
            $apiService = $this->get('cairn_user.api');

            $jsonRequest = json_decode($request->getContent(), true);

            $pRepo = $em->getRepository('CairnUserBundle:PushNotification');
            $pushNotif = $pRepo->findByTokenAndKeyword($jsonRequest['device_token'],$jsonRequest['keyword'],$currentUser->getAppData());

            if($jsonRequest['keyword'] == PushNotification::KEYWORD_PAYMENT){
                $form = $this->createForm(PaymentPushNotificationType::class,$pushNotif);
            }elseif($jsonRequest['keyword'] == PushNotification::KEYWORD_REGISTER){
                $form = $this->createForm(RegistrationPushNotificationType::class,$pushNotif);
            }else{
                return $apiService->getErrorResponse(array('invalid_push_keyword'),Response::HTTP_BAD_REQUEST);
            }

            $form->submit($jsonRequest);
            if($form->isValid()){
                $newPushNotif = $form->getData();

                $appData = $currentUser->getAppData();
                $newPushNotif->setAppData($appData);

                $em->persist($newPushNotif);
                $em->flush();

                return $apiService->getOkResponse($newPushNotif,Response::HTTP_CREATED);
            }else{
                return $apiService->getFormErrorResponse($form);
            }
        }

        throw new NotFoundHttpException('POST Method required !');

    }

    public function unregisterTokenAction(Request $request)
    {
        if($request->isMethod('DELETE')){ 
            $currentUser = $this->getUser();

            $em = $this->getDoctrine()->getManager();
            $apiService = $this->get('cairn_user.api');

            $jsonRequest = json_decode($request->getContent(), true);

            $pRepo = $em->getRepository('CairnUserBundle:PushNotification');

            $pushNotif = $pRepo->findByTokenAndKeyword($jsonRequest['device_token'],$jsonRequest['keyword'],$currentUser->getAppData());

            if($pushNotif){
                $em->remove($pushNotif);
                $em->flush();

                return $apiService->getOkResponse('OK',Response::HTTP_OK);
            }

            return $apiService->getErrorResponse(array('push_registration_not_found'),Response::HTTP_BAD_REQUEST);
        }

        throw new NotFoundHttpException('DELETE Method required !');
    }

    public function configNotificationsAction(Request $request, User $user)
    {

    }

}
