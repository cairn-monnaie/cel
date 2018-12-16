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
    public function testExecuteInstallAdminCommand($login, $password, $message, $isInstalled, $firstLogin, $cardGenerated)
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

            $admin = $em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));   

            if($firstLogin){     
                $this->assertTrue($admin->isFirstLogin());

                //check that, after first login, user is redirected to password change
                $this->assertTrue($client->getResponse()->isRedirect('/profile/change-password'));
                $crawler =  $client->followRedirect();

                //change password
                $form = $crawler->selectButton('fos_user_change_password_form_save')->form();
                $form['fos_user_change_password_form[current_password]']->setValue('@@bbccdd');
                $form['fos_user_change_password_form[plainPassword][first]']->setValue('@@bbccdd');
                $form['fos_user_change_password_form[plainPassword][second]']->setValue('@@bbccdd');
                $crawler = $client->submit($form);
                $crawler =  $client->followRedirect();

                $em->refresh($admin);
                $this->assertFalse($admin->isFirstLogin());
            }

            if(!$cardGenerated){
                //test card generation
                $crawler = $client->request('GET','/card/generate/'.$admin->getID());

                $form = $crawler->selectButton('confirmation_save')->form();       
                $crawler =  $client->submit($form);                          
                $crawler = $client->followRedirect(); 

//                $admin = $em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));        
                $em->refresh($admin);
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
            'wrong pwd'=>array('username'=>'admin_network','password'=>'@bcdefgh',
            'message'=>'Wrong','isInstalled'=>false,'firstLogin'=>true,'cardGenerated'=>false),
            'wrong login'=>array('username'=>'test_admin','password'=>'@@bbccdd',
            'message'=>'Wrong','isInstalled'=>false,'firstLogin'=>true,'cardGenerated'=>false),
            'success'=>array('username'=>'admin_network','password'=>'@@bbccdd',
            'message'=>'created successfully','isInstalled'=>true,'firstLogin'=>true,'cardGenerated'=>false),
            'already installed'=>array('username'=>'admin_network','password'=>'@@bbccdd',
            'message'=>'already been created','isInstalled'=>true,'firstLogin'=>false,'cardGenerated'=>true),
        );
    }
}
