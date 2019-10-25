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
     *@dataProvider provideDataForSmsData
     */
    public function testGetPhones($username, $isEmpty, $httpResponse){

        $this->mobileLogin($username,'@@bbccdd');
        $crawler = $this->client->request('GET','/mobile/phones');

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($isEmpty){
            $this->assertEquals($responseData, []);
        }else{
            foreach($responseData as $phone){
                $this->assertSerializedEntityContent($phone,'phone');
            }
        }
    }

    public function provideDataForSmsData()
    {
        return array(
            array('username'=>'gjanssens',true, Response::HTTP_OK),
            array('username'=>'nico_faus_prod',false, Response::HTTP_OK)
        );
        

    }
}
