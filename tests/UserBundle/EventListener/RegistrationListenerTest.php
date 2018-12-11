<?php
//src/Cairn/Tests/UserBundle/EventListener/RegistrationListenerTest.php

namespace Tests\UserBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

use Cairn\UserBundle\Entity\User;

class RegistrationListenerTest extends TestCase
{

    public function testProfileEdit()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->getMock();
        $user = $container->get('doctrine')->getManager()->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'locavore'));


        $newName = 'John Doe';
        $newUsername = 'john_doe';
        $newEmail = 'john_doe@cairn-monnaie.com';
        
        $user->setName($newName);
        $user->setUsername($newUsername);
        $user->setEmail($newEmail);

        //mock the repository
        $userRepo = $this->getMockBuilder('Cairn\UserBundle\Repository\UserRepository')->getMock();
        $userRepo->expects($this->any())
                        ->method('find')
                        ->willReturn($user);
        
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects($this->any())
                        ->method('getRepository')
                        ->willReturn($userRepo);

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $event = new FormEvent($form,$request);

        $eventDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->getMock();
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS,$event); 

        //test how the user has changed on Cyclos side
        //get userVO
        $this->assertEquals($userVO->name, $newName);
        $this->assertEquals($userVO->username, $newUserName);
        $this->assertEquals($userVO->email, $newEmail);

    }
}
