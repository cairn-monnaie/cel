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

    function __construct($name = NULL, array $data = array(), $dataName = ''){
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
     * @dataProvider provideReferentsAndTargets
     */
    public function testCardOperations($referent,$target,$isReferent)
    {
        $crawler = $this->login($referent, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','/card/home/'.$targetUser->getID());

        if($currentUser === $targetUser){
            if($currentUser->hasRole('ROLE_SUPER_ADMIN')){
                $this->assertSame(4,$crawler->filter('a.operation_link')->count());
            }else{
                $this->assertSame(3,$crawler->filter('a.operation_link')->count());
            }
        }else{ //user for someone else
            if($isReferent){
                $this->assertSame(3,$crawler->filter('a.operation_link')->count());
            }
            else{
                $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            }
        }

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
            'invalid key'        => array('login'=>$this->testAdmin,'key'=>'5555','expectForm'=>true,'expectValidKey'=>false),
            'valid key'          => array('login'=>$this->testAdmin,'key'=>'1111','expectForm'=>true,'expectValidKey'=>true),
            'validated card'     => array('login'=>$this->testAdmin,'key'=>'1111','expectForm'=>false,'expectValidKey'=>true),
            'not generated card' => array('login'=>'DrDBrew', 'key'=>'1111','expectForm'=>false,'expectValidKey'=>false),
        );
    }

    /**
     *Tests if card intermediate step is reached for a  sensible operation
     *@depends testValidateCard
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
        return array(
            'validated card'     => array('login'=>$this->testAdmin, 'expectValid'=>true),
            'not validated card' => array('login'=>'LaDourbie','expectValid'=>false),
        );
    }

    /**
     * if $current is installed super admin : can generate a card for himself
     * if $current is current of $target : can generate card
     * if $current is not current of $target : code 403
     * if $current does not have active card : redirection
     * if $target has a generated card : redirection
     *
     *@depends testCardSecurityLayer
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
        return array(
               array('current'=>$this->testAdmin,'target'=>$this->testAdmin,'expectConfirm'=>false,'expectMessage'=>'déjà été générée'),             
               array('current'=>$this->testAdmin,'target'=>'LaBonnePioche','expectConfirm'=>true,'expectMessage'=>'xxx'),             
               array('current'=>$this->testAdmin,'target'=>'DrDBrew','expectConfirm'=>true,'expectMessage'=>'xxx'),             
               array('current'=>$this->testAdmin,'target'=>'MaltOBar','expectConfirm'=>true,'expectMessage'=>'xxx'),             
               array('current'=>$this->testAdmin,'target'=>'locavore','expectConfirm'=>true,'expectMessage'=>'xxx'),             
               array('current'=>$this->testAdmin,'target'=>'cafeEurope','expectConfirm'=>false,'expectMessage'=>'pas référent'),             
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
     *@depends testGenerateCard
     */
    public function testRevokeCard($current, $target,$expectForm,$expectMessage)
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
                $form['confirmation[password]']->setValue('@@bbccdd');
                $crawler =  $this->client->submit($form);
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));

                //assert card
                $this->em->refresh($targetUser);
                $this->assertEquals($targetUser->getCard(),NULL);

                //assert email
                $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
                $this->assertSame(1, $mailCollector->getMessageCount());
                $message = $mailCollector->getMessages()[0];
                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('Révocation', $message->getSubject());
                $this->assertContains('révocation', $message->getBody());
                $this->assertContains($currentUser->getName(), $message->getBody());
                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($targetUser->getEmail(), key($message->getTo()));

                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

            }
        }
    }

    public function provideUsersForCardRevocation()
    {
        return array(
             'revocation from ref'=> array('current'=>$this->testAdmin,'target'=>'MaltOBar','expectForm'=>true,'expectMessage'=>'xxx'), 
             'self revocation'=> array('current'=>'DrDBrew','target'=>'DrDBrew','expectForm'=>true,'expectMessage'=>'xxx'),             
             'revoc from non ref'=>array('current'=>$this->testAdmin,'target'=>'cafeEurope','expectForm'=>false,'expectMessage'=>'pas référent'),
             'no card to revoke'=>array('current'=>'LaDourbie','target'=>'LaDourbie','expectForm'=>false,'expectMessage'=>'pas encore été créée'),
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
     * @depends testRevokeCard
     */
    public function testOrderCard($current,$target,$expectForm, $expectMessage)
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
                $form['confirmation[password]']->setValue('@@bbccdd');
                $crawler =  $this->client->submit($form);

                //assert card
                $this->em->refresh($targetUser);
                $this->assertNotEquals($targetUser->getCard(),NULL);
                $this->assertEquals($targetUser->getCardKeyTries(), 0);

                //assert email
                $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
                $this->assertSame(1, $mailCollector->getMessageCount());
                $message = $mailCollector->getMessages()[0];
                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('Nouvelle carte', $message->getSubject());
                $this->assertContains('nouvelle carte', $message->getBody());
                $this->assertContains($currentUser->getName(), $message->getBody());

                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($targetUser->getEmail(), key($message->getTo()));

                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

            }
        }
    }

    public function provideUsersForCardOrder()
    {
        return array(
             'ordered by ref'=> array('current'=>$this->testAdmin,'target'=>'DrDBrew','expectForm'=>true,'expectMessage'=>'xxx'), 
             'self order'=> array('current'=>'MaltOBar','target'=>'MaltOBar','expectForm'=>true,'expectMessage'=>'xxx'),             
             'ordered by non ref'=>array('current'=>$this->testAdmin,'target'=>'cafeEurope','expectForm'=>false,'expectMessage'=>'pas référent'),
             'no card to order'=>array('current'=>'LaDourbie','target'=>'LaDourbie','expectForm'=>false,'expectMessage'=>'déjà une carte'),
        );
    }

}

