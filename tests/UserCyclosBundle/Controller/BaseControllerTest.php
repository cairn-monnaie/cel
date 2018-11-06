<?php

namespace Tests\UserCyclosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\ProductManager;


use Cyclos;

class BaseControllerTest extends WebTestCase
{
    protected $client;
    protected $container;
    protected $userManager;
    protected $productManager;

    protected $em;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = static::createClient();

        $this->container = $this->client->getContainer();
        $this->userManager = new UserManager();
        $this->productManager = new ProductManager();

    }


    public function login($username,$password)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

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
        $form = $crawler->selectButton('card_save')->form();
        $form['card[field]']->setValue($key);
        return $this->client->submit($form);
    }


}
