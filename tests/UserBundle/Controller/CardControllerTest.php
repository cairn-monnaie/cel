<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;

use Cyclos;

class CardControllerTest extends BaseControllerTest
{

    function __construct(){
        parent::__construct();
    }


    /**
     *_currentUser wants card action for itself
     *_currentUser is referent of another user
     *_currentUser is not referent
     */
    public function testCardOperations()
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
        $this->client->followRedirects();

        $this->login('DrDBrew','@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
        $targetOption = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'cafeEurope'));

        $crawler = $this->client->request('GET','/card/home/'.$currentUser->getID());
        $this->assertSame(1,$crawler->filter('html:contains("Révoquer")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Commander")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Activer")')->count());
        $this->assertSame(0,$crawler->filter('html:contains("Générer")')->count());

        $this->login('mazouthm','admin');

        $crawler = $this->client->request('GET','/card/home/'.$currentUser->getID());
        $this->assertSame(1,$crawler->filter('html:contains("Révoquer")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Commander")')->count());
        $this->assertSame(0,$crawler->filter('html:contains("Activer la carte")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Générer la carte")')->count());

        $crawler = $this->client->request('GET','/card/home/'.$targetOption->getID());
        $this->assertSame(0,$crawler->filter('html:contains("Révoquer")')->count());
        $this->assertSame(0,$crawler->filter('html:contains("Commander")')->count());
        $this->assertSame(0,$crawler->filter('html:contains("Activer la carte")')->count());
        $this->assertSame(0,$crawler->filter('html:contains("Générer la carte")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("pas référent")')->count());

    }

    /**
     *_ROLE_PRO tries to generate card
     *_ADMIN tries to generate its own card
     *_ADMIN tries to generate non referent Pro's card
     *_ADMIN generates pro' card under its responsibility
     */
    public function testGenerateCard()
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
        $this->client->followRedirects();

        $crawler = $this->login('mazouthm','admin');
//        $this->assertSame(1,$crawler->filter('html:contains("Espace Administrateur")')->count());

        $targetOption = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
//        $crawler = $this->client->request('GET','/card/generate/?id=4');
        $this->assertTrue($client->getResponse()->isRedirect());



//        $this->assertSame(1,$crawler->filter('html:contains("carte de sécurité")')->count());
//        $form = $crawler->selectButton('card_save')->form();
//        $form['card[field]']->setValue('1111');
//        $crawler = $this->client->submit($form);

//        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
////
//        $this->assertSame(1,$crawler->filter('html:contains("déjà")')->count());
//     
//        $targetOption = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'cafeEurope'));
//        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
//        $this->assertSame(1,$crawler->filter('html:contains("pas référent de")')->count());
//
//        $this->login('cafeEurope','@@bbccdd');
//        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
//        $this->assertSame(1,$crawler->filter('html:contains("access denied")')->count());

    }
    
    public function testCheckDelayedCards()
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
        $this->client->followRedirects();

        $this->login('mazouthm','admin');
        $crawler = $this->client->request('GET','/card/check/delayed');

        //TODO : control users with deadline missed
    }
}

