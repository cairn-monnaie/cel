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

    /**
     * Tests the installation of an administrator in command line.
     *
     * If the admin is not supposed to be already installed, he is removed before command line is called.
     * Then, if the admin has been successfully installed after the command line had been called, the user entity
     * exists, is enabled and must be forced to change his password
     *
     *@dataProvider provideInstallData
     */
    public function testExecuteInstallAdminCommand($login, $password, $message, $isAlreadyInstalled,$successInstalled)
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $client = $kernel->getContainer()->get('test.client');                 
        $client->setServerParameters(array());

        $container = $kernel->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $application = new Application($kernel);
        $application->add(new CreateInstallAdminCommand());

        if(! $isAlreadyInstalled){
            $admin = $em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'admin_network'));        

            $operations = $em->getRepository('CairnUserBundle:Operation')->findBy(array('creditor'=>$admin));    
            foreach($operations as $operation){                                
                $operation->setCreditor(NULL);                                 
            }                                                                  

            $operations = $em->getRepository('CairnUserBundle:Operation')->findBy(array('debitor'=>$admin));     
            foreach($operations as $operation){                                
                $operation->setDebitor(NULL);                                  
            }
            $em->remove($admin);
            $em->flush();
        }

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


        if($successInstalled){
            //test a connection
            $crawler = $client->request('GET','/login');                     

            $form = $crawler->selectButton('_submit')->form();                     
            $form['_username']->setValue($login);                               
            $form['_password']->setValue($password);                               
            $crawler = $client->submit($form);                               

            $admin = $em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));   

            $this->assertTrue($admin->isFirstLogin());

            //check that, after first login, user is redirected to password change
            $crawler =  $client->followRedirect(); //login_check
            $this->assertTrue($client->getResponse()->isRedirect('/profile/change-password'));
            $crawler =  $client->followRedirect();

        }

        $kernel->shutDown();
    }

    public function provideInstallData()
    {
        return array(
            'wrong pwd'=>array('username'=>'admin_network','password'=>'@bcdefgh',
                               'message'=>'Wrong','isAlreadyInstalled'=>false,'successInstalled'=>false),
            'wrong login'=>array('username'=>'test_admin','password'=>'@@bbccdd',
                                 'message'=>'Wrong','isAlreadyInstalled'=>false,'successInstalled'=>false),
            'success'=>array('username'=>'admin_network','password'=>'@@bbccdd',
                                 'message'=>'created successfully','isAlreadyInstalled'=>false,'successInstalled'=>true),
            'already installed'=>array('username'=>'admin_network','password'=>'@@bbccdd',
                                 'message'=>'already been created','isAlreadyInstalled'=>true,'successInstalled'=>false),
        );
    }
}
