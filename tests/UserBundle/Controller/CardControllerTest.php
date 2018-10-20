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
     *_$referent wants card action for himself :
     *                          .installed superadmin : revoke / order / activate / generate
     *                          .otherwise : revoke / order / activate
     *_$referent wants card action for $target :
     *                          .isGranted ROLE_ADMIN : 
     *                              *if $referent is referent of $target : revoke / order / generate
     *                              *otherwise : nothing(should not even see card_operations) 
     * @dataProvider provideReferentsAndTargets
     */
    public function testCardOperations($referent,$target)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($referent[0], $referent[1]);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','/card/home/'.$targetUser->getID());

        if($currentUser === $targetUser){//user for himself
            if($currentUser->getUsername() == $this->container->getParameter('cyclos_global_admin_username')){
                $this->assertSame(4,$crawler->filter('a.operation_link')->count());
            }else{
                $this->assertSame(3,$crawler->filter('a.operation_link')->count());
            }
        }else{ //user for someone else
            if($targetUser->hasReferent($currentUser)){
                $this->assertSame(3,$crawler->filter('a.operation_link')->count());
            }
            else{
                $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            }
        }

    }

    public function inputCardKey($crawler, $key)
    {
        $form = $crawler->selectButton('card_save')->form();
        $form['card[field]']->setValue($key);
        return $this->client->submit($form);
    }

    /**
     *Tests the card validation using a key
     *@dataProvider provideUsers
     */
    public function testValidateCard($login,$password)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($login, $password);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));
        $crawler = $this->client->request('GET', '/card/validate');

        $card = $currentUser->getCard();
        if(!$card || !$card->isGenerated() || $card->isEnabled()){
            $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$currentUser->getID()));
        }else{
            //wrong key
            $crawler = $this->inputCardKey($crawler,'5555');
            $this->assertTrue($this->client->getResponse()->isRedirect('/card/validate'));
            $crawler = $this->client->followRedirect();
            $this->assertSame(1,$crawler->filter('html:contains("Clé invalide")')->count());
            $this->assertSame(1,$crawler->filter('html:contains("Attention")')->count());

            $this->em->refresh($currentUser);
            $this->assertSame(1,$currentUser->getCardKeyTries());

            //valid key
            $crawler = $this->inputCardKey($crawler,'1111');
            $crawler = $this->client->followRedirect();
            $this->assertSame(0,$crawler->filter('html:contains("Activer la carte")')->count());

            $this->em->refresh($currentUser);
            $this->em->refresh($card);

            $this->assertSame(0,$currentUser->getCardKeyTries());
            $this->assertTrue($card->isEnabled());
        }
    }

    /**
     *Tests if card intermediate step is reached for a  sensible operation
     *@depends testValidateCard
     *@dataProvider provideUsers
     */
    public function testCardSecurityLayer($login,$password)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($login, $password);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));

        //any sensible operation (see Cairn/UserBundle/Event/SecurityEvents.php)
        $url = '/user/beneficiaries/add';
        $crawler = $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));

        $crawler = $this->client->followRedirect();

        $card = $currentUser->getCard();
        if(!$card || !$card->isEnabled()){
            $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$currentUser->getID()));
        }else{
            //valid key
            $crawler = $this->inputCardKey($crawler,'1111');
            $this->assertTrue($this->client->getResponse()->isRedirect($url));
        }
    }

    /**
     * if $referent is installed super admin : can generate a card for himself
     * if $referent is referent of $target : can generate card
     * if $referent is not referent of $target : code 403
     * if $referent does not have active card : redirection
     * if $target has a generated card : redirection
     *
     *@depends testCardSecurityLayer
     *@dataProvider provideReferentsAndTargets
     */
    public function testGenerateCard($referent, $target)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($referent[0], $referent[1]);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        //sensible operation
        $crawler = $this->client->request('GET','/card/generate/'.$targetUser->getID());

        $card = $targetUser->getCard();

        if(! ($targetUser->getUsername() == $this->container->getParameter('cyclos_global_admin_username') 
            && $targetUser === $currentUser)){
            $crawler = $this->client->followRedirect();
            $crawler = $this->inputCardKey($crawler, '1111');
            $crawler = $this->client->followRedirect();
        }

        if(!$targetUser->hasReferent($currentUser)){
            //access denied exception
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        }else{
            $card = $targetUser->getCard();
            if(!$card || $card->isGenerated()){
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();
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

    /**
     * $target wants to revoke his card
     *_$referent = $target || $referent is referent of $target
     *                          . no card || card revoked : redirection to card home page
     *                          . card not revoked : password confirmation
     *                                                      . success password : session message with key "success" + card exists
     *                                                      . wrong password : try again
     *@dataProvider provideUsersWithRevokedCard 
     *@depends testGenerateCard
     */
    public function testRevokeCard($referent, $target)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($referent[0], $referent[1]);


        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
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
        }else{
            $targetCard = $targetUser->getCard();
            if(!$targetCard || !$targetCard->getFields()){
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));
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
                $this->assertSame(1,$crawler->filter('html:contains("prise en compte")')->count());

            }
        }

    }

    /**
     * $target wants a new card
     *_$referent = $target || $referent is referent of $target
     *                          . card not revoked : redirection to card home page
     *                          . card revoked : password confirmation
     *                                                      . success password : session message with key "success" + card exists
     *                                                      . wrong password : try again
     * @dataProvider provideUsersWithRevokedCard
     * @depends testRevokeCard
     */
    public function testNewCard($referent,$target)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($referent[0], $referent[1]);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        //sensible operation
        $url = '/card/new/'.$targetUser->getID();
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
        }else{
            $targetCard = $targetUser->getCard();
            if($targetCard){
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/home/'.$targetUser->getID()));
            }else{
                $this->client->enableProfiler();

                $form = $crawler->selectButton('confirmation_save')->form();
                $form['confirmation[password]']->setValue('@@bbccdd');
                $crawler =  $this->client->submit($form);

                //assert card
                $this->em->refresh($targetUser);
                $this->assertNotEquals($targetUser->getCard(),NULL);

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
                $this->assertSame(1,$crawler->filter('html:contains("prise en compte")')->count());
            }
        }
    }

    /**
     *
     *depends testNewCard
     */
    public function testCheckCardsExpiration()
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $login = $this->container->getParameter('cyclos_global_admin_username');
        $password = '@@bbccdd';
        $crawler = $this->login($login, $password);


        $crawler = $this->client->request('GET','/card/check/expiration/');

        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'response status is 2xx');

    }

    public function provideReferentsAndTargets()
    {
        return array(
            array(array('mazouthm','@@bbccdd'),'mazouthm'),
            array(array('mazouthm','@@bbccdd'),'DrDBrew'),
            array(array('mazouthm','@@bbccdd'),'MaltOBar'),
            array(array('mazouthm','@@bbccdd'),'cafeEurope')
        );
    }

    public function provideUsersWithRevokedCard()
    {
        return array(
            array(array('mazouthm','@@bbccdd'),'cafeEurope'), 
            array(array('mazouthm','@@bbccdd'),'LaBonnePioche'),  
            array(array('MaltOBar','@@bbccdd'),'MaltOBar'),  
            array(array('DrDBrew','@@bbccdd'),'DrDBrew')     
        );
    }

    public function provideUsers()
    {
        return array(
            array('mazouthm','@@bbccdd'),
            array('DrDBrew','@@bbccdd')
        );
    }

}

