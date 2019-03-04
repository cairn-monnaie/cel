<?php
//src/Cairn/Tests/UserBundle/EventListener/OperationListenerTest.php

namespace Tests\UserBundle\EventListener;

use Doctrine\ORM\Events;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

use Cairn\UserBundle\Entity\Operation;

class OperationListenerTest extends TestCase
{

    public function testPostLoad()
    {
    }
}
