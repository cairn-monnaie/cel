<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Cairn\UserBundle\Entity\User;


class HelloassoControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     *
     *@dataProvider provideDataForHelloassoNotification
     */
    public function testHelloassoNotification($helloassoID, $isValidID, $isAlreadyHandled, $isValidEmail)
    {
        $user = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'speedy_andrew'));

        $user->setEmail('maxime.mazouth-laurol@cairn-monnaie.com');
        $this->client->request(
            'POST',
            '/helloasso/notification',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            http_build_query(
                array(
                    'id'=>$helloassoID, // only parameter that matters
                    'amount'=>10,
                    'payer_first_name'=>'Jean',
                    'payer_last_name'=>'Valjean',

                )
            )
        );

        if($isValidID){
            if($isAlreadyHandled){
                $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
                return;
            }elseif($isValidEmail){
                $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
                return;
            }else{
                $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
                return;
            }
        }else{
            $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        }
    }

    public function provideDataForHelloassoNotification()
    {
        return array(
            'valid notification ' => array('helloassoID'=>'000040780773', true, false,true),
            'invalid notification : user not found' => array('helloassoID'=>'000043036883', false, true, false),
            'invalid notification : invalid ID' => array('helloassoID'=>'1', false, false, false),
            'invalid notification : already handled' => array('helloassoID'=>'000040877783', true, false, true),
        );

    }

    /**
     *
     *@dataProvider provideDataForHelloassoSync
     */
    public function testHelloassoSync()
    {
        
    }

    public function provideDataForHelloassoSync()
    {
        return array(

        );

    }


}

