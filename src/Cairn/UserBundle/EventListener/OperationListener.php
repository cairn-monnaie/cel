<?php
// src/Cairn/UserBundle/EventListener/OperationListener.php

namespace Cairn\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserCyclosBundle\Service\BankingInfo;
use Cairn\UserBundle\Service\BridgeToSymfony;
use Cairn\UserBundle\Service\MessageNotificator;
use Symfony\Bundle\TwigBundle\TwigEngine;

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

    protected $messageNotificator;

    protected $templating;

    public function __construct(BankingInfo $cyclosBankingInfo, BridgeToSymfony $bridgeToSymfony, MessageNotificator $messageNotificator, TwigEngine $templating)
    {
        $this->cyclosBankingInfo = $cyclosBankingInfo;
        $this->bridgeToSymfony = $bridgeToSymfony;
        $this->messageNotificator = $messageNotificator;
        $this->templating = $templating;
    }

    /**
     * Deals with asynchronous operations made in Cyclos in order to update our database
     *
     * If a transaction has been scheduled in the future and has finally been executed, the operation type must be edited from
     * SCHEDULED to EXECUTED. If the future transaction has failed, type becomes FAILED
     *
     *@param Operation $operation 
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

                    $body = $this->templating->render('CairnUserBundle:Emails:failed_transaction.html.twig',
                        array('operation'=>$operation));

                    $subject = 'Echec de votre virement programmé';
                    $from = $this->messageNotificator->getNoReplyEmail();
                    $to = $operation->getDebitor()->getEmail();
                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);      

                }elseif($scheduledPaymentVO->installments[0]->status == 'PROCESSED'){
                    $operation->setType(Operation::TYPE_TRANSACTION_EXECUTED);
                }

                $entityManager->flush();
            }
        }
    }

}
