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

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if(!$entity instanceof Operation){
            return;
        }

        $entityManager = $args->getEntityManager();

        if($entity->getType() == Operation::$TYPE_TRANSACTION_SCHEDULED){
            $interval = $entity->getExecutionDate()->diff($entity->getUpdatedAt());                                        
            if($interval->invert == 0){
                $scheduledPaymentVO = $this->bridgeToSymfony->fromSymfonyToCyclosOperation($entity);
                if($scheduledPaymentVO->installments[0]->status == 'FAILED'){
                    $entity->setType(Operation::$TYPE_SCHEDULED_FAILED);
                }elseif($scheduledPaymentVO->installments[0]->status == 'PROCESSED'){
                    $entity->setType(Operation::$TYPE_TRANSACTION_EXECUTED);
                }

                $entityManager->flush();
            }
        }

    }

}
