<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Entities
use Cairn\UserBundle\Entity\Banknote;
use Cairn\UserBundle\Entity\BanknoteStatus;
//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Cairn\UserBundle\Form\BanknoteType;
use Cairn\UserBundle\Form\SearchBanknoteType;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BanknoteController extends Controller
{
    public function indexAction()
    {
        return $this->render('CairnUserBundle:Banknote:index.html.twig');
    }

    public function addBanknoteAction(Request $request)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $banknoteRepo = $em->getRepository('CairnUserBundle:Banknote');
        $statusRepo = $em->getRepository('CairnUserBundle:BanknoteStatus');
        $banknote = new Banknote();
        $form = $this->createForm(BanknoteType::class, $banknote);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $test = $banknoteRepo->findOneBy(array('number'=>$banknote->getNumber(),'value'=>$banknote->getValue()));
                if($test){
                    $session->getFlashBag()->add('error','Ce billet existe déjà, vous ne pouvez pas l\'ajouter');
                    return $this->redirectToRoute('cairn_user_banknote_edit',array('id'=>$test->getID()));
                }

                $requestedStatus = $banknote->getStatus();
                $status = $statusRepo->findOneBy(array('status'=>$requestedStatus->getStatus(),'exchangeOffice'=>$requestedStatus->getExchangeOffice()));
                if(! $status){
                    $em->persist($banknote->getStatus());
                }
                else{
                    $banknote->setStatus($status);
                } 
                $em->persist($banknote);
                $em->flush();

                $session->getFlashBag()->add('info','Le billet a bien été ajouté');
                return $this->redirectToRoute('cairn_user_banknote_view',array('id'=>$banknote->getID()));
            }
            else{
                $session->getFlashBag()->add('error','Le formulaire contient des données invalides');
            }
        }
        return $this->render('CairnUserBundle:Banknote:add.html.twig', array('form' => $form->createView()));
    }

    public function viewBanknoteAction(Request $request, $id)
    {
        $session = $request->getSession();
        $banknote = $this->getDoctrine()->getManager()->getRepository('CairnUserBundle:Banknote')->findOneBy(array('id'=>$id)); 
        if($banknote){
            return $this->render('CairnUserBundle:Banknote:view.html.twig',array('banknote'=>$banknote)); 
        }
        else{
            $session->getFlashBag()->add('error','Ce coupon-billet n\'a jamais été enregistré');
            return $this->redirectToRoute('cairn_user_banknote_home');
        }
    }
    public function multiformAction()
    {
        $form1 = $this->get('form.factory')->createNamedBuilder(TextType::class, 'form1name')
            ->add('foo', 'text')
            ->getForm();

        $form2 = $this->get('form.factory')->createNamedBuilder(TextType::class, 'form2name')
            ->add('bar', 'text')
            ->getForm();

        if('POST' === $request->getMethod()) {

            if ($request->request->has('form1name')) {
                return new Response('ok');
                // handle the first form
            }

            if ($request->request->has('form2name')) {
                // handle the second form
                return new Response('ko');
            }
        }

        return array(
            'form1' => $form1->createView(),
            'form2' => $form2->createView()
        );  
    }
    /**
     *@TODO : search for banknotes dynamically with javascript
     */
    public function searchBanknotesAction(Request $request)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();

        $banknoteRepo = $em->getRepository('CairnUserBundle:Banknote');
        //        $form = $this->createForm(SearchBanknoteType::class);

        $form = $this->createFormBuilder()
            ->add('number', IntegerType::class, array('label' => 'N°'))
            ->getForm();

        $form->handleRequest($request);
        if($request->isMethod('POST')){
            if($form->isValid()){
                $re = '#^[1-9][0-9]*$#';
                $str = $form->get('number')->getData();
                preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

                if(count($matches) >= 1){
                    $banknote = $banknoteRepo->findOneBy(array('number'=>$matches[0][0]));
                    if($banknote){
                        return $this->redirectToRoute('cairn_user_banknote_view',array('id'=>$banknote->getId()));
                    }
                    else{
                        $session->getFlashBag()->add('error','Impossible de trouver le billet');
                    }
                }
            }
        }
        $banknotes = $banknoteRepo->findAll();
        return $this->render('CairnUserBundle:Banknote:search.html.twig', array('form' => $form->createView(),'banknotes'=>$banknotes));

    }
    public function editBanknoteAction(Request $request, $id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $statusRepo = $em->getRepository('CairnUserBundle:BanknoteStatus');
        $banknoteRepo = $em->getRepository('CairnUserBundle:Banknote');
        $banknote = $banknoteRepo->findOneBy(array('id'=>$id));
        $form = $this->createForm(BanknoteType::class, $banknote);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $requestedStatus = $banknote->getStatus();
                $status = $statusRepo->findOneBy(array('status'=>$requestedStatus->getStatus(),'exchangeOffice'=>$requestedStatus->getExchangeOffice()));
                if(! $status){
                    $em->persist($banknote->getStatus());
                }
                else{
                    $banknote->setStatus($status);
                } 
                $em->persist($banknote);
                $em->flush();

                $session->getFlashBag()->add('info','Le billet a bien été mis à jour');
                return $this->redirectToRoute('cairn_user_banknote_view',array('id'=>$banknote->getID()));
            }
            else{
                $session->getFlashBag()->add('error','Le formulaire contient des données invalides');
            }
        }
        return $this->render('CairnUserBundle:Banknote:edit.html.twig', array('form' => $form->createView()));
    }

}
