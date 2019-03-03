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
use Cairn\UserBundle\Entity\Address;
use Cairn\UserBundle\Entity\ZipCity;

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

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name,$data, $dataName);
        self::$kernel = static::createKernel();                                      
        self::$kernel->boot();                                                       
        $this->container = self::$kernel->getContainer();

        $this->eventDispatcher = new EventDispatcher();
    }

    public function testOnRegistrationInitialize()
    {
        $listener = new RegistrationListener($this->container);
        $this->eventDispatcher->addListener(FOSUserEvents::REGISTRATION_INITIALIZE, array($listener, 'onRegistrationInitialize'));

        //we use a client to retrieve a real instance of Request, filled with necessary attributes and parameters
        $client = $this->container->get('test.client');                 
        $client->setServerParameters(array());

        $client->request('GET','/inscription');

        $request = $client->getRequest();//$this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $session = $request->getSession();


        //PRO is registering
        $user = new User();
        $session->set('registration_type','pro');

        $event = new UserEvent($user,$request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE,$event); 
        $this->assertTrue($user->hasRole('ROLE_PRO'));
        $this->assertFalse($user->hasRole('ROLE_PERSON'));
        $this->assertFalse($user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($user->hasRole('ROLE_SUPER_ADMIN'));

        //PERSON is registering
        $user = new User();
        $session->set('registration_type','person');

        $event = new UserEvent($user,$request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE,$event); 
        $this->assertTrue($user->hasRole('ROLE_PERSON'));
        $this->assertFalse($user->hasRole('ROLE_PRO'));
        $this->assertFalse($user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($user->hasRole('ROLE_SUPER_ADMIN'));

        //ADMIN is trying to get registered by none admin user
        $user = new User();
        $session->set('registration_type','localGroup');

        $event = new UserEvent($user,$request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE,$event); 
        $this->assertTrue($user->hasRole('ROLE_PERSON'));
        $this->assertTrue($session->get('registration_type') == 'person');
        $this->assertFalse($user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($user->hasRole('ROLE_PRO'));
        $this->assertFalse($user->hasRole('ROLE_SUPER_ADMIN'));

        //SUPER_ADMIN is trying to get registered by none admin user
        $user = new User();
        $session->set('registration_type','superAdmin');

        $event = new UserEvent($user,$request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE,$event); 
        $this->assertTrue($user->hasRole('ROLE_PERSON'));
        $this->assertTrue($session->get('registration_type') == 'person');
        $this->assertFalse($user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($user->hasRole('ROLE_PRO'));
        $this->assertFalse($user->hasRole('ROLE_SUPER_ADMIN'));

        //Undefined User type registration
        $user = new User();
        $session->set('registration_type','XXXX');

        $event = new UserEvent($user,$request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE,$event); 
        $this->assertTrue($user->hasRole('ROLE_PERSON'));
        $this->assertTrue($session->get('registration_type') == 'person');
        $this->assertFalse($user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($user->hasRole('ROLE_PRO'));
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
        $this->assertTrue($user->getCard() == NULL);
        $this->assertTrue($user->getCyclosID() != NULL);

    }

    /**
     *
     *@dataProvider provideDataForRegistrationConfirm
     */
    public function testOnRegistrationConfirm($role, $city, $nbReferents)
    {
        //workaround for error on reaching templating service in test environment
        $templating = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')->getMock();
        $templating
            ->expects($this->any())
            ->method('render')
            ->willReturn('Un email vous sera envoyé');

        $this->container->set('templating',$templating);
                                                    
        $listener = new RegistrationListener($this->container);
        $this->eventDispatcher->addListener(FOSUserEvents::REGISTRATION_CONFIRM, array($listener, 'onRegistrationConfirm'));

        $user = new User();
        $address = new Address();
        $zipCity = new ZipCity();
        $zipCity->setCity($city);

        $address->setZipCity($zipCity);

        $user->setRoles(array($role));
        $user->setAddress($address);

        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM,$event); 

        //user must be disabled because the member area is validated "by hand" by admins
        $this->assertFalse($user->isEnabled());
        $this->assertTrue($session->getFlashBag()->has('success'));

        //super admins are automatically assigned as referent
        $this->assertTrue( count($user->getReferents()) == $nbReferents);
        $this->assertTrue($event->getResponse()->isRedirection('/logout'));

    }

    public function provideDataForRegistrationConfirm()
    {
        return array(
            'pro : city matches an admin city'=>array('ROLE_PRO','Grenoble',2),
            'pro : city does not match an admin city'=>array('ROLE_PRO','Meylan',1),
            'person '=>array('ROLE_PERSON','Meylan',4),
            'person '=>array('ROLE_PERSON','Meylan',4),

        );
    }
}
