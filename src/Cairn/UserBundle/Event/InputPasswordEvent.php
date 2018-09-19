<?php
// src/Cairn/UserBundle/Event/InputPasswordEvent.php

namespace Cairn\UserBundle\Event;

use Cairn\UserBundle\Event\InputSecurityElementEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * contains security elements specfic to a user's password 
 *
 */
class InputPasswordEvent extends InputSecurityElementEvent
{
    /**
     *Input password that will be compared to user's real one
     *@var int $position
     */
    protected $password;

    public function __construct(UserInterface $user, $password)
    {
        parent::__construct($user);
        $this->password = $password;
    }


    public function getPassword()
    {
        return $this->password;
    }
}
