<?php

// tests/UserBundle/Command/RemoveAbortedOperationsCommandTest.php
namespace Tests\UserBundle\Command;

use Cairn\UserBundle\Command\RemoveAbortedOperationsCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\StringInput;

use Cairn\UserBundle\Entity\Sms;

use Cyclos;

class RemoveAbortedOperationsCommandTest extends KernelTestCase
{

    /**
     *
     * Tests that all aborted operations have been removed
     * If payment is SMS payment, then STATE is WAITING_KEY
     * If payment is anything else, PaymentID is NULL 
     */
    public function testRemoveAbortedOperationsCommand()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $application = new Application($kernel);
        $application->add(new RemoveAbortedOperationsCommand());

        //assert the database content BEFORE command execution
        $abortedOperations = $operationRepo->findBy(array('paymentID'=>NULL));
        $this->assertTrue(count($abortedOperations) != 0);

        $command = $application->find('cairn.user:remove-aborted-operations');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName()
        ));

        // assert the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('updated successfully', $output);

        //assert the database content AFTER command execution
        $abortedOperations = $operationRepo->findBy(array('paymentID'=>NULL));
        $this->assertTrue(count($abortedOperations) == 0);

    }

}
