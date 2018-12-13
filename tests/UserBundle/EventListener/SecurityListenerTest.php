<?php
//src/Cairn/Tests/UserBundle/EventListener/SecurityListenerTest.php

namespace Tests\UserBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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

use Cairn\UserBundle\EventListener\SecurityListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;


class SecurityListenerTest extends KernelTestCase
{

    private $eventDispatcher;

    protected static $kernel;

    private $user;

    private $container;

    public function __construct()
    {
        self::$kernel = static::createKernel();                                      
        self::$kernel->boot();                                                       
        $this->container = self::$kernel->getContainer();
                                                                                         

        $this->eventDispatcher = new EventDispatcher();
    }

    public function setUp()
    {
        $this->user = new User();
        $this->user->setUsername('LaBonnePioche');
        $this->user->setFirstLogin(true);
//        $this->user->setName($this->userData->name);
//        $this->user->setEmail($this->userData->email);
//        $this->user->setCyclosID($id);
//
//
//        $password = '@@bbccdd';
//        $this->user->setPlainPassword($password);
        $this->user->setEnabled(true);

    }

    public function testEventsPriority()
    {
        ;
    }

    /*
     *As user is not connected during this unit test, the real Security service will never be tested. To simulate that an user is connecte
     * we mock the security service and make it return an instance of User
     */
    public function testDisabledUser()
    {
        //first, this->user is disabled
        $this->user->setEnabled(false);


        $security = $this->getMockBuilder('Cairn\UserBundle\Service\Security')->disableOriginalConstructor()->getMock();
        $security->expects($this->any())
            ->method('getCurrentUser')
            ->willReturn($this->user);

        $this->container->set('cairn_user.security',$security);

        $listener = new SecurityListener($this->container);
        $this->eventDispatcher->addListener(KernelEvents::REQUEST, array($listener, 'onDisabledUser'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $event = new GetResponseEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->eventDispatcher->dispatch(KernelEvents::REQUEST,$event); 

        $this->assertFalse($event->getResponse() == NULL);
        $this->assertEquals($event->getResponse()->getStatusCode(), 302);
        $logout = '/logout';
        $this->assertTrue($event->getResponse()->isRedirect($logout));

        //finally, this->user is enabled
        $this->user->setEnabled(true);
        $security->expects($this->any())
            ->method('getCurrentUser')
            ->willReturn($this->user);

        $event = new GetResponseEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->eventDispatcher->dispatch(KernelEvents::REQUEST,$event); 
        $this->assertTrue($event->getResponse() == NULL);
    }

    public function testMaintenance()
    {
        $listener = new SecurityListener($this->container);
        $this->eventDispatcher->addListener(KernelEvents::REQUEST, array($listener, 'onMaintenance'));

        //artificially create maintenance file
        file_put_contents("maintenance.txt", '');

        $request = new Request(); //$this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();

        $event = new GetResponseEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->eventDispatcher->dispatch(KernelEvents::REQUEST,$event); 

        $this->assertFalse($event->getResponse() == NULL);
        $this->assertContains('maintenance',$event->getResponse()->getContent());
        $this->assertNotContains('security.login.username',$event->getResponse()->getContent());

        //delete maintenance file
        unlink('maintenance.txt');

        //finally, test that connection works properly
        $event = new GetResponseEvent(self::$kernel, $request, HttpKernelInterface::SUB_REQUEST);
        $this->eventDispatcher->dispatch(KernelEvents::REQUEST,$event); 

        $this->assertTrue($event->getResponse() == NULL);
    }


    public function testCardKeyInput()
    {
        $session = new Session(new MockArraySessionStorage());

        //ajouter la carte
        $card = new Card($this->user,5,5,NULL);
        $card->generateCard('test');
        $this->user->setCard($card);

        //we use these lines to get a plaintext password encoder(not used in config)
        $encoder = new PlaintextPasswordEncoder();
        $encoderFactory = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\EncoderFactory')->disableOriginalConstructor()->getMock();
        $encoderFactory->expects($this->any())
            ->method('getEncoder')
            ->willReturn($encoder);
        $this->container->set('security.encoder_factory',$encoderFactory);


        $listener = new SecurityListener($this->container);
        $inputCardKeyEvent = \Cairn\UserBundle\Event\SecurityEvents::INPUT_CARD_KEY;
        $this->eventDispatcher->addListener($inputCardKeyEvent, array($listener, 'onCardKeyInput'));

        //wrong key
        $event = new InputCardKeyEvent($this->user,'2222',10, $session);
        $this->eventDispatcher->dispatch($inputCardKeyEvent,$event); 
        $this->assertEquals(1,$event->getUser()->getCardKeyTries());
        $this->assertEquals($event->getSession()->get('has_input_card_key_valid'),NULL);

        //right key
        $event = new InputCardKeyEvent($this->user,'1111',10, $session);
        $this->eventDispatcher->dispatch($inputCardKeyEvent,$event); 
        $this->assertEquals($event->getUser()->getCardKeyTries(),0);
        $this->assertTrue($event->getSession()->get('has_input_card_key_valid'));

        //3 wrong keys in a row
        for($i =0 ; $i < 3 ; $i++){
            $event = new InputCardKeyEvent($this->user,'2222',10, $session);
            $this->eventDispatcher->dispatch($inputCardKeyEvent,$event); 
        }
        $this->assertEquals(3,$event->getUser()->getCardKeyTries());
        $this->assertFalse($event->getUser()->isEnabled());
    }

    public function testOnLogin()
    {
        $listener = new SecurityListener($this->container);
        $this->eventDispatcher->addListener(SecurityEvents::INTERACTIVE_LOGIN, array($listener, 'onLogin'));

        $listener = new SecurityListener($this->container);

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')->disableOriginalConstructor()->getMock();

        $request = new Request();

        //good login/password
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $request->request->set('_username',$this->user->getUsername());
        $request->request->set('_password','@@bbccdd');

        $event = new InteractiveLoginEvent($request,$token);
        $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN,$event); 
        $this->assertNotEquals($event->getRequest()->getSession()->get('cyclos_session_token'),NULL);

        $this->assertTrue($this->eventDispatcher->hasListeners(\Cairn\UserBundle\Event\SecurityEvents::FIRST_LOGIN));

        //wrong password
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);
        $request->request->set('_username','LBP');
        $request->request->set('_password','@@bbccdd');

        $event = new InteractiveLoginEvent($request,$token);
        try{
            $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN,$event); 
        }catch(\Exception $e){
            $this->assertEquals($e->errorCode, 'LOGIN');
        }
        $this->assertEquals($event->getRequest()->getSession()->get('cyclos_session_token'),NULL);

    }

    public function testOnFirstLogin()
    {
        $security = $this->getMockBuilder('Cairn\UserBundle\Service\Security')->disableOriginalConstructor()->getMock();
        $security->expects($this->any())
            ->method('getCurrentUser')
            ->willReturn($this->user);

        $this->container->set('cairn_user.security',$security);

        $listener = new SecurityListener($this->container);
        $this->eventDispatcher->addListener(KernelEvents::RESPONSE, array($listener, 'onFirstLogin'));

        //we use a client to retrieve a real instance of Request, filled with necessary attributes and parameters
        $client = $this->container->get('test.client');                 
        $client->setServerParameters(array());

        //request is not change password
        $client->request('GET','/');
        $request = $client->getRequest();//$this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();

        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $event = new FilterResponseEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST,new Response());

        $this->eventDispatcher->dispatch(KernelEvents::RESPONSE,$event); 
        $this->assertTrue($event->getResponse()->isRedirect('/profile/change-password'));

        //request is change password
        $client->request('GET','/profile/change-password');
        $request = $client->getRequest();//$this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();

        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $event = new FilterResponseEvent(self::$kernel, $request, HttpKernelInterface::MASTER_REQUEST,new Response());

        $this->eventDispatcher->dispatch(KernelEvents::RESPONSE,$event); 
        $this->assertFalse($event->getResponse()->isRedirect());

    }


    public function testOnResetPassword()
    {
        $listener = new SecurityListener($this->container);
        $this->eventDispatcher->addListener(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, array($listener, 'onResetPassword'));

        $request = new Request();
        //good login/password
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        //user has already been connected to the application
        $this->user->setLastLogin(new \Datetime());
        $event = new GetResponseNullableUserEvent($this->user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE,$event); 
        $this->assertTrue($event->getUser()->isEnabled());

        //user has never been connected to the application
        $this->user->setLastLogin(NULL);
        $event = new GetResponseNullableUserEvent($this->user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE,$event); 
        $this->assertFalse($event->getUser()->isEnabled());
        $logout = '/logout';
        $this->assertTrue($event->getResponse()->isRedirect($logout));

    }


}
