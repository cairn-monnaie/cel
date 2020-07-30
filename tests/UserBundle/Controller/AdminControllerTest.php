<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;
use Cairn\UserBundle\Entity\Operation;

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

        $crawler = $this->client->request('GET','/user/block/'.$targetUser->getUsername());

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(! $isReferent){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{

            if(! $targetUser->isEnabled()){
                $crawler = $this->client->followRedirect();
                $this->assertSame(1,$crawler->filter('html:contains("déjà bloqué")')->count());

                $this->assertUserIsDisabled($targetUser, false);
            }else{
                $this->client->enableProfiler();

                $form = $crawler->selectButton('confirmation_save')->form();
                $crawler =  $this->client->submit($form);

                $this->em->refresh($targetUser);

                //assert email
                $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
                $this->assertSame(1, $mailCollector->getMessageCount());
                $message = $mailCollector->getMessages()[0];
                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('Opposition', $message->getSubject());
                $this->assertContains('opposition', $message->getBody());
                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($targetUser->getEmail(), key($message->getTo()));

                $crawler = $this->client->followRedirect();

                //assert targetUser can't connect
                $crawler = $this->login($target, '@@bbccdd');
                $this->assertContains('login_check',$this->client->getResponse()->getContent());

                $this->assertUserIsDisabled($targetUser, true);
            }
        }

    }

    public function provideDataForBlock()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'valid + has sms enabled' => array('referent'=>$adminUsername,'target'=>'maltobar','isReferent'=>true),
           'already blocked' => array('referent'=>$adminUsername,'target'=>'tout_1_fromage','isReferent'=>true),
            'not referent'    =>array('referent'=>$adminUsername,'target'=>'NaturaVie','isReferent'=>false),
            'adherent for himself'    =>array('referent'=>'apogee_du_vin','target'=>'apogee_du_vin','isReferent'=>true),
            'adherent for other'    =>array('referent'=>'apogee_du_vin','target'=>'maltobar','isReferent'=>false),
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

        $crawler = $this->client->request('GET','/admin/users/activate/'.$targetUser->getUsername());

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(! $isReferent){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            if($targetUser->isEnabled()){
                $crawler = $this->client->followRedirect();
                $this->assertSame(1,$crawler->filter('html:contains("déjà accessible")')->count());

                $this->assertUserIsEnabled($targetUser, false);
            }else{
                $this->client->enableProfiler();

                $form = $crawler->selectButton('confirmation_save')->form();
                $crawler =  $this->client->submit($form);

                $this->em->refresh($targetUser);
                $userVO = $this->container->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($targetUser);
                $this->assertNotEquals($userVO, NULL);

                //assert email
                $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
                $this->assertSame($nbEmails, $mailCollector->getMessageCount());
                $message = $mailCollector->getMessages()[0];
                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('activé', $message->getSubject());
                $this->assertContains('ouvert', $message->getBody());
//                $this->assertContains($currentUser->getName(), $message->getBody());
                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($targetUser->getEmail(), key($message->getTo()));

                $crawler = $this->client->followRedirect();

                //assert targetUser can connect
                $crawler = $this->login($targetUser->getUsername(), '@@bbccdd');
                $this->assertSame(1,$crawler->filter('html:contains("Espace")')->count());

                $this->assertUserIsEnabled($targetUser,true);

            }
        }

    }

    public function provideDataForActivation()
    {
        $adminUsername = $this->testAdmin;

        return array(
           'valid, already log in'           => array('referent'=>$adminUsername,'target'=>'tout_1_fromage','isReferent'=>true,1),
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

        $crawler = $this->client->request('GET','/user/referents/assign/'.$targetUser->getUsername());

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

    /**
     *
     *@dataProvider provideCyclosOperationToSync
     */
    public function testCyclosOperationToSync($current, $isExpectedForm, $symfonyPersistedID, $cyclosPersistedID, $type, $reason)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $operationRepo = $this->em->getRepository('CairnUserBundle:Operation');

        $ob = $operationRepo->createQueryBuilder('o');
        $ob->where('o.paymentID is not NULL');
        $operationRepo->whereType($ob, $type);
        $targetOperation = $ob->getQuery()->getResult()[0];

        $cyclosID = $targetOperation->getPaymentID();

        if(! $symfonyPersistedID){
            $this->em->remove($targetOperation);
            $this->em->flush();
        }

        if(! $cyclosPersistedID){
            $cyclosID = '1';
        }

        $crawler = $this->client->request('GET','/admin/operation/sync');

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(! $isExpectedForm){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{

            $form = $crawler->selectButton('form_save')->form();
            $form['form[payment_id]']->setValue($cyclosID);
            $form['form[reason]']->setValue($reason);
            $form['form[type]']->select($type);

            $crawler = $this->client->submit($form);

            $crawler = $this->client->followRedirect();

            if( (! $symfonyPersistedID) && ($cyclosPersistedID)) { //valid case
                $this->assertSame(1,$crawler->filter('html:contains("succès")')->count());
                $newOperation = $operationRepo->findOneByPaymentID($cyclosID);

                $this->assertNotNull($newOperation);
                $this->assertEquals($newOperation->getType(), $type);
                return;
            }elseif($symfonyPersistedID){ //synchronization has been done correctly, nothing to sync  
                $this->assertSame(1,$crawler->filter('html:contains("existe déjà")')->count());
                return;
            } elseif(! $cyclosPersistedID){ //no cyclos data  
                $newOperation = $operationRepo->findOneByPaymentID($cyclosID);

                $this->assertNull($newOperation);
                $this->assertSame(1,$crawler->filter('html:contains("introuvable")')->count());
                return;
            }

        }
    }

    public function provideCyclosOperationToSync()
    {
        $type = Operation::TYPE_DEPOSIT;
        $admin = $this->testAdmin;

        $reason = 'Dépôt';

        return array(
            'invalid : no access for adherent' => array('mazmax',false,false,false,$type,$reason),
            'invalid : no access for simple admins' => array('gl_grenoble',false,false,false,$type,$reason),
            'invalid : paymentID already exists on Symfony side' => array($admin,true, true, true, $type, $reason),
            'invalid : paymentID does not exist on Cyclos side' => array($admin, true, false, false, $type, $reason),
            'valid synchronization' => array($admin, true, false, true, $type, $reason)
        );

    }

}
