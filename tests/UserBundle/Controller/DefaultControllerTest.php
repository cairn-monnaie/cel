<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;

use Cyclos;

class DefaultControllerTest extends BaseControllerTest
{

    function __construct(){
        parent::__construct();
    }

    public function testInstall()
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
        $this->client->followRedirects();

        $crawler = $this->client->request('GET','/install/');
       $this->assertSame(1,$crawler->filter('html:contains("already exists")')->count());
//        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->login('DrDBrew','@@bbccdd');
        $link = $crawler->filter('a:contains("Profil")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $crawler = $this->client->request('GET','/install/');
        $this->assertTrue($this->client->getResponse()->isRedirect('/home/'));

        print_r($this->client->getResponse()->getContent());

        $this->assertSame(1,$crawler->filter('html:contains("Espace Professionnel")')->count());
       $this->assertSame(1,$crawler->filter('html:contains("already exists")')->count());


    }

    public function testRegistration()
    {

    }
}
