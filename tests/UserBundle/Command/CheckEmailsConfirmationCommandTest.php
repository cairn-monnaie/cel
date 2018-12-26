<?php

// tests/UserBundle/Command/CheckEmailsConfirmationCommandTest.php
namespace Tests\UserBundle\Command;

use Cairn\UserBundle\Command\CheckEmailsConfirmationCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\StringInput;

use Symfony\Component\Console\Input\InputInterface;                            
use Symfony\Component\Console\Output\OutputInterface;

class CheckEmailsConfirmationCommandTest extends KernelTestCase
{
    protected $application;
    protected $container;


    /**
     * WARNING : User used for this test MUST HAVE a NULL Login + be disabled + confirmationToken
     *
     *@dataProvider provideDataForEmails
     */
    public function testEmailsConfirmationCommand($date,$emailSent,$isOnRemoval,$content)
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $userRepo = $container->get('doctrine.orm.entity_manager')->getRepository('CairnUserBundle:User');


        $activationDelay = $container->getParameter('cairn_email_activation_delay');
        $limitDate = date_modify(new \Datetime(), '-'.$activationDelay.' days');
        $creationDate = date_modify($limitDate, $date);                

        $user = $userRepo->findOneByUsername('tout_1_fromage');
        $user->setLastLogin(NULL);                                         
        $user->setConfirmationToken('bjkbfvjkbhbsqcljbvsd');
        $user->setCreationDate($creationDate);

        $container->get('doctrine.orm.entity_manager')->flush();

        $application = new Application($kernel);
        $application->add(new CheckEmailsConfirmationCommand());

        $command = $application->find('cairn.user:check-emails-confirmation');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        // assert the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('performed successfully', $output);

        //assert the email sent
        $messageLogger = $container->get('swiftmailer.mailer.default.plugin.messagelogger');

        if($emailSent){
            $this->assertEquals(count($messageLogger->getMessages()),1);
            $message = $messageLogger->getMessages()[0];

            $this->assertInstanceOf('Swift_Message', $message);        
            $this->assertContains($content, $message->getBody());

            $this->assertSame($container->getParameter('cairn_email_noreply'), key($message->getFrom()));
            $this->assertSame($user->getEmail(), key($message->getTo()));
        }else{
            $this->assertEquals(count($messageLogger->getMessages()),0);
        }

        //assert the content of the database with respect to the setup
        if($isOnRemoval){
            $this->assertTrue($user->getRemovalRequest());
            $this->assertEquals($user->getConfirmationToken(),NULL);
        }else{
            $this->assertFalse($user->getRemovalRequest());
            $this->assertNotEquals($user->getConfirmationToken(),NULL);

            if($emailSent){
                $message = $messageLogger->getMessages()[0];
                $this->assertContains($user->getConfirmationToken(), $message->getBody());
            }
        }
    }

    public function provideDataForEmails()
    {
        return array(
            array('date'=>'+1 days','emailSent'=>true,'isOnRemoval'=>false,'content'=>'jour(s) pour procéder à l\'activation'),
            array('date'=>'+2 days','emailSent'=>true,'isOnRemoval'=>false,'content'=>'jour(s) pour procéder à l\'activation'),
            array('date'=>'+3 days','emailSent'=>false,'isOnRemoval'=>false,'content'=>'jour(s) pour procéder à l\'activation'),
            array('date'=>'+5 days','emailSent'=>true,'isOnRemoval'=>false,'content'=>'jour(s) pour procéder à l\'activation'),
            array('date'=>'+10 days','emailSent'=>false,'isOnRemoval'=>false,'content'=>'jour(s) pour procéder à l\'activation'),
            array('date'=>'+0 days','emailSent'=>true,'isOnRemoval'=>true,'content'=>'été automatiquement supprimé'),
            array('date'=>'-1 days','emailSent'=>true,'isOnRemoval'=>true,'content'=>'été automatiquement supprimé'),
            array('date'=>'-10 days','emailSent'=>true,'isOnRemoval'=>true,'content'=>'été automatiquement supprimé'),
            array('date'=>'-2 months','emailSent'=>true,'isOnRemoval'=>true,'content'=>'été automatiquement supprimé'),
        );
    }

}
