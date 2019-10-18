<?php
// src/Cairn/UserBundle/Controller/AccountScoreController.php

namespace Cairn\UserBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\AccountScore;

//manage Events 
use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

//manage Forms
use Cairn\UserBundle\Form\AccountScoreType;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\SubmitType;                     
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * This class contains actions related to account scores 
 *
 * @Security("is_granted('ROLE_PRO')")
 */
class AccountScoreController extends Controller
{   
    /**
     * A pro can download a daily account score 
     *
     * @Security("has_role('ROLE_PRO')")
     */
    public function configureAccountScoreAction(Request $request, User $user)
    {
        $currentUser = $this->getUser();

        //to see the content, check that currentUser is owner or currentUser is referent
        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        if(! $user->hasRole('ROLE_PRO')){
            throw new AccessDeniedException('Concerne les professionnels uniquement');
        }

        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        
        $accountScore = $em->getRepository('CairnUserBundle:AccountScore')->findOneByUser($user);
        if(! $accountScore){
            $accountScore = new AccountScore();
            $accountScore->setUser($user);

            //little hack to force preUpdate entity listener event to show up even if this is a new entity
            //otherwise, we cannot use the preUpdate event dispatchment and generate a confirmation token if 
            //the email is different from the user email
            $em->persist($accountScore);
            $em->flush();
        }

        $form = $this->createForm(AccountScoreType::class, $accountScore);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $schedule = $accountScore->getSchedule();

            //reorder schedules ASC for each day 
            foreach($schedule as $day => $daySchedule){
                usort($daySchedule, array($this,'compareDates'));
                $schedule[$day] = $daySchedule;
            }
            
            $accountScore->setSchedule($schedule);

            // if today schedule has changed, we must simulate that previous emails requested today have been sent
            // to keep consistent data
            $cmpt = 0;
            $now = new \Datetime();
            $nowTime = $now->format('H:i');
            $nowDay = $now->format('D');

            $nbTotalTimes = count( $schedule[$nowDay] );
            while ( ($cmpt < $nbTotalTimes) && ($schedule[$nowDay][$cmpt] < $nowTime) ){
                $cmpt++;
            }

            $accountScore->setNbSentToday($cmpt);
            $em->persist($accountScore);
            $em->flush();

            if($accountScore->getConfirmationToken()){
                return $this->render('CairnUserBundle:AccountScore:check_email.html.twig',
                    array('accountScore'=>$accountScore));
            }else{
                $session->getFlashBag()->add('success','Configuration mise à jour avec succès !');
                return $this->redirectToRoute('cairn_user_accountscore_view',array('id'=>$accountScore->getID())); 
            }
            
        }

        return $this->render('CairnUserBundle:AccountScore:_form.html.twig',
            array('form'=>$form->createView()));
    }

    private function compareDates($a, $b)
    {
        if($a == $b){
            return 0;
        }

        return ($a < $b) ? -1 : 1;
        
    }

    /**
     *
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function confirmAccountScoreEmailAction(Request $request, string $token){

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        $accountScore = $em->getRepository('CairnUserBundle:AccountScore')->findOneByConfirmationToken($token);

        if($accountScore){
            $accountScore->setConfirmationToken(null);
            $em->flush();
            $session->getFlashBag()->add('success','Votre adresse électronique a été confirmée avec succès !');
            return $this->redirectToRoute('cairn_user_accountscore_view',array('id'=>$accountScore->getID())); 
        }else{
            return $this->redirectToRoute('fos_user_security_login');
        }
    }


    /**
     * A pro can view his account score configuration 
     *
     * @Security("has_role('ROLE_PRO')")
     */
    public function viewAccountScoreAction(Request $request, AccountScore $accountScore)
    {
        return $this->render('CairnUserBundle:AccountScore:view.html.twig',
            array('accountScore'=>$accountScore));
    }


}
