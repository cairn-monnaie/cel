<?php
// src/Cairn/UserBundle/EventListener/AccountScoreListener.php

namespace Cairn\UserBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Cairn\UserBundle\Entity\AccountScore;
use Cairn\UserBundle\Service\Security;
use Cairn\UserBundle\Service\MessageNotificator;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This class is used to generate a confirmation token if email has changed 
 *
 */
class AccountScoreListener
{

    protected $securityService;
    protected $messageNotificator;
    protected $router;
    protected $templating;

    public function __construct(Security $securityService, MessageNotificator $messageNotificator, Router $router, TwigEngine $templating)
    {
        $this->securityService = $securityService;
        $this->messageNotificator = $messageNotificator;
        $this->router = $router;
        $this->templating = $templating;
    }

    /**
     *@param AccountScore $accountScore 
     */
    public function preUpdate(AccountScore $accountScore, PreUpdateEventArgs $eventArgs)
    {
        if ($accountScore instanceof AccountScore) {
            if ($eventArgs->hasChangedField('email') ) {
                if( $eventArgs->getNewValue('email') != $accountScore->getUser()->getEmail()){
                    $confirmationToken = $this->securityService->generateUrlToken();

                    $accountScore->setConfirmationToken($confirmationToken);

                    $url = $this->router->generate('cairn_user_accountscore_confirm', array('token' => $accountScore->getConfirmationToken() ), UrlGeneratorInterface::ABSOLUTE_URL);
                    $body = $this->templating->render('CairnUserBundle:Emails:confirm_account_score.html.twig', array(
                        'accountScore' => $accountScore,
                        'confirmationUrl' => $url,
                    ));

                    $this->messageNotificator->notifyByEmail('Adresse mail pointage', $this->messageNotificator->getNoReplyEmail(),$eventArgs->getNewValue('email'), $body);

                }else{
                    $accountScore->setConfirmationToken(null);
                }

            }
        }

    }

}
