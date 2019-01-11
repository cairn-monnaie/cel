<?php
//src/Cairn/Tests/UserBundle/EventListener/SecurityListenerTest.php

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

use Cairn\UserBundle\EventListener\ExceptionListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;


class ExceptionListenerTest extends KernelTestCase
{

    private $eventDispatcher;

    protected static $kernel;


    private $container;

    public function __construct()
    {
        parent::__construct();
        self::$kernel = static::createKernel();                                      
        self::$kernel->boot();                                                       
        $this->container = self::$kernel->getContainer();


        $this->eventDispatcher = new EventDispatcher();
    }


    public function testOnKernelException()
    {
        $security = $this->container->get('cairn_user.security');
        $router = $this->container->get('router');
        $em = $this->container->get('doctrine.orm.entity_manager');
        $accessPlatform = $this->container->get('cairn_user.access_platform');
        $messageNotificator = $this->container->get('cairn_user.message_notificator');


        $listener = new ExceptionListener($messageNotificator, $accessPlatform, $em, $router, $security);
        $this->eventDispatcher->addListener(KernelEvents::EXCEPTION, array($listener, 'onKernelException'));

        //we use a client to retrieve a real instance of Request, filled with necessary attributes and parameters
        $client = $this->container->get('test.client');                 
        $client->setServerParameters(array());

        $client->request('GET','/inscription');

        $request = $client->getRequest();//$this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $logout = '/logout';
        $welcome = '/';
        //test ServiceException
        //test LOGIN error
        $exception = new Cyclos\ServiceException('Service test','Operation test','LOGIN',NULL);
        $event = new GetResponseForExceptionEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
        $this->eventDispatcher->dispatch(KernelEvents::EXCEPTION,$event); 
        $this->assertTrue($event->getResponse()->isRedirect($logout));

        $messages_flash = $event->getRequest()->getSession()->getFlashBag()->get('error',array());
        $this->assertContains('Problème de connexion',$messages_flash[0]);


        //test ENTITY_NOT_FOUND
        $exception = new Cyclos\ServiceException('Service test','Operation test','ENTITY_NOT_FOUND',NULL);
        $event = new GetResponseForExceptionEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
        $this->eventDispatcher->dispatch(KernelEvents::EXCEPTION,$event); 
        $this->assertFalse($event->getResponse()->isRedirect($logout));
        $this->assertTrue($event->getResponse()->isRedirect($welcome));

        $messages_flash = $event->getRequest()->getSession()->getFlashBag()->get('error',array());
        $this->assertContains('introuvable',$messages_flash[0]);

        //test PERMISSION_DENIED
        $exception = new Cyclos\ServiceException('Service test','Operation test','PERMISSION_DENIED',NULL);
        $event = new GetResponseForExceptionEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
        $this->eventDispatcher->dispatch(KernelEvents::EXCEPTION,$event); 
        $this->assertFalse($event->getResponse()->isRedirect($logout));
        $this->assertTrue($event->getResponse()->isRedirect($welcome));

        $messages_flash = $event->getRequest()->getSession()->getFlashBag()->get('error',array());
        $this->assertContains('pas les droits',$messages_flash[0]);

        //test LOGGED_OUT
        $exception = new Cyclos\ServiceException('Service test','Operation test','LOGGED_OUT',NULL);
        $event = new GetResponseForExceptionEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
        $this->eventDispatcher->dispatch(KernelEvents::EXCEPTION,$event); 
        $this->assertTrue($event->getResponse()->isRedirect($logout));

        $messages_flash = $event->getRequest()->getSession()->getFlashBag()->get('error',array());
        $this->assertContains('session a expiré',$messages_flash[0]);

        //test NULL_POINTER
        $exception = new Cyclos\ServiceException('Service test','Operation test','NULL_POINTER',NULL);
        $event = new GetResponseForExceptionEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
        $this->eventDispatcher->dispatch(KernelEvents::EXCEPTION,$event); 
        $this->assertFalse($event->getResponse()->isRedirect($logout));
        $this->assertTrue($event->getResponse()->isRedirect($welcome));

        $messages_flash = $event->getRequest()->getSession()->getFlashBag()->get('error',array());
        $this->assertContains('introuvable',$messages_flash[0]);

        //test else
        $exception = new Cyclos\ServiceException('Service test','Operation test','XXX',NULL);
        $event = new GetResponseForExceptionEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
        $this->eventDispatcher->dispatch(KernelEvents::EXCEPTION,$event); 
        $this->assertTrue($event->getResponse()->isRedirect($welcome));

        $messages_flash = $event->getRequest()->getSession()->getFlashBag()->get('error',array());
        $this->assertContains('erreur technique',$messages_flash[0]);

        //test ConnectionException
        $exception = new Cyclos\ConnectionException();
        $event = new GetResponseForExceptionEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
        $this->eventDispatcher->dispatch(KernelEvents::EXCEPTION,$event); 
        $this->assertTrue($event->getResponse()->isRedirect($logout));
        $this->assertTrue(file_exists('maintenance.txt'));
        unlink('maintenance.txt');

        //test non-cyclos exception
        $exception = new \Exception();
        $event = new GetResponseForExceptionEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
        $this->eventDispatcher->dispatch(KernelEvents::EXCEPTION,$event); 
        $this->assertTrue($event->getResponse() == NULL);
    }


}
