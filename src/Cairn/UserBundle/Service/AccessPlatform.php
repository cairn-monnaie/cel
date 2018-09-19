<?php                                                                          
// src/Cairn/UserBundle/Service/AccessPlatform.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Service\MessageNotificator;
use Cairn\UserBundle\Repository\UserRepository;

/**
 * This class contains services related to the access to the platform.
 *
 */
class AccessPlatform
{
    /**
     * Service dealing with notifications(mailing/notifications)
     *@var MessageNotificator $messageNotificator
     */
    protected $messageNotificator;

    /**
     *@var UserRepository $userRepo
     */
    protected $userRepo;
    
    public function __construct(UserRepository $userRepo, MessageNotificator $messageNotificator)
    {
        $this->userRepo = $userRepo;
        $this->messageNotificator = $messageNotificator;
    }

    /**
     *Disables all users and send an email with content $body
     *
     *@param text $body HTML to set at the content in the email
     */
    public function shutDown($body)
    {
        if($body == NULL){
            $body = "Le site est en phase de maintenance. Veuillez nous excuser. /n Le Cairn, ";
        }
        $all = $this->userRepo->findAll();
        $this->disable($all,'La plateforme Cairn est en maintenance',$body);

    }

    /**
     *Enables all users and send an email with content $body
     *
     *@param text $body HTML to set at the content in the email
     */
    public function openAccess($body)
    {
        $allPros = $this->userRepo->myFindByRole(array('ROLE_PRO'));
        $this->enable($allPros,'La plateforme Cairn est accessible',$body);
    }

    /**
     *Disable users and send an email with subject $subject and content $body
     *
     *@param text $body HTML to set at the content in the email
     *@param string $subject 
     *@param array $users 
     */
    public function disable($users, $subject, $body)
    {
        $from = $this->messageNotificator->getNoReplyEmail();
        foreach($users as $user){
            $this->messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);
            $user->setEnabled(false);
        }
    }

    /**
     *Enable users and send an email with subject $subject and content $body
     *
     *@param text $body HTML to set at the content in the email
     *@param string $subject 
     *@param array $users 
     */
    public function enable($users)
    {
        $subject = "Votre espace membre Cairn a été activé";
        $body = "Votre compte est désormais accessible";
        $from = $this->messageNotificator->getNoReplyEmail();

        foreach($users as $user){
            $this->messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);
            $user->setEnabled(true);
            $user->setPasswordTries(0);
            $user->setCardKeyTries(0);
        }
    }

}
