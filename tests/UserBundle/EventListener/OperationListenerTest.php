<?php
//src/Cairn/Tests/UserBundle/EventListener/OperationListenerTest.php

namespace Tests\UserBundle\EventListener;

use Doctrine\ORM\Events;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

use Cairn\UserBundle\Entity\Operation;

class OperationListenerTest extends TestCase
{

    public function testUpdateOperation()
    {
        //get paymentVO 
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->getMock();
        $paymentVO = $container->get('cairn_user_cyclos_banking_info')->getTransactions();

        //create a new Operation from it
        $executionDate = new \Datetime($paymentVO->date);
        $operation = new Operation();
        $operation->setType(Operation::$TYPE_TRANSACTION_SCHEDULED);              
        $operation->setPaymentID($paymentVO->id);
        $operation->setExecutionDate($executionDate);    
        $operation->setSubmissionDate(date_modify($executionDate,'-1 days'));    
        $operation->setAmount($paymentVO->dueAmount->amount);    
        $operation->setDescription($paymentVO->description);    
        $operation->setReason('Test paiement futur');    
        $operation->setFromAccountNumber();    
        $operation->setToAccountNumber();    
        $operation->setUpdatedAt(new \Datetime());
        $operation->setUser($toUser);

        //mock the repository
        $operationRepo = $this->getMockBuilder('Cairn\UserBundle\Repository\OperationRepository')->getMock();
        $operationRepo->expects($this->any())
                        ->method('find')
                        ->willReturn($operation);
        
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects($this->any())
                        ->method('getRepository')
                        ->willReturn($operationRepo);

        $event = new LifecycleEventArgs($operation,$objectManager); 
        $eventDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->getMock();
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $eventDispatcher->dispatch(Events::postLoad,$event); 

        //test how the operation has changed
    }
}
