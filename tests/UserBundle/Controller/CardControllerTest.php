<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Cyclos;

class CardControllerTest extends BaseControllerTest
{

    function __construct(){
        parent::__construct();
    }


    /**
     *_$currentUser wants card action for itself :
     *                          .installed superadmin : revoke / order / activate / generate
     *                          .otherwise : revoke / order / activate
     *_$currentUser wants card action for $toUser :
     *                          .isGranted ROLE_LOCAL_GROUP : 
     *                              *if $currentUser is referent of $toUser : revoke / order / generate
     *                              *otherwise : nothing(should not even see card_operations) 
     *
     */
    public function testCardOperations($couple)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
        $this->client->followRedirects();
        $this->client->catchExceptions(false);
        //logged as installed super admin
        $crawler = $this->login($couple[0][0], $couple[0][1]);


        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'mazouthm'));

        $targetOption1 = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
        $targetOption2 = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'cafeEurope'));

        //action for itself
        $crawler = $this->client->request('GET','/card/home/'.$currentUser->getID());
        if($currentUser->getUsername() == $this->container->getParameter('cyclos_global_admin_username')){
            $this->assertSame(4,$crawler->filter('a.operation_link')->count());
        }else{
            $this->assertSame(3,$crawler->filter('a.operation_link')->count());
        }

        //action for someone else
        if($targetOption2->hasReferent($currentUser)){
            $crawler = $this->client->request('GET','/card/home/'.$targetOption1->getID());
            $this->assertSame(3,$crawler->filter('a.operation_link')->count());
        }
        else{
            $this->expectException(AccessDeniedException::class);
            $crawler = $this->client->request('GET','/card/home/'.$targetOption2->getID());
            $this->assertSame(1,$crawler->filter('html:contains("pas référent")')->count());
        }
    }

    public function provideReferentsAndUsers()
    {
        return array(
            array(array('mazouthm','admin'),'DrDBrew'),
            array(array('mazouthm','admin'),'cafeEurope')
        );
    }

    /**
     *_ROLE_PRO tries to generate card
     *_ADMIN tries to generate its own card
     *_ADMIN tries to generate non referent Pro's card
     *_ADMIN generates pro' card under its responsibility
     */
    //    public function testGenerateCard()
    //    {
    //      $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
    //      $this->client->followRedirects();
    //
    //      $crawler = $this->login('mazouthm','admin');
    //        $this->assertSame(1,$crawler->filter('html:contains("Espace Administrateur")')->count());
    //
    //      $targetOption = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
    //      $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
    //        $crawler = $this->client->request('GET','/card/generate/?id=4');
    //      $this->assertTrue($client->getResponse()->isRedirect());
    //
    //
    //
    //        $this->assertSame(1,$crawler->filter('html:contains("carte de sécurité")')->count());
    //        $form = $crawler->selectButton('card_save')->form();
    //        $form['card[field]']->setValue('1111');
    //        $crawler = $this->client->submit($form);
    //
    //        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
    //
    //        $this->assertSame(1,$crawler->filter('html:contains("déjà")')->count());
    //     
    //        $targetOption = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'cafeEurope'));
    //        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
    //        $this->assertSame(1,$crawler->filter('html:contains("pas référent de")')->count());
    //
    //        $this->login('cafeEurope','@@bbccdd');
    //        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
    //        $this->assertSame(1,$crawler->filter('html:contains("access denied")')->count());
    //
    //  }
    //  
    //  public function testCheckDelayedCards()
    //  {
    //      $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
    //      $this->client->followRedirects();
    //
    //      $this->login('mazouthm','admin');
    //      $crawler = $this->client->request('GET','/card/check/delayed');
    //
    //      //TODO : control users with deadline missed
    //    }
}

