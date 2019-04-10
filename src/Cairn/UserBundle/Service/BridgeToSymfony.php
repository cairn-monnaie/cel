<?php                                                                          
// src/Cairn/UserBundle/Service/BridgeToSymfony.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserCyclosBundle\Service\UserInfo;

use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Repository\OperationRepository;
use Cairn\UserCyclosBundle\Service\BankingInfo;

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
     * Service dealing with notifications(mailing/notifications/sms)
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

    /**
     * Class used to retrieve operation's entities on Symfony side
     *@var OperationRepository $operationRepo
     */
    protected $operationRepo;

    /**
     * Class used to retrieve operation's data on Cyclos side
     *@var Cairn\UserCyclosBundle\Service\BankingInfo $cyclosBankingInfo
     */ 
    protected $cyclosBankingInfo;


    public function __construct(MessageNotificator $messageNotificator,UserRepository $userRepo,  UserInfo $cyclosUserInfo, 
                                OperationRepository $operationRepo, BankingInfo $cyclosBankingInfo)
    {
        $this->messageNotificator = $messageNotificator;
        $this->userRepo = $userRepo;
        $this->cyclosUserInfo = $cyclosUserInfo;
        $this->operationRepo = $operationRepo;
        $this->cyclosBankingInfo = $cyclosBankingInfo;

    }

    /**
     * Retrieves Cyclos user from Symfony
     *
     *@param User $user Symfony user to retrieve cyclos equivalent
     *@throws Cyclos/ServiceException if no cyclos user matches
     *@return stdClass $cyclosUser
     */
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
                $body = 'Entité : User. Equivalent Cyclos inexistant. ID Symfony valide '.$user->getID(). ' correspondant au membre de login ' .$user->getUsername();
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);

            }else{
                throw $e;
            }
        }
    }

    /**
     * Retrieves Symfony user from Cyclos
     *
     *@param int $id Cyclos ID of an user
     *@return User $symfonyUser User equivalent entity
     */
    public function fromCyclosToSymfonyUser($id)
    {
        $symfonyUser = $this->userRepo->findOneBy(array('cyclosID'=>$id));
        if(!$symfonyUser){
            //if no cyclos user matches $id, exception is thrown
            $cyclosUser = $this->cyclosUserInfo->getUserVO($id);
            $from = $this->messageNotificator->getNoReplyEmail();
            $to = $this->messageNotificator->getMaintenanceEmail();


            $subject = "Dissociation des bases de données Symfony-Cyclos";
            $body = 'Entité Doctrine inexistante. cyclosID valide '.$id. ' correspondant au membre de login ' .$cyclosUser->shortDisplay.' ayant un rôle '.$cyclosUser->role.
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
        }
        return $symfonyUser;
    }

    /**
     * Retrieves Cyclos user from Symfony
     *
     *@param Operation $operation Symfony operation to retrieve cyclos equivalent transaction
     *@throws Cyclos/ServiceException if no cyclos transfer matches
     *@return stdClass $cyclosOperation corresponding to org.cyclos.model.banking.transactions.TransactionVO
     */
    public function fromSymfonyToCyclosOperation(Operation $operation)
    {
         try{
            $cyclosOperation = $this->cyclosBankingInfo->getTransactionDataByID($operation->getPaymentID());
            return $cyclosOperation->transaction;
        }catch(\Exception $e){
            if($e->errorCode == 'ENTITY_NOT_FOUND'){
                $from = $this->messageNotificator->getNoReplyEmail();
                $to = $this->messageNotificator->getMaintenanceEmail();

                $subject = "Dissociation des bases de données Symfony-Cyclos";
                $body = 'Entité : Operation. Equivalent Cyclos inexistant. ID Symfony valide '.$operation->getID();
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
            }

            throw $e;
        }
       
    }

    /**
     * Retrieves Symfony operation (transfer of units from account to account) from Cyclos
     *
     *@param int $id Cyclos ID of a transaction
     *@return Operation $symfonyOperation Operation equivalent entity
     */
    public function fromCyclosToSymfonyOperation($id)
    {
        $symfonyOperation = $this->operationRepo->findOneBy(array('paymentID'=>$id));
        if(!$symfonyOperation){
            //if no cyclos operation matches $id, exception is thrown
            $cyclosOperation = $this->cyclosBankingInfo->getTransactionDataByID($id);

            $from = $this->messageNotificator->getNoReplyEmail();
            $to = $this->messageNotificator->getMaintenanceEmail();

            $subject = "Dissociation des bases de données Symfony-Cyclos";
            $body = 'Entité Doctrine inexistante. cyclosID valide '.$id. ' correspondant à un paiement de classe '.$cyclosOperation->transaction->class .' réalisé par : '.$cyclosOperation->transaction->fromOwner->name.' vers '.$cyclosOperation->transaction->toOwner->name. ' de description '.$cyclosOperation->transaction->description ; 
            $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
        }

        return $symfonyOperation;

    }

}
