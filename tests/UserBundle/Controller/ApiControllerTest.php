<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class ApiControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     *
     *
     */
    public function testGetPhones(){

        $this->mobileLogin('gjanssens','@@bbccdd');
        $crawler = $this->client->request('GET','/mobile/phones');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

    }


}
