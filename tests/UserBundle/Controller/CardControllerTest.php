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
     *_$referent wants card action for itself :
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

        $crawler = $this->client->request('GET','/card/home/'.$currentUser->getID());

        if($currentUser === $targetUser){//user for himself
            if($currentUser->getUsername() == $this->container->getParameter('cyclos_global_admin_username')){
                $this->assertSame(4,$crawler->filter('a.operation_link')->count());
            }else{
                $this->assertSame(3,$crawler->filter('a.operation_link')->count());
            }
        }else{ //user for someone else
            $crawler = $this->client->request('GET','/card/home/'.$targetUser->getID());

            if($targetUser->hasReferent($currentUser)){
                $this->assertSame(3,$crawler->filter('a.operation_link')->count());
            }
            else{
                var_dump($this->client->getResponse());
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

            //valid key
            $crawler = $this->inputCardKey($crawler,'1111');
            $crawler = $this->client->followRedirect();
            $this->assertSame(0,$crawler->filter('html:contains("Activer la carte")')->count());
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
        $crawler = $this->client->followRedirect();

        $card = $targetUser->getCard();

        if(! ($targetUser->getUsername() == $this->container->getParameter('cyclos_global_admin_username') 
            && $targetUser === $currentUser)){
                $crawler = $this->inputCardKey($crawler, '1111');
                $crawler = $this->client->followRedirect();
        }

        if(!$targetUser->hasReferent($currentUser)){
            //access denied exception
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        }else{
            $card = $targetUser->getCard();
            if(!$card || $card->isGenerated()){
                $this->assertTrue($this->client->getResponse()->isRedirect());
            }else{
                $form = $crawler->selectButton('confirmation_save')->form();
                $crawler =  $this->client->submit($form);
                $this->assertTrue($this->client->getResponse()->isRedirect('/card/download/'.$card->getID()));
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
     *@depends testGenerateCard
     */
    public function testRevokeCard()
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $targetName = 'MaltOBar';
        $crawler = $this->login($targetName, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$targetName));
        //        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));
        //
        //        //action of user for itself
        $crawler = $this->client->request('GET','/card/revoke/'.$currentUser->getID());
        $this->assertSame(1,$crawler->filter('html:contains("été créée")')->count());

        if(!$currentUser->getCard()){
            $this->assertTrue($this->client->getResponse()->isRedirect());
        }


        //        //wrong password
        //        $form = $crawler->selectButton('card_save')->form();
        //        $form['card[field]']->setValue('1111');
        //        $crawler = $this->client->submit($form);
        //
        //        //valid password
        //        $form = $crawler->selectButton('confirmation_save')->form();
        //        $form['confirmation[password]']->setValue('@@bbccdd');
        //        $crawler = $this->client->submit($form);
        //        $this->assertTrue($this->client->getResponse()->isRedirect('/card/revoke/'.$currentUser->getID()));
        //
        //
        //        $card = $currentUser->getCard();
        //        $this->assertTrue($card);
    }

    //    /**
    //     * $target wants a new card
    //     *_$referent = $target || $referent is referent of $target
    //     *                          . card not revoked : redirection to card home page
    //     *                          . card revoked : password confirmation
    //     *                                                      . success password : session message with key "success" + card exists
    //     *                                                      . wrong password : try again
    //     * @dataProvider provideReferentsAndTargets
    //     * @depends testRevokeCard
    //     */
    //    public function testNewCard($referent,$target)
    //    {
    //        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
    //        $this->client->followRedirects();
    //
    //        $crawler = $this->login($referent[0], $referent[1]);
    //
    //        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
    //        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));
    //
    //        //action of user for itself
    //        $crawler = $this->client->request('GET','/card/new/'.$currentUser->getID());
    //        if($currentUser->getCard()){
    //            $this->assertTrue($this->client->getResponse()->isRedirect());
    //        }
    //
    //        //wrong password
    //        $form = $crawler->selectButton('confirmation_save')->form();
    //        $form['confirmation[password]']->setValue('@bbccdd');
    //        $crawler = $this->client->submit($form);
    //        $this->assertSame(1,$crawler->filter('html:contains("invalide")')->count());
    //        $this->assertTrue($this->client->getResponse()->isRedirect('/card/new'.$currentUser->getID()));
    //
    //        //valid password
    //        $form = $crawler->selectButton('confirmation_save')->form();
    //        $form['confirmation[password]']->setValue('@@bbccdd');
    //        $crawler = $this->client->submit($form);
    //
    //        $card = $currentUser->getCard();
    //        $this->assertTrue($card);
    //    }

    public function provideReferentsAndTargets()
    {
        return array(
//            array(array('mazouthm','admin'),'mazouthm'),
//            array(array('mazouthm','admin'),'DrDBrew'),
//            array(array('mazouthm','admin'),'MaltOBar'),
            array(array('mazouthm','admin'),'cafeEurope')
        );
    }

    public function provideUsers()
    {
        return array(
            array('mazouthm','admin'),
            array('DrDBrew','@@bbccdd')
        );
    }

    /**
     *_ROLE_PRO tries to generate card
     *_ADMIN tries to generate its own card
     *_ADMIN tries to generate non referent Pro's card
     *_ADMIN generates pro' card under its responsibility
     */
    //    public function testGenerateCard()
    //    {
    //      $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
    //      $this->client->followRedirects();
    //
    //      $crawler = $this->login('mazouthm','admin');
    //        $this->assertSame(1,$crawler->filter('html:contains("Espace Administrateur")')->count());
    //
    //      $targetOption = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
    //      $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
    //        $crawler = $this->client->request('GET','/card/generate/?id=4');
    //      $this->assertTrue($client->getResponse()->isRedirect());
    //
    //
    //
    //        $this->assertSame(1,$crawler->filter('html:contains("carte de sécurité")')->count());
    //        $form = $crawler->selectButton('card_save')->form();
    //        $form['card[field]']->setValue('1111');
    //        $crawler = $this->client->submit($form);
    //
    //        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
    //
    //        $this->assertSame(1,$crawler->filter('html:contains("déjà")')->count());
    //     
    //        $targetOption = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'cafeEurope'));
    //        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
    //        $this->assertSame(1,$crawler->filter('html:contains("pas référent de")')->count());
    //
    //        $this->login('cafeEurope','@@bbccdd');
    //        $crawler = $this->client->request('GET','/card/generate/?id='.$targetOption->getID());
    //        $this->assertSame(1,$crawler->filter('html:contains("access denied")')->count());
    //
    //  }
    //  
    //  public function testCheckDelayedCards()
    //  {
    //      $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
    //      $this->client->followRedirects();
    //
    //      $this->login('mazouthm','admin');
    //      $crawler = $this->client->request('GET','/card/check/delayed');
    //
    //      //TODO : control users with deadline missed
    //    }
}

