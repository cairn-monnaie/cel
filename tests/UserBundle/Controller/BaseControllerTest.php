<?php

namespace Tests\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\ProductManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;


use Cyclos;

class BaseControllerTest extends WebTestCase
{
    protected $client;
    protected $container;
    protected $scriptManager;
    protected $userManager;
    protected $productManager;

    protected $em;
    protected $testAdmin;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->client = static::createClient();

        $this->container = $this->client->getContainer();
        $this->scriptManager = new ScriptManager();
        $this->userManager = new UserManager();

        $this->em = $this->container->get('doctrine.orm.entity_manager');                          
        $this->testAdmin = 'admin_network';
    }


    public function login($username,$password)
    {
        $crawler = $this->client->request('GET','/logout');
        $crawler = $this->client->request('GET','/login');


        $form = $crawler->selectButton('_submit')->form();
        $form['_username']->setValue($username);
        $form['_password']->setValue($password);
        $crawler = $this->client->submit($form);

        return $this->client->followRedirect();

    }

    public function inputCardKey($crawler, $key)
    {
        $form = $crawler->selectButton('Valider')->form();
        $form['card[field]']->setValue($key);
        return $this->client->submit($form);
    }


    public function provideReferentsAndTargets()
    {

        $adminUsername = $this->testAdmin;
        return array(
            array('referent'=>$adminUsername,'target'=>$adminUsername,'isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'DrDBrew','isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'NaturaVie','isReferent'=>false)
        );
    }


}
