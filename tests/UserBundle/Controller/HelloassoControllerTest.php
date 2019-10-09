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
        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $username = 'mazmax';

        //login to be able to access cyclos data (account balance before & after ) 
        $crawler = $this->login($username, '@@bbccdd');

        $creditorUser = $userRepo->findOneBy(array('username'=>$username));
        $creditorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($creditorUser->getCyclosID())[0];

        $creditorICC = $creditorAccount->number;
        $accountBalanceBefore = $creditorAccount->status->balance;

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

        //WARNING : you must get the status code before login otherwise POST response data is lost
        $statusCode =  $this->client->getResponse()->getStatusCode();

        //login to be able to access cyclos data (account balance before & after ) 
        $crawler = $this->login($username, '@@bbccdd');

        $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($creditorUser->getCyclosID())[0];
        $accountBalanceAfter = $ownerAccount->status->balance;

        if($isValidID){
            if($isAlreadyHandled){
                $this->assertEquals(403,$statusCode);
                $this->assertTrue($accountBalanceAfter == $accountBalanceBefore);
                return;
            }
            if($isValidEmail){
                $this->assertEquals(200, $statusCode);
                $this->assertTrue($accountBalanceAfter > $accountBalanceBefore);
                return;
            }

            $this->assertEquals(404, $statusCode);
            $this->assertTrue($accountBalanceAfter == $accountBalanceBefore);
            return;
        }else{
            $this->assertEquals(404, $statusCode);
            $this->assertTrue($accountBalanceAfter == $accountBalanceBefore);
        }
    }

    public function provideDataForHelloassoNotification()
    {
        return array(
            'valid notification ' => array('helloassoID'=>'000040780773', true, false,true),
            'invalid notification : invalid ID' => array('helloassoID'=>'1', false, false, false),
            'invalid notification : user not found' => array('helloassoID'=>'000043036883', true, false, false),
            'invalid notification : already handled' => array('helloassoID'=>'000040877783', true, true, true),
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

