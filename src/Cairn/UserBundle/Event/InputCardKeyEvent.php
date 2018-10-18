<?php
// src/Cairn/UserBundle/Event/InputCardKeyEvent.php

namespace Cairn\UserBundle\Event;

use Cairn\UserBundle\Event\InputSecurityElementEvent;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * contains security elements specfic to a user's card 
 *
 */
class InputCardKeyEvent extends InputSecurityElementEvent
{
    /**
     *User's card key input
     *@var $string $cardKey
     */
    protected $cardKey;

    /**
     *Index of the card key that will be compared to user's input
     *@var int $position
     */
    protected $position;

     /**
      *This attribute is used to get the current session
      *@var Session $session
      */
    protected $session;


    public function __construct(UserInterface $user, $cardKey, $position, Session $session)
    {
        parent::__construct($user);
        $this->cardKey = $cardKey;
        $this->position = $position;
        $this->session = $session;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getCardKey()
    {
        return $this->cardKey;
    }

    public function getPosition()
    {
        return $this->position;
    }
}
