<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Cyclos;

class CardControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = ''){
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @TODO : tester directement la présence des urls
     *_$referent wants card action for himself :
     *                          .installed superadmin : revoke / order / activate / generate
     *                          .otherwise : revoke / order / activate
     *_$referent wants card action for $target :
     *                          .isGranted ROLE_ADMIN : 
     *                              *if $referent is referent of $target : revoke / order / generate
     *                              *otherwise : nothing(should not even see card_operations) 
     * @dataProvider provideCardOperationsData
     */
    public function testCardOperations($referent,$target,$isReferent,$nbLinks)
    {
        $crawler = $this->login($referent, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','/card/home/'.$targetUser->getID());

        if($currentUser !== $targetUser && !$isReferent){
                $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
                $this->assertSame($nbLinks,$crawler->filter('a.operation_link')->count());
        }

    }

    public function provideCardOperationsData()
    {
        $adminUsername = $this->testAdmin;
        return array(
            'superadmin for himself'=>array('referent'=>$adminUsername,'target'=>$adminUsername,'isReferent'=>true,2),
            'referent for non generated card'=>array('referent'=>$adminUsername,'target'=>'DrDBrew','isReferent'=>true,3),
            'non referent'=> array('referent'=>$adminUsername,'target'=>'vie_integrative','isReferent'=>false,0),
            'pro for himself'=> array('referent'=>'vie_integrative','target'=>'vie_integrative','isReferent'=>false,1),
            'non referent'=>array('referent'=>'vie_integrative','target'=>'DrDBrew','isReferent'=>false,0),
            'pro with not generated card'=>array('referent'=>'DrDBrew','target'=>'DrDBrew','isReferent'=>false,1),
            'pro with no card'=>array('referent'=>'episol','target'=>'episol','isReferent'=>false,1)
        );
    }

    /**
     *Tests the card validation using a key
     *@dataProvider provideUsersForCardValidation
     */
    public function testValidateCard($login,$key, $expectForm, $expectValid)
    {
        $crawler = $this->login($login, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));
        $crawler = $this->client->request('GET', '/card/validate');

        $card = $currentUser->getCard();

        if(!$expectForm){
            $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$currentUser->getID()));
            $crawler = $this->client->followRedirect();
            $this->assertSame(1, $crawler->filter('div.alert-info')->count());    

        }else{
            $crawler = $this->inputCardKey($crawler,$key);

            if(!$expectValid){
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/validate'));
                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-error')->count());    
   
                $this->em->refresh($currentUser);
                $this->assertSame(1,$currentUser->getCardKeyTries());
                $this->assertFalse($card->isEnabled());
            }else{
                $crawler = $this->client->followRedirect();
                $this->assertSame(0,$crawler->filter('html:contains("Activer la carte")')->count());
    
                $this->em->refresh($currentUser);
                $this->em->refresh($card);
    

                $this->assertSame(0,$currentUser->getCardKeyTries());
                $this->assertTrue($card->isEnabled());
                $this->assertSame(1, $crawler->filter('div.alert-success')->count());    
            }
        }
    }

    public function provideUsersForCardValidation()
    {
        return array(
            'invalid key'        => array('login'=>'recycleco','key'=>'5555','expectForm'=>true,'expectValidKey'=>false),
            'valid key'          => array('login'=>'recycleco','key'=>'1111','expectForm'=>true,'expectValidKey'=>true),
            'validated card'     => array('login'=>'vie_integrative','key'=>'1111','expectForm'=>false,'expectValidKey'=>true),
            'not generated card' => array('login'=>'DrDBrew', 'key'=>'1111','expectForm'=>false,'expectValidKey'=>false),
        );
    }

    /**
     *Tests if card intermediate step is reached for a  sensible operation
     *@dataProvider provideUsersWithValidatedCard
     */
    public function testCardSecurityLayer($login,$expectValid)
    {
        $crawler = $this->login($login, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));

        //any sensible operation (see Cairn/UserBundle/Event/SecurityEvents.php)
        $url = '/user/beneficiaries/add';
        $crawler = $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));

        $crawler = $this->client->followRedirect();

        $card = $currentUser->getCard();
        if(!$expectValid){
            $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$currentUser->getID()));
            $crawler = $this->client->followRedirect();
            $this->assertSame(1,$crawler->filter('html:contains("pas active")')->count());
            $this->assertSame(1, $crawler->filter('div.alert-info')->count());    

        }else{
            $crawler = $this->inputCardKey($crawler,'1111');
            $this->assertTrue($this->client->getResponse()->isRedirect($url));
        }
    }

    public function provideUsersWithValidatedCard()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'validated card'     => array('login'=>$adminUsername, 'expectValid'=>true),
            'not validated card' => array('login'=>'recycleco','expectValid'=>false),
        );
    }

    /**
     * if $current is installed super admin : can generate a card for himself
     * if $current is current of $target : can generate card
     * if $current is not current of $target : code 403
     * if $current does not have active card : redirection
     * if $target has a generated card : redirection
     *
     *@dataProvider provideUsersForCardGeneration
     */
    public function testGenerateCard($current, $target,$expectConfirm,$expectMessage)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        //sensible operation
        $crawler = $this->client->request('GET','/card/generate/'.$targetUser->getID());

        $card = $targetUser->getCard();

        if(! ($targetUser->hasRole('ROLE_SUPER_ADMIN') && $targetUser === $currentUser)){
            $crawler = $this->client->followRedirect();
            $crawler = $this->inputCardKey($crawler, '1111');
            $crawler = $this->client->followRedirect();
        }

        if(!$targetUser->hasReferent($currentUser)){
            //access denied exception
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
        }else{
            $card = $targetUser->getCard();
            if(!$expectConfirm){
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();
                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
                $this->assertSame(1, $crawler->filter('div.alert-info')->count());    
            }else{
                $form = $crawler->selectButton('confirmation_save')->form();
                $crawler =  $this->client->submit($form);

                $this->assertTrue($this->client->getResponse()->isRedirect('/card/download/'.$card->getID()));
                $crawler = $this->client->followRedirect();

                $this->em->refresh($card);
                $this->assertTrue($card->isGenerated());
                $this->assertNotEquals($card->getFields(),NULL);
                $this->assertTrue($this->client->getResponse()->headers->contains(
                        'Content-Type',
                        'application/pdf'));
            }
        }
    }

    public function provideUsersForCardGeneration()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'card already generated' => array('current'=>'labonnepioche','target'=>'labonnepioche','expectConfirm'=>false,
                                              'expectMessage'=>'déjà été générée'),             
            'successful generation' => array('current'=>$adminUsername,'target'=>'DrDBrew','expectConfirm'=>true,
                                             'expectMessage'=>'xxx'),             
            'not referent'          =>array('current'=>$adminUsername,'target'=>'vie_integrative','expectConfirm'=>false,
                                            'expectMessage'=>'pas référent'),             
            'no card'          =>array('current'=>$adminUsername,'target'=>'episol','expectConfirm'=>false,
                                            'expectMessage'=>'pas de carte de sécurité'),             

        );
    }

    /**
     * $target wants to revoke his card
     *_$current = $target || $current is referent of $target
     *                          . no card || card revoked : redirection to card home page
     *                          . card not revoked : password confirmation
     *                                                      . success password : session message with key "success" + card exists
     *                                                      . wrong password : try again
     *@dataProvider provideUsersForCardRevocation 
     */
    public function testRevokeCard($current, $target,$expectForm,$expectMessage,$emailSent)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        //sensible operation
        $url = '/card/revoke/'.$targetUser->getID();
        $crawler = $this->client->request('GET',$url);
        if($currentUser !== $targetUser){
            $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));
            $crawler = $this->client->followRedirect();
            $crawler = $this->inputCardKey($crawler, '1111');
            $crawler = $this->client->followRedirect();
        }

        if(! ($targetUser->hasReferent($currentUser) || $targetUser === $currentUser)){
            //access denied exception
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
        }else{
            $targetCard = $targetUser->getCard();
            if(!$expectForm){
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();
                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
                $this->assertSame(1, $crawler->filter('div.alert-info')->count());    

            }else{
                $this->client->enableProfiler();

                $form = $crawler->selectButton('confirmation_save')->form();
                $form['confirmation[current_password]']->setValue('@@bbccdd');
                $crawler =  $this->client->submit($form);
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));

                //assert card
                $this->em->refresh($targetUser);
                $this->assertEquals($targetUser->getCard(),NULL);

                $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

                if($emailSent){
                //assert email
                    $this->assertSame(1, $mailCollector->getMessageCount());
                    $message = $mailCollector->getMessages()[0];
                    $this->assertInstanceOf('Swift_Message', $message);
                    $this->assertContains('Révocation', $message->getSubject());
                    $this->assertContains('révocation', $message->getBody());
                    $this->assertContains($currentUser->getName(), $message->getBody());
                    $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                    $this->assertSame($targetUser->getEmail(), key($message->getTo()));
                }else{
                    $this->assertSame(0, $mailCollector->getMessageCount());
                }
                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

            }
        }
    }

    public function provideUsersForCardRevocation()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'revocation from ref'=> array('current'=>$adminUsername,'target'=>'labonnepioche','expectForm'=>true,'expectMessage'=>'xxx',
                                          'emailSent'=>true), 
            'self revocation'=> array('current'=>'labonnepioche','target'=>'labonnepioche','expectForm'=>true,'expectMessage'=>'xxx',
                                      'emailSent'=>false),             
            'revoc from non ref'=>array('current'=>$adminUsername,'target'=>'vie_integrative','expectForm'=>false,
                                        'expectMessage'=>'pas référent','emailSent'=>false),
            'no card to revoke'=>array('current'=>'episol','target'=>'episol','expectForm'=>false,
                                       'expectMessage'=>'déjà été révoquée','emailSent'=>false),
        );
    }

    /**
     * $target wants a new card
     *_$referent = $target || $referent is referent of $target
     *                          . card not revoked : redirection to card home page
     *                          . card revoked : password confirmation
     *                                                      . success password : session message with key "success" + card exists
     *                                                      . wrong password : try again
     * @dataProvider provideUsersForCardOrder
     */
    public function testOrderCard($current,$target,$expectForm, $expectMessage, $emailSent)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        //sensible operation
        $url = '/card/order/'.$targetUser->getID();
        $crawler = $this->client->request('GET',$url);
        if($currentUser !== $targetUser){
            $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));
            $crawler = $this->client->followRedirect();
            $crawler = $this->inputCardKey($crawler, '1111');
            $crawler = $this->client->followRedirect();
        }

        if(! ($targetUser->hasReferent($currentUser) || $targetUser === $currentUser)){
            //access denied exception
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            $this->assertContains($expectMessage,$this->client->getResponse()->getContent());

        }else{
            $targetCard = $targetUser->getCard();
            if(!$expectForm){
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();
                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
                $this->assertSame(1, $crawler->filter('div.alert-info')->count());    
            }else{
                $this->client->enableProfiler();

                $form = $crawler->selectButton('confirmation_save')->form();
                $form['confirmation[current_password]']->setValue('@@bbccdd');
                $crawler =  $this->client->submit($form);

                //assert card
                $this->em->refresh($targetUser);
                $this->assertNotEquals($targetUser->getCard(),NULL);
                $this->assertEquals($targetUser->getCardKeyTries(), 0);

                $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

                if($emailSent){
                    //assert email
                    $this->assertSame(1, $mailCollector->getMessageCount());
                    $message = $mailCollector->getMessages()[0];
                    $this->assertInstanceOf('Swift_Message', $message);
                    $this->assertContains('Nouvelle carte', $message->getSubject());
                    $this->assertContains('nouvelle carte', $message->getBody());
                    $this->assertContains($currentUser->getName(), $message->getBody());

                    $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                    $this->assertSame($targetUser->getEmail(), key($message->getTo()));
                }else{
                    $this->assertSame(0, $mailCollector->getMessageCount());
                }

                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

            }
        }
    }

    public function provideUsersForCardOrder()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'ordered by ref'=> array('current'=>$adminUsername,'target'=>'episol','expectForm'=>true,'expectMessage'=>'xxx',
                                     'emailSent'=>true), 
            'self order'=> array('current'=>'episol','target'=>'episol','expectForm'=>true,'expectMessage'=>'xxx',
                                 'emailSent'=>false),             
             'ordered by non ref'=>array('current'=>$adminUsername,'target'=>'NaturaVie','expectForm'=>false,
                                         'expectMessage'=>'pas référent','emailSent'=>false),
             'card already ordered'=>array('current'=>'DrDBrew','target'=>'DrDBrew','expectForm'=>false,
                                           'expectMessage'=>'carte commandée en cours','emailSent'=>false),
             'generated card'=>array('current'=>'labonnepioche','target'=>'labonnepioche','expectForm'=>false,
                                     'expectMessage'=>'carte courante est active','emailSent'=>false),
             'not generated card'=>array('current'=>'recycleco','target'=>'recycleco','expectForm'=>false,
                                     'expectMessage'=>'déjà une carte courante','emailSent'=>false),

        );
    }

}

