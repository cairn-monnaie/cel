<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cairn\UserBundle\Controller;


use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Form\ProfileType as CairnProfileType;

#use FOS\UserBundle\Controller\ProfileController as BaseController;

/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends Controller
{

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request, User $user)
    {
        $eventDispatcher = $this->get('event_dispatcher');
        $formFactory = $this->get('fos_user.profile.form.factory');
        $userManager = $this->get('fos_user.user_manager');

        $currentUser = $this->getUser();
        if (!is_object($currentUser) || !$currentUser instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        $event = new GetResponseUserEvent($user, $request);
        $eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

       // $form = $formFactory->createForm();
        $form = $this->createForm(CairnProfileType::class, $user);

        //$form->setData($user);


        $apiService = $this->get('cairn_user.api');

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isValid()){

                $event = new FormEvent($form, $request);
                $eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

//                $userManager->updateUser($user);
                //if($form->get('initialize_parameters')->getData()){
                //    $user->setNbPhoneNumberRequests(0);
                //    $user->setPhoneNumberActivationTries(0);
                //    $user->setPasswordTries(0);
                //    $user->setCardKeyTries(0);
                //    $user->setCardAssociationTries(0); 
                //}


                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_profile_show');
                    $response = new RedirectResponse($url);
                }

                $eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }//else{
             //   if( $apiService->isRemoteCall()){
             //       return $apiService->getFormErrorResponse($form);
             //   }
             //}
        }

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
