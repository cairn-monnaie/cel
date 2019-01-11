<?php

// tests/UserBundle/Command/CheckCardsAssociationCommandTest.php
namespace Tests\UserBundle\Command;

use Cairn\UserBundle\Command\CheckCardsAssociationCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\StringInput;

use Symfony\Component\Console\Input\InputInterface;                            
use Symfony\Component\Console\Output\OutputInterface;

class CheckCardsAssociationCommandTest extends KernelTestCase
{
    protected $application;
    protected $container;


    /**
     *
     *@dataProvider provideDataForCards
     */
    public function testCardsAssociationCommand($date,$isRemoved)
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $userRepo = $container->get('doctrine.orm.entity_manager')->getRepository('CairnUserBundle:User');
        $cardRepo = $container->get('doctrine.orm.entity_manager')->getRepository('CairnUserBundle:Card');

        $securityService = $container->get('cairn_user.security');

        $associationDelay = $container->getParameter('card_association_delay');
        $limitDate = date_modify(new \Datetime(), '-'.$associationDelay.' days');
        $creationDate = date_modify($limitDate, $date);                


        $salt = $securityService->generateCardSalt();
        $uniqueCode = $securityService->findAvailableCode();
        $card = new Card(NULL,$container->getParameter('cairn_card_rows'),$container->getParameter('cairn_card_cols'),$salt,$uniqueCode);
        $card->generateCard($container->getParameter('kernel.environment'));
        $card->setCreationDate($creationDate);

        $container->get('doctrine.orm.entity_manager')->flush();

        $application = new Application($kernel);
        $application->add(new CheckCardsAssociationCommand());

        $command = $application->find('cairn.user:check-cards-association');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        // assert the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('performed successfully', $output);

        $card = $cardRepo->findAvailableCardWithCode($uniqueCode);

        if($isRemoved){
            $this->assertEquals($card,NULL);
        }else{
            $this->assertNotEquals($card,NULL);
        }
    }

    public function provideDataForCards()
    {
        return array(
            array('date'=>'+1 days'  ,'isRemoved'=>false),
            array('date'=>'+2 days'  ,'isRemoved'=>false),
            array('date'=>'+3 days'  ,'isRemoved'=>false),
            array('date'=>'+5 days'  ,'isRemoved'=>false),
            array('date'=>'+0 days'  ,'isRemoved'=>false),
            array('date'=>'-1 days'  ,'isRemoved'=>true),
            array('date'=>'-10 days' ,'isRemoved'=>true),
            array('date'=>'-2 months','isRemoved'=>true),
        );
    }

}
