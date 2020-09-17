<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Cyclos
use Cyclos;

//manage Events 
use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Phone;
use Cairn\UserBundle\Entity\Deposit;
use Cairn\UserCyclosBundle\Entity\UserManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
//manage Forms
use Cairn\UserBundle\Form\SmsDataType;
use Cairn\UserBundle\Form\PhoneType;
use Cairn\UserBundle\Form\ConfirmationType;

use Cairn\UserBundle\Validator\UserPassword;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\PasswordType;                   


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * This class contains all actions than can be public
 *
 */
class PublicController extends Controller
{

    public function listProsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $qb = $userRepo->createQueryBuilder('u');
        $userRepo->whereRole($qb,'ROLE_PRO')->whereConfirmed($qb);

        $pros = $qb->getQuery()->getResult();

        shuffle($pros);
        return $this->render('CairnUserBundle:Pro:all.html.twig',array('pros'=>$pros ));
    }

    public function cardPresentationAction()
    {
        return $this->render('CairnUserBundle:Default:howto_card.html.twig');
    }

    public function smsPresentationAction()
    {
        return $this->render('CairnUserBundle:Default:howto_sms_page.html.twig');
    }

}
