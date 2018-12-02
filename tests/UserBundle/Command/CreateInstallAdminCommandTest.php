<?php

// tests/UserBundle/Command/CreateInstallAdminCommandTest.php
namespace Tests\UserBundle\Command;

use Cairn\UserBundle\Command\CreateInstallAdminCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\StringInput;

use Cyclos;

class CreateInstallAdminCommandTest extends KernelTestCase
{

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

    /**
     *@dataProvider provideInstallData
     */
    public function testExecuteInstallAdminCommand($login, $password, $message, $isInstalled, $cardGenerated)
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $client = $kernel->getContainer()->get('test.client');                 
        $client->setServerParameters(array());

        $container = $kernel->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $application = new Application($kernel);
        $application->add(new CreateInstallAdminCommand());

        $command = $application->find('cairn.user:create-install-admin');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'username'=> $login,
            'password'=> $password
        ));

        // assert the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains($message, $output);

        $admin = $em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));        

        if($isInstalled){
            $this->assertTrue($admin != NULL);

            //test a connection
            $crawler = $client->request('GET','/login');                     

            $form = $crawler->selectButton('_submit')->form();                     
            $form['_username']->setValue($login);                               
            $form['_password']->setValue($password);                               
            $crawler = $client->submit($form);                               

            $crawler =  $client->followRedirect();

            $this->assertSame(1, $crawler->filter('li#id_welcome')->count());

            if(!$cardGenerated){
                //test card generation
                $crawler = $client->request('GET','/card/generate/'.$admin->getID());

                $form = $crawler->selectButton('confirmation_save')->form();       
                $crawler =  $client->submit($form);                          
                $crawler = $client->followRedirect(); 

                $admin = $em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));        
                $card = $admin->getCard();
                $this->assertTrue($card->isGenerated());
            }else{
                $card = $admin->getCard();
                $this->assertTrue($card->isGenerated());
            }
        }else{
            $this->assertTrue($admin == NULL);
        }
        //assert the emails sent

        //assert the content of the database with respect to the setup
    }

    public function provideInstallData()
    {
        return array(
            array('username'=>'test_admin','password'=>'@bcdefgh','message'=>'Wrong','isInstalled'=>false,'cardGenerated'=>false),
            array('username'=>'admin','password'=>'@@bbccdd','message'=>'Wrong','isInstalled'=>false,'cardGenerated'=>false),
            array('username'=>'test_admin','password'=>'@@bbccdd','message'=>'created successfully','isInstalled'=>true,
            'cardGenerated'=>false),
            array('username'=>'test_admin','password'=>'@@bbccdd','message'=>'already been created','isInstalled'=>true,
            'cardGenerated'=>true),
        );
    }
}
