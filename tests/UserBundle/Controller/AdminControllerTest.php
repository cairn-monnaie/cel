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

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }


    /**
     * depends testValidateCard
     * @dataProvider provideReferentsAndTargets
     */
    public function testBlockUser($referent,$target, $isReferent)
    {
        $crawler = $this->login($referent, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','/admin/users/block/'.$targetUser->getID());

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(! $isReferent){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            if(! $targetUser->isEnabled()){
                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-info')->count());    
                $this->assertSame(1,$crawler->filter('html:contains("déjà bloqué")')->count());
            }else{
                $this->client->enableProfiler();

                $form = $crawler->selectButton('confirmation_save')->form();
                $crawler =  $this->client->submit($form);

                $this->em->refresh($targetUser);
                $this->assertFalse($targetUser->isEnabled());

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

                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

                //assert targetUser can't connect
                $crawler = $this->login($targetUser->getUsername(), '@@bbccdd');
                $this->assertSame(0, $crawler->filter('li#id_welcome')->count());    
                $this->assertSame(1,$crawler->filter('html:contains("Account is disabled")')->count());
            }
        }

    }

    public function provideReferentsAndTargets()
    {
        $adminUsername = $this->getAdminUsername();

        return array(
            'valid'           => array('referent'=>$adminUsername,'target'=>'MaltOBar','isReferent'=>true),
           'already blocked' => array('referent'=>$adminUsername,'target'=>'MaltOBar','isReferent'=>true),
            'not referent'    =>array('referent'=>$adminUsername,'target'=>'cafeEurope','isReferent'=>false)
        );
    }

    /**
     * @depends testBlockUser
     * @dataProvider provideReferentsAndTargets
     */
    public function testActivateUser($referent,$target, $isReferent)
    {

        $crawler = $this->login($referent, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','/admin/users/activate/'.$targetUser->getID());

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(! $isReferent){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            if($targetUser->isEnabled()){
                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-info')->count());    
                $this->assertSame(1,$crawler->filter('html:contains("déjà accessible")')->count());
            }else{
                $this->client->enableProfiler();

                $form = $crawler->selectButton('confirmation_save')->form();
                $crawler =  $this->client->submit($form);

                $this->em->refresh($targetUser);
                $this->assertTrue($targetUser->isEnabled());

                $userVO = $this->container->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($targetUser);
                $this->assertNotEquals($userVO, NULL);

                //assert email
                $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
                $this->assertSame(1, $mailCollector->getMessageCount());
                $message = $mailCollector->getMessages()[0];
                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('activé', $message->getSubject());
                $this->assertContains('accessible', $message->getBody());
                $this->assertContains($currentUser->getName(), $message->getBody());
                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($targetUser->getEmail(), key($message->getTo()));

                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

                //assert targetUser can connect
                $crawler = $this->login($targetUser->getUsername(), '@@bbccdd');
                $this->assertSame(1, $crawler->filter('li#id_welcome')->count());    
            }
        }

    }


    /**
     *
     *depends testRegistration
     *@dataProvider provideReferentsToAssign
     */
    public function testAssignReferent($referent, $target, $isValid, $isPro, $expectKey)
    {
        $adminUsername = $this->getAdminUsername();
        $crawler = $this->login($adminUsername, '@@bbccdd');

        //can be null
        $referentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','/user/referents/assign/'.$targetUser->getID());

        if(!$isPro){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            $this->assertSame(1,$crawler->filter('html:contains("Seuls les professionnels")')->count());
        }else{

            $form = $crawler->selectButton('form_save')->form();
            $form['form[singleReferent]']->select($referent);
            $crawler = $this->client->submit($form);

            $crawler = $this->client->followRedirect();
            if($isValid){
                $this->assertSame(1, $crawler->filter('div.alert-'.$expectKey)->count());    

                if($referentUser){
                    $this->assertTrue($targetUser->hasReferent($referentUser));
                }else{
                    $this->assertEquals($targetUser->getLocalGroupReferent(),NULL);
                }
            }else{
                $this->assertSame(1, $crawler->filter('div.alert-'.$expectKey)->count());    
                $this->assertSame(0, $crawler->filter('div.alert-success')->count());    
            }    
        }

    }

    public function provideReferentsToAssign()
    {
        return array(
            'invalid target' => array('referent'=>'glGrenoble','target'=>'glVoiron','isValid'=>false,'isPro'=>false,'expectKey'=>''),
            'valid assignation' => array('referent'=>'glGrenoble','target'=>'LaBonnePioche','isValid'=>true,'isPro'=>true,'expectKey'=>'success'),
            'useless assignation' => array('referent'=>'','target'=>'LaDourbie','isValid'=>true,'isPro'=>true,'expectKey'=>'info'),
            'invalid assignation' => array('referent'=>'glGrenoble','target'=>'LaBonnePioche','isValid'=>false,'isPro'=>true,'expectKey'=>'info'),
        );

    }

}
