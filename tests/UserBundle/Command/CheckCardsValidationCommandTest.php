<?php

// tests/UserBundle/Command/CheckCardsValidationCommandTest.php
namespace Tests\UserBundle\Command;

use Cairn\UserBundle\Command\CheckCardsValidationCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\StringInput;

use Symfony\Component\Console\Input\InputInterface;                            
use Symfony\Component\Console\Output\OutputInterface;

class CheckCardsValidationCommandTest extends KernelTestCase
{
    protected $application;
    protected $container;


    /**
     * WARNING : User used for this test MUST HAVE a not validated card
     *
     *@dataProvider provideDataForCards
     */
    public function testCardsValidationCommand($date,$emailSent,$hasCard,$content)
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $userRepo = $container->get('doctrine.orm.entity_manager')->getRepository('CairnUserBundle:User');


        $activationDelay = $container->getParameter('card_activation_delay');
        $limitDate = date_modify(new \Datetime(), '-'.$activationDelay.' days');
        $creationDate = date_modify($limitDate, $date);                

        $user = $userRepo->findOneByUsername('recycleco');
        $card  = $user->getCard();                                         
        $card->setCreationDate($creationDate);

        $container->get('doctrine.orm.entity_manager')->flush();

        $application = new Application($kernel);
        $application->add(new CheckCardsValidationCommand());

        $command = $application->find('cairn.user:check-cards-validation');
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
        if($hasCard){
            $this->assertTrue($user->getCard() instanceOf \Cairn\UserBundle\Entity\Card);
        }else{
            $this->assertEquals($user->getCard(), NULL);
        }
    }

    public function provideDataForCards()
    {
        return array(
            array('date'=>'+1 days','emailSent'=>true,'hasCard'=>true,'content'=>'en attente d\'activation'),
            array('date'=>'+2 days','emailSent'=>true,'hasCard'=>true,'content'=>'en attente d\'activation'),
            array('date'=>'+3 days','emailSent'=>false,'hasCard'=>true,'content'=>'en attente d\'activation'),
            array('date'=>'+5 days','emailSent'=>true,'hasCard'=>true,'content'=>'en attente d\'activation'),
            array('date'=>'+0 days','emailSent'=>true,'hasCard'=>false,'content'=>'été automatiquement révoquée'),
            array('date'=>'-1 days','emailSent'=>true,'hasCard'=>false,'content'=>'été automatiquement révoquée'),
            array('date'=>'-10 days','emailSent'=>true,'hasCard'=>false,'content'=>'été automatiquement révoquée'),
            array('date'=>'-2 months','emailSent'=>true,'hasCard'=>false,'content'=>'été automatiquement révoquée'),
        );
    }

}
