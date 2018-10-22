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
    function __construct($name = NULL, array $data = array(), $dataName = ''){
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @depends testValidateCard
     * @dataProvider provideReferentsAndTargets2
     */
    public function testBlockUser($referent,$target)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($referent[0], $referent[1]);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','/admin/users/block/'.$targetUser->getID());

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(!$targetUser->hasReferent($currentUser)){
            //access denied exception
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            $this->client->enableProfiler();

            $form = $crawler->selectButton('confirmation_save')->form();
            $crawler =  $this->client->submit($form);

            $this->em->refresh($targetUser);
            $this->assertTrue(!$targetUser->isEnabled());

            //assert email
            $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(1, $mailCollector->getMessageCount());
            $message = $mailCollector->getMessages()[0];
            $this->assertInstanceOf('Swift_Message', $message);
            $this->assertContains('désactivé', $message->getSubject());
            $this->assertContains('bloqué', $message->getBody());
            $this->assertContains($currentUser->getName(), $message->getBody());
            $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
            $this->assertSame($targetUser->getEmail(), key($message->getTo()));

            //assert targetUser can't connect
            $crawler = $this->login($targetUser->getUsername(), '@@bbccdd');
            $this->assertSame(1,$crawler->filter('html:contains("Account is disabled")')->count());
        }

    }


    /**
     * @depends testValidateCard
     * @dataProvider provideReferentsAndTargets2
     */
    public function testActivateUser($referent,$target)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($referent[0], $referent[1]);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','/admin/users/activate/'.$targetUser->getID());

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(!$targetUser->hasReferent($currentUser)){
            //access denied exception
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            $this->client->enableProfiler();

            $form = $crawler->selectButton('confirmation_save')->form();
            $crawler =  $this->client->submit($form);

            $this->em->refresh($targetUser);
            $this->assertTrue($targetUser->isEnabled());

            //assert email
            $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(1, $mailCollector->getMessageCount());
            $message = $mailCollector->getMessages()[0];
            $this->assertInstanceOf('Swift_Message', $message);
            $this->assertContains('désactivé', $message->getSubject());
            $this->assertContains('bloqué', $message->getBody());
            $this->assertContains($currentUser->getName(), $message->getBody());
            $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
            $this->assertSame($targetUser->getEmail(), key($message->getTo()));

            //assert targetUser can connect
            $crawler = $this->login($targetUser->getUsername(), '@@bbccdd');
            $this->assertSame(1,$crawler->filter('html:contains("Accueil")')->count());
        }

    }

//    /**
//     *
//     *depends testRegistration
//     */
//    public function testAssignReferent()
//    {
//        ;
//    }

//    //test that after registration + email confirmation, user is disabled
//    //not randomly(define criteria), a registered user will confirm its email or not
//    /**
//     *depends testRegistration
//     */
//    public function testCheckEmailsValidation()
//    {
//        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
//
//        $login = $this->container->getParameter('cyclos_global_admin_username');
//        $password = '@@bbccdd';
//        $crawler = $this->login($login, $password);
//
//
//        $crawler = $this->client->request('GET','user/email/check/expiration/');
//
//        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'response status is 2xx');
//
//    }

}
