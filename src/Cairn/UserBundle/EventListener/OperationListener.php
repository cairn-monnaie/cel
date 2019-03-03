<?php
// src/Cairn/UserBundle/EventListener/OperationListener.php

namespace Cairn\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserCyclosBundle\Service\BankingInfo;
use Cairn\UserBundle\Service\BridgeToSymfony;

/**
 * This class is used to synchronize operation entities with the Cyclos database 
 *
 */
class OperationListener
{

    /**
     * Class used to retrieve operation's data on Cyclos side
     *@var Cairn\UserCyclosBundle\Service\BankingInfo $cyclosBankingInfo
     */ 
    protected $cyclosBankingInfo;

    protected $bridgeToSymfony;

    public function __construct(BankingInfo $cyclosBankingInfo, BridgeToSymfony $bridgeToSymfony)
    {
        $this->cyclosBankingInfo = $cyclosBankingInfo;
        $this->bridgeToSymfony = $bridgeToSymfony;
    }

    /**
     * Deals with asynchronous operations made in Cyclos in order to update our database
     *
     * If a transaction has been scheduled in the future and has finally been executed, the operation type must be edit from
     * SCHEDULED to EXECUTED. If the future transaction has failed, type becomes FAILED
     *
     */
    public function postLoad(Operation $operation, LifecycleEventArgs $args)
    {
        $entityManager = $args->getEntityManager();

        if($operation->getType() == Operation::TYPE_TRANSACTION_SCHEDULED){
            $operation->setUpdatedAt(new \Datetime());

            $interval = $operation->getExecutionDate()->diff($operation->getUpdatedAt());                                        
            if($interval->invert == 0){
                $scheduledPaymentVO = $this->bridgeToSymfony->fromSymfonyToCyclosOperation($operation);
                if($scheduledPaymentVO->installments[0]->status == 'FAILED'){
                    $operation->setType(Operation::TYPE_SCHEDULED_FAILED);
                }elseif($scheduledPaymentVO->installments[0]->status == 'PROCESSED'){
                    $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);
                }

                $entityManager->flush();
            }
        }
    }

}
