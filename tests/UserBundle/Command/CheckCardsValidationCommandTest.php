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

//  /**
//   *
//   *provide users with cards activated/unactivated at different creation dates to see how the command behaves
//   */
//    public function __construct()
//    {
//        $kernel = self::bootKernel();
//        $this->application = new Application($kernel);
//        $this->container = $kernel->getContainer(); 
//    }
//
//    protected function setUp()
//    {
//        self::runCommand('doctrine:fixtures:load --append --env=test --fixtures=src/Cairn/UserBundle/DataFixtures/ORM/LoadUser.php --no-interaction');
//
//    }

    protected static function runCommand($application,$command)
    {
        $command = sprintf('%s', $command);
        return $application->run(new StringInput($command));
    }

    public function testExecuteCardsValidationCommand()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        self::runCommand($application,'doctrine:fixtures:load --append --env=test --fixtures=src/Cairn/UserBundle/DataFixtures/ORM/LoadUser.php --no-interaction');

        $application->add(new CheckCardsValidationCommand());

        $command = $application->find('cairn.user:check-cards-validation');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        // assert the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('performed', $output);

        //assert the emails sent

        //assert the content of the database with respect to the setup
    }

}
