<?php
//src/Cairn/Tests/UserBundle/EventListener/RegistrationListenerTest.php

namespace Tests\UserBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Cyclos;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Cairn\UserBundle\Repository\UserRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;

use Cairn\UserBundle\EventListener\RegistrationListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Cairn\UserBundle\Event\InputCardKeyEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;


class RegistrationListenerTest extends KernelTestCase
{

    private $eventDispatcher;

    protected static $kernel;

    private $container;

    public function __construct()
    {
        self::$kernel = static::createKernel();                                      
        self::$kernel->boot();                                                       
        $this->container = self::$kernel->getContainer();

        $this->eventDispatcher = new EventDispatcher();
    }

    public function testOnRegistrationInitialize()
    {
        $listener = new RegistrationListener($this->container);
        $this->eventDispatcher->addListener(FOSUserEvents::REGISTRATION_INITIALIZE, array($listener, 'onRegistrationInitialize'));

        //PRO is registering
        $user = new User();
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $session->set('registration_type','pro');
        $request->setSession($session);

        $event = new UserEvent($user,$request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE,$event); 
        $this->assertTrue($user->hasRole('ROLE_PRO'));
        $this->assertFalse($user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($user->hasRole('ROLE_SUPER_ADMIN'));

        //local group is registering
        $user = new User();
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $session->set('registration_type','localGroup');
        $request->setSession($session);

        $event = new UserEvent($user,$request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE,$event); 
        $this->assertFalse($user->hasRole('ROLE_PRO'));
        $this->assertTrue($user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($user->hasRole('ROLE_SUPER_ADMIN'));

    }

    public function testOnRegistrationSuccess()
    {
        $listener = new RegistrationListener($this->container);
        $this->eventDispatcher->addListener(FOSUserEvents::REGISTRATION_SUCCESS, array($listener, 'onRegistrationSuccess'));

        $user = new User();
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->disableOriginalConstructor()->getMock();
        $form->expects($this->any())
            ->method('getData')
            ->willReturn($user);

        $event = new FormEvent($form, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS,$event); 

        //necessary to pass the constraint "cannot be null"
        $this->assertTrue($user->getCard() != NULL);
        $this->assertTrue($user->getCyclosID() != NULL);

    }

    public function testOnRegistrationConfirm()
    {
        $listener = new RegistrationListener($this->container);
        $this->eventDispatcher->addListener(FOSUserEvents::REGISTRATION_CONFIRM, array($listener, 'onRegistrationConfirm'));

        $user = new User();
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM,$event); 

        $this->assertFalse($user->isEnabled());
        $this->assertTrue($session->getFlashBag()->has('success'));

    }

}
