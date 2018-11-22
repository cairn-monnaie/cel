<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class LoginManager
{
    private $loginService;

    public function __construct()
    {
        $this->loginService = new Cyclos\LoginService();
    }

    public function login($timeIntervalDTO)
    {
        return $this->loginService->login($timeIntervalDTO);
    }

    public function logout()
    {
        $this->loginService->logout();
    }

}
