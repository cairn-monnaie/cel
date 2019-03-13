<?php                                                                          
// src/Cairn/UserBundle/Service/AccessPlatform.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Entity\User;
use Cairn\UserCyclosBundle\Entity\UserManager;

use Cairn\UserBundle\Service\Security;
use Cairn\UserBundle\Service\MessageNotificator;
use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserCyclosBundle\Service\NetworkInfo;

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
     *@var Security $security
     */
    protected $security;

    /**
     *@var UserRepository $userRepo
     */
    protected $userRepo;

    /**
     *@var NetworkInfo $networkInfo
     */
    protected $networkInfo;

    protected $anonymousUser;

    protected $network;

    public function __construct(UserRepository $userRepo, MessageNotificator $messageNotificator, Security $security, NetworkInfo $nI, $anonymous, $network)
    {
        $this->userRepo = $userRepo;
        $this->messageNotificator = $messageNotificator;
        $this->security = $security;
        $this->networkInfo = $nI;
        $this->anonymousUser = $anonymous;
        $this->network = $network;
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
    public function disable($users, $subject = NULL, $body = NULL)
    {
        if(!$subject){
            $subject = "Compte e-Cairn bloqué";
        }
        if(!$body){
            $body = "Votre compte e-Cairn est désormais bloqué";
        }

        $from = $this->messageNotificator->getNoReplyEmail();
        foreach($users as $user){
            if($user->isEnabled()){
                $this->messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);
                $user->setEnabled(false);

                $this->changeUserStatus($user, 'DISABLED');

                if($smsData = $user->getSmsData()){
                    $smsData->setSmsEnabled(false);
                }
            }
        }
    }

    /**
     *Enable users and send an email with subject $subject and content $body
     *
     *
     * Activating an user on Cyclos-side can be done ONLY by an admin, and it must be the case in this application
     *
     *@param text $body HTML to set at the content in the email
     *@param string $subject 
     *@param array $users 
     */
    public function enable($users, $subject = NULL, $body = NULL)
    {
        if(!$subject){
            $subject = "Votre espace membre Cairn a été activé";
        }
        if(!$body){
            $body = "Votre compte est désormais accessible";
        }

        $from = $this->messageNotificator->getNoReplyEmail();

        foreach($users as $user){
            if(!$user->isEnabled()){
                $this->messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);
                $user->setEnabled(true);
                $user->setPasswordTries(0);
                $user->setCardKeyTries(0);
                $user->setPhoneNumberActivationTries(0);
                $user->setCardAssociationTries(0);

                $this->changeUserStatus($user, 'ACTIVE');

            }

        }
    }

    /**
     *
     *Changes user status on Cyclos side to be coherent
     */
    public function changeUserStatus(User $user,$status)
    {
        $currentUser = $this->security->getCurrentUser();
        $userManager = new UserManager();


        if( !$currentUser  || !$currentUser->isAdmin()){
            //connect to cyclos with anonymous user
            $username = $this->anonymousUser;
            $credentials = array('username'=>$username,'password'=>$username);

            $network = $this->network;
            $this->networkInfo->switchToNetwork($network,'login',$credentials);
        }
        $params = new \stdClass();                                         
        $params->user = $user->getCyclosID();                              
        $params->status = $status;                                        
        $userManager->changeStatusUser($params);
    }

}
