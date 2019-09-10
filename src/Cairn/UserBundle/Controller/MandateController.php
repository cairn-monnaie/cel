<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Cyclos
use Cyclos;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Mandate;
use Cairn\UserBundle\Entity\Operation;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
//manage Forms
use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\PasswordType;                   
use Cairn\UserBundle\Form\MandateType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * This class contains all actions related to user experience
 *
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class MandateController extends Controller
{

    public function mandateDashboard(Request $request)
    {


    }

    public function declareMandateAction(Request $request)
    {
        
        $mandate = new Mandate();
        $form = $this->createForm(MandateType::class, $mandate);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            

        }

        return $this->render('CairnUserBundle:Mandate:add.html.twig',
            array('form'=>$form->createView())
            );

    }

    public function editMandate(Request $request, Mandate $mandate)
    {

    }

    public function deleteMandate(Request $request, Mandate $mandate)
    {

    }
}
