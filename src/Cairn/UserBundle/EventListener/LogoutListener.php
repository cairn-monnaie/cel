<?php

namespace Cairn\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use FOS\UserBundle\Model\UserManagerInterface;

use Cairn\UserCyclosBundle\Entity\LoginManager;

class LogoutListener implements LogoutHandlerInterface {
    protected $userManager;

    public function __construct(UserManagerInterface $userManager){
        $this->userManager = $userManager;
    }

    public function logout(Request $Request, Response $Response, TokenInterface $Token) {
        $loginManager = new LoginManager();
        $loginManager->logout();
        $myfile = fopen("logout.txt", "w");
        fwrite($myfile, 'logout succesfully executed !');
        fclose($myfile);
    }
}
