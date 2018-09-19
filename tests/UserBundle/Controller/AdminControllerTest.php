<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;

use Cyclos;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminControllerTest extends BaseControllerTest
{

    function __construct(){
        parent::__construct();
    }

    public function testBlockUser()
    {
        $crawler = $this->login('mazouthm','admin');

        $targetOption1 = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'MaltOBar'));
        $targetOption2 = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'cafeEurope'));

        //security card is asked
        $crawler = $this->client->request('GET', '/admin/users/block/?id='.$targetOption1->getID());
        $this->client->followRedirect();   

//        $this->assertSame(1,$crawler->filter('html:contains("carte de sécurité Cairn")')->count());
//       $form = $crawler->selectButton('card_save')->form();
//       $form['card[field]']->setValue('1111');
//       $crawler = $this->client->submit($form);
//
        $crawler = $this->login('MaltOBar','@@bbccdd');
         $this->client->followRedirect();   
         $this->assertSame(1,$crawler->filter('html:contains("Account is disabled")')->count());
       
         $crawler = $this->login('mazouthm','admin');
         $this->client->followRedirect();   

       $crawler = $this->client->request('GET', '/admin/users/block/?id='.$targetOption2->getID());
//         $this->assertSame(1,$crawler->filter('html:contains("pas référent de")')->count());
         $this->expectException(AccessDeniedException::class);
         $this->client->followRedirect();   

    }

    public function testActivateUser()
    {
        $this->client->followRedirects();   
        $crawler = $this->login('mazouthm','admin');

        $targetOption1 = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'MaltOBar'));
        $targetOption2 = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'cafeEurope'));

        $link = $crawler->filter('a:contains("membres")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("Malt")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("Autoriser")')->eq(0)->link();
        $crawler = $this->client->click($link);

//         $this->assertSame(1,$crawler->filter('html:contains("contenue")')->count());

        //security card is asked
//        $crawler = $this->client->request('GET', '/admin/users/activate/?id='.$targetOption1->getID());
        $crawler = $this->login('MaltOBar','@@bbccdd');
         $this->assertSame(1,$crawler->filter('html:contains("Account is disabled")')->count());
//        $this->assertTrue($client->getResponse()->isRedirect('/home/'));
    }


}
