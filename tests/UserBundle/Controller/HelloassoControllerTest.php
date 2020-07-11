<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\HelloassoConversion;


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
                $this->assertEquals(400,$statusCode);
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
    public function testHelloassoSync($current, $isExpectedForm, $symfonyPersistedID, $helloassoPersistedID, $isValidEmail)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $helloassoRepo = $this->em->getRepository('CairnUserBundle:HelloassoConversion');

        $conversion = $helloassoRepo->findAll()[0];
        $helloassoID = $conversion->getPaymentID();

        if($isValidEmail){
            $email = $conversion->getEmail();
        }else{
            $email = 'xyz@xy.com';
        }
        
        if(! $symfonyPersistedID){
            $this->em->remove($conversion);
            $this->em->flush();
        }

        if(! $helloassoPersistedID){
            $helloassoID = '1';
        }

        $crawler = $this->client->request('GET','/admin/helloasso/sync');

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(! $isExpectedForm){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{

            $form = $crawler->selectButton('form_sync')->form();
            $form['form[payment_id]']->setValue($helloassoID);
            $form['form[email]']->setValue($email);

            $crawler = $this->client->submit($form);

            $crawler = $this->client->followRedirect();

            if( (! $symfonyPersistedID) && ($helloassoPersistedID)) { //valid case
                if($isValidEmail){
                    $this->assertSame(1,$crawler->filter('html:contains("succès")')->count());
                    $newConversion = $helloassoRepo->findOneByPaymentID($helloassoID);

                    $this->assertNotNull($newConversion);
                    $this->assertEquals($newConversion->getEmail(), $email);
                    return;
                }else{
                    $newConversion = $helloassoRepo->findOneByPaymentID($helloassoID);

                    $this->assertNull($newConversion);
                    $this->assertSame(1,$crawler->filter('html:contains("ne correspond à aucun compte")')->count());
                    return;
                }
            }elseif($symfonyPersistedID){ //synchronization has been done correctly, nothing to sync  
                $this->assertSame(1,$crawler->filter('html:contains("déjà été")')->count());
                return;
            } elseif(! $helloassoPersistedID){ //no helloasso data  
                $newConversion = $helloassoRepo->findOneByPaymentID($helloassoID);

                $this->assertNull($newConversion);
                return;
            }

        }
    }

    public function provideDataForHelloassoSync()
    {
        $admin = $this->testAdmin;

        return array(
            'invalid : no access for adherent' => array('mazmax',false,false,false,false),
            'invalid : no access for simple admins' => array('gl_grenoble',false,false,false,false),
            'invalid : paymentID already exists on Symfony side' => array($admin,true, true, true, true),
            'invalid : paymentID does not exist on Helloasso side' => array($admin, true, false, false, false),
            'invalid : paymentID valid but email invalid' => array($admin, true, false, false, false),
            'valid synchronization' => array($admin, true, false, true, true)
        );

    }

    

}

