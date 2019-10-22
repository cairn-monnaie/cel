<?php

// tests/UserBundle/Command/UpdateMandatesStatusCommandTest.php
namespace Tests\UserBundle\Command;

use Cairn\UserBundle\Command\UpdateMandatesStatusCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\StringInput;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Cairn\UserBundle\Entity\Mandate;
use Cairn\UserBundle\Entity\Operation;

use Cyclos;

class UpdateMandatesStatusCommandTest extends KernelTestCase
{

    /**
     *
     *@dataProvider provideDataForUpdate
     */
    public function testUpdateMandatesStatusCommand($beforeToday, $afterToday,$createdAt, $status, $nbOperations, $expectStatus)
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $MandateRepo = $em->getRepository('CairnUserBundle:Mandate');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $mandate = new Mandate();
        $mandate->setStatus($status);
        $mandate->setContractor($userRepo->findOneByUsername('lib_colibri'));
        $mandate->setAmount(20);

        //create fake mandate document doc
         $absoluteWebDir = $container->getParameter('kernel.project_dir').'/web/';
         $originalName = 'poster_sms.pdf';
         $absolutePath = $absoluteWebDir.$originalName;

         $file = new UploadedFile($absolutePath,$originalName,null,null,null, true);

         $document = new File();
         $document->setUrl($file->guessExtension());
         $document->setAlt($file->getClientOriginalName());

         $mandate->addMandateDocument($document);
         $document->setMandate($mandate);

        $today = new \Datetime();

        $clone = clone $today;
        $before = $clone->modify($beforeToday);

        if($before->format('d') >= 25){
            $before->modify('first day of next month');
        }

        $clone = clone $today;
        $after = $clone->modify($afterToday);

        if($after->format('d') >= 25){
            $after->modify('first day of next month');
        }

        $clone = clone $today;

        $mandate->setBeginAt($before);
        $mandate->setEndAt($after);
        $mandate->setCreatedAt(date_modify($clone,$createdAt));

        for($i = 0; $i < $nbOperations; $i++){
            $operation = new Operation();
            $operation->setType(Operation::TYPE_MANDATE);
            $operation->setPaymentID($i);
            $operation->setAmount(20);
            $operation->setReason('Mandat');
            $operation->setDescription('Test');
            $operation->setFromAccountNumber('1234');
            $operation->setToAccountNumber('4321');
            $operation->setCreditorName('John Doe');
            $operation->setDebitorName('Doe John');

            $mandate->addOperation($operation);

        }

        $em->persist($mandate);

        $application = new Application($kernel);
        $application->add(new UpdateMandatesStatusCommand());

        $em->flush();

        $command = $application->find('cairn.user:update-mandates-status');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName()
        ));

        // assert the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('status updated', $output);

        $this->assertEquals($expectStatus, $mandate->getStatus());

    }

    public function provideDataForUpdate()
    {
        return array(
            'is uptodate' => array('beforeToday'=>'-1 month','afterToday'=>'+4 months','createdAt'=>'-3 months',Mandate::UP_TO_DATE, 1 , Mandate::UP_TO_DATE ),
            'should be uptodate' => array('beforeToday'=>'-1 month','afterToday'=>'+4 months','createdAt'=>'-3 months',Mandate::OVERDUE, 1 , Mandate::UP_TO_DATE ),
            'payment is overdue' => array('beforeToday'=>'-1 month','afterToday'=>'+4 months','createdAt'=>'-3 months',Mandate::UP_TO_DATE, 0 , Mandate::OVERDUE ),
            'from scheduled to overdue' => array('beforeToday'=>'-1 month','afterToday'=>'+4 months','createdAt'=>'-3 months',Mandate::SCHEDULED, 0 , Mandate::OVERDUE ),
            'canceled' => array('beforeToday'=>'-1 month','afterToday'=>'+4 months','createdAt'=>'-3 months',Mandate::CANCELED, 2 , Mandate::CANCELED ),
            'complete' => array('beforeToday'=>'-1 month','afterToday'=>'+4 months','createdAt'=>'-3 months',Mandate::COMPLETE, 5 , Mandate::COMPLETE ),
        );
    }

}
