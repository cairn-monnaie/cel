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
     * @dataProvider provideDataForBlock
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
                $this->assertSame(1,$crawler->filter('html:contains("déjà bloqué")')->count());
            }else{
                $this->client->enableProfiler();

                $form = $crawler->selectButton('confirmation_save')->form();
                $crawler =  $this->client->submit($form);

                $this->em->refresh($targetUser);
                $this->assertFalse($targetUser->isEnabled());

                if($smsData = $targetUser->getSmsData()){
                    $this->assertFalse($smsData->isSmsEnabled());
                }
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

                //assert targetUser can't connect
                $crawler = $this->login($targetUser->getUsername(), '@@bbccdd');
            }
        }

    }

    public function provideDataForBlock()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'valid + has sms enabled'           => array('referent'=>$adminUsername,'target'=>'maltobar','isReferent'=>true),
           'already blocked' => array('referent'=>$adminUsername,'target'=>'tout_1_fromage','isReferent'=>true),
            'not referent'    =>array('referent'=>$adminUsername,'target'=>'NaturaVie','isReferent'=>false)
        );
    }

    /**
     * @dataProvider provideDataForActivation
     */
    public function testActivateUser($referent,$target, $isReferent, $nbEmails)
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
                $this->assertSame($nbEmails, $mailCollector->getMessageCount());
                $message = $mailCollector->getMessages()[0];
                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('activé', $message->getSubject());
                $this->assertContains('accessible', $message->getBody());
                $this->assertContains($currentUser->getName(), $message->getBody());
                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($targetUser->getEmail(), key($message->getTo()));

                $crawler = $this->client->followRedirect();

                //assert targetUser can connect
                $crawler = $this->login($targetUser->getUsername(), '@@bbccdd');
                $this->assertSame(1,$crawler->filter('html:contains("Espace Professionnel")')->count());

            }
        }

    }

    public function provideDataForActivation()
    {
        $adminUsername = $this->testAdmin;

        return array(
           'valid'           => array('referent'=>$adminUsername,'target'=>'tout_1_fromage','isReferent'=>true,2),
           'already activated' => array('referent'=>$adminUsername,'target'=>'labonnepioche','isReferent'=>true,0),
           'not referent'    =>array('referent'=>$adminUsername,'target'=>'NaturaVie','isReferent'=>false,0)
        );
    }


    /**
     *
     *@dataProvider provideReferentsToAssign
     */
    public function testAssignReferent($referent, $target, $isValid, $isPro, $expectKey)
    {
        $adminUsername = $this->testAdmin;
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

                if($referentUser){
                    $this->assertTrue($targetUser->hasReferent($referentUser));
                }else{
                    $this->assertEquals($targetUser->getLocalGroupReferent(),NULL);
                }
            }else{
            }    
        }

    }

    public function provideReferentsToAssign()
    {
        return array(
            'invalid target(GL)' => array('referent'=>'gl_grenoble','target'=>'gl_voiron','isValid'=>false,'isPro'=>false,'expectKey'=>''),
            'invalid target(person)' => array('referent'=>'gl_grenoble','target'=>'tous_andre','isValid'=>false,
                                              'isPro'=>false,'expectKey'=>''),
            'valid assignation' => array('referent'=>'gl_grenoble','target'=>'labonnepioche','isValid'=>true,'isPro'=>true,
                                         'expectKey'=>'success'),
            'referent already assigned' => array('referent'=>'gl_grenoble','target'=>'episol','isValid'=>true,'isPro'=>true,
                                                 'expectKey'=>'info'),
        );

    }

}
