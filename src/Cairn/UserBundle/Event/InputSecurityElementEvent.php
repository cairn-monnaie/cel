<?php
// src/Cairn/UserBundle/Event/InputSecurityElementEvent.php

namespace Cairn\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Superclass for security events that require an input from a user
 *
 */
class InputSecurityElementEvent extends Event
{
    /**
     *User to be tested through a security event
     *@var \Cairn\UserBundle\Entity\User
     */
    protected $user;

    /**
     *This attribute is used to tell if it is necessary to redirect $user to login 
     *@var string $redirect
     */
    protected $redirect;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
        $this->redirect = false;
    }


    public function getUser(){
        return $this->user;
    }

    public function getRedirect()
    {
        return $this->redirect;
    }

    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

}
