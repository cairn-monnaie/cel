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
     * Service dealing with notifications(mailing/notifications/sms)
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

    /**
     *@var string $anonymousUser : username of anonymous user on Cyclos-side
     */
    protected $anonymousUser;

    /**
     *@var string $network : name of the cyclos network
     */
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
     *@param string $reason In one word, why the acocunt has been opposed
     *@param string $subject 
     *@param array $users 
     */
    public function disable($users, $reason = NULL, $subject = NULL)
    {
        $templating = $this->messageNotificator->getTemplatingService();

        if(!$subject){
            $subject = "Compte [e]-Cairn bloqué";
        }

        $from = $this->messageNotificator->getNoReplyEmail();
        foreach($users as $user){
            $body = $templating->render('CairnUserBundle:Emails:blocked_account.html.twig',
                 array('reason'=>$reason, 'user'=>$user)); 

            if($user->isEnabled()){
                $this->messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);
                $user->setEnabled(false);

                $this->changeUserStatus($user, 'DISABLED');

                $phones = $user->getPhones();
                foreach($phones as $phone){
                    $phone->setPaymentEnabled(false);
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
        $templating = $this->messageNotificator->getTemplatingService();

        if(!$subject){
            $subject = "Votre compte [e]-Cairn a été activé";
        } 

        $from = $this->messageNotificator->getNoReplyEmail();

        foreach($users as $user){
            if(! $body){
            $body = $templating->render('CairnUserBundle:Emails:opened_account.html.twig',
                array('user'=>$user)); 
            }

            if(!$user->isEnabled()){
                $this->messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);
                $user->setEnabled(true);
                $user->setRemovalRequest(false);
                $user->setPasswordTries(0);
                $user->setCardKeyTries(0);
                $user->setPhoneNumberActivationTries(0);
                $user->setCardAssociationTries(0);

                $this->changeUserStatus($user, 'ACTIVE');

            }
        }
    }

    /**
     * Changes user status on Cyclos side to be consistent with FOSUserBundle
     * 
     * A non admin user cannot change his status on Cyclos-side. Therefore, if an adherent opposes his app member area, the operation
     * cannot be extended to Cyclos. Therefore, we use an anonymous user whom only purpose is to do this for us. If user is admin, he can
     * do it
     * Therefore, in the end, if an adherent is blocked on Symfony-side, he is also blocked on Cyclos-side, so that he is not visible in 
     * Cyclos anymore
     * @param User $user user to change status to
     * @param string $status BLOCKED | ACTIVE
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
