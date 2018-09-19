<?php

namespace Cairn\UserCyclosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage HTTP format                                                           
use Symfony\Component\HttpFoundation\Response;                                 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Session\Session; 

//manage Forms

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $session = new Session();
        $checker =$this->get('security.authorization_checker');
        $user = $this->getUser();

        if(!$checker->isGranted('ROLE_PRO')){
            $session->getFlashBag()->add('error','Vous n\'êtes pas authentifié(e) : accès refusé');
            return $this->redirectToRoute('fos_user_security_login');
        }
        else{
            return $this->redirectToRoute('cairn_user_welcome');
        }

    }
}
