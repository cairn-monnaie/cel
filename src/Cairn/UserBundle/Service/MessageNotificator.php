<?php
// src/Cairn/UserBundle/Service/MessageNotificator.php

namespace Cairn\UserBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Cairn\UserBundle\Repository\UserRepository;

/**
 * This class contains services related to the notifications/mailing.
 *
 */
class MessageNotificator
{
    /**
     *@var UserRepository $userRepo
     */
    protected $userRepo;

     /**
     * Service dealing with emails
     *@var \Swift_Mailer $mailer
     */
   protected $mailer;

    /**
     *email address defined in global parameter 
     *@var string $technicalServices
     */
    protected $technicalServices;

    /**
     * email address defined in global parameter
     *@var string $noreply
     */
    protected $noreply;

    public function __construct(UserRepository $userRepo, \Swift_Mailer $mailer, $technicalServices, $noreply)
    {
        $this->userRepo = $userRepo;
        $this->mailer = $mailer;
        $this->technicalServices = $technicalServices;
        $this->noreply = $noreply;
    }

    //TODO : to change when SMS API avaialble
    public function sendSMS($phoneNumber, $content)
    {
        $email = 'whoknows@test.com';
        $this->notifyByEmail('SMS',$this->getNoReplyEmail(), $email, $content);
    }

    public function getNoReplyEmail()
    {
        return $this->noreply;
    }

    public function getMaintenanceEmail()
    {
        return $this->technicalServices;
    }

    /**
     *Send emails to all users with roles $roles
     *
     *@param array $roles
     *@param string $subject
     *@param string $from email adress of the sender
     *@param text $body HTML text
     */
    public function notifyRolesByEmail($roles, $subject,$from,$body)
    {
        $users = $this->userRepo->myFindByRole($roles);
        foreach($users as $user){
            $to = $user->getEmail();
            $this->notifyByEmail($subject,$from,$to,$body);
        }
    }

    /**
     *Send email
     *
     *@param string $subject
     *@param string $from email adress of the sender
     *@param string $to email adress of the receiver
     *@param text $body HTML text content
     */
    public function notifyByEmail($subject,$from,$to,$body)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body,'text/html');
        $this->mailer->send($message);
    }
}

