<?php                                                                          
// src/Cairn/UserBundle/Service/BridgeToSymfony.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Entity\User;
/**
 * This class is a bridge between Symfony entities and their equivalences in other databases to ensure consistency
 *
 * It may happen that Symfony is coupled with other third-party applications to build yours. These applications may have their own 
 * databases, so that data redundancy occurs between Symfony database and those third-party applications' databases. This class, defined
 * as a service, is used as a bridge between Symfony db and others db to retrieve data and ensure consistency between them.
 */
class BridgeToSymfony
{
    /**
     * Service dealing with notifications(mailing/notifications)
     *@var MessageNotificator $messageNotificator
     */
    protected $messageNotificator;

    /**
     * Class used to retrieve user's entities on Symfony side
     *@var UserRepository $userRepo
     */
    protected $userRepo;

    /**
     * Class used to retrieve user's data on Cyclos side
     *@var Cairn\UserCyclosBundle\Service\UserInfo $cyclosUserInfo
     */ 
    protected $cyclosUserInfo;

    public function __construct(UserRepository $userRepo, MessageNotificator $messageNotificator, UserInfo $cyclosUserInfo)
    {
        $this->userRepo = $userRepo;
        $this->messageNotificator = $messageNotificator;
        $this->cyclosUserInfo = $cyclosUserInfo;
    }

    public function fromSymfonyToCyclosUser(User $user)
    {
        try{
            $cyclosUser = $this->cyclosUserInfo->getUserVO($user->getCyclosID());
            return $cyclosUser;
        }catch(\Exception $e){
            if($e->errorCode == 'ENTITY_NOT_FOUND'){
                $from = $this->messageNotificator->getNoReplyEmail();
                $to = $this->messageNotificator->getMaintenanceEmail();

                $subject = "Dissociation des bases de données Symfony-Cyclos";
                $body = 'Entité Cyclos inexistante. ID Symfony valide '.$user->getID(). ' correspondant au membre de login ' .$user->getUsername();
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);

            }else{
                throw $e;
            }
        }
    }

    public function fromCyclosToSymfonyUser($id)
    {
        $symfonyUser = $this->userRepo->findOneBy(array('cyclosID'=>$id));
        if(!$symfonyUser){
            $cyclosUser = $this->cyclosUserInfo->getUserVO($id);
            $from = $this->messageNotificator->getNoReplyEmail();
            $to = $this->messageNotificator->getMaintenanceEmail();


            $subject = "Dissociation des bases de données Symfony-Cyclos";
            $body = 'Entité Doctrine inexistante. cyclosID valide '.$id. ' correspondant au membre de login ' .$cyclosUser->shortDisplay.' ayant un rôle '.$cyclosUser->role.
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
        }
    }

    public function fromSymfonyToCyclosReconversion()
    {

    }

    public function fromSymfonyToCyclosReconversion()
    {

    }
}
