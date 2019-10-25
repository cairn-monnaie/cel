<?php

namespace Tests\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Cyclos;

class BaseControllerTest extends WebTestCase
{
    protected $client;
    protected $container;

    protected $em;
    protected $testAdmin;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $this->testAdmin = 'admin_network';
    }

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');                          

    }

    public function assertSerializedEntityContent($entity,$entityType){
        switch($entityType){
        case 'phone':
            $this->assertArrayHasKey('id', $entity);
            $this->assertArrayHasKey('phoneNumber', $entity);
            $this->assertArrayHasKey('identifier', $entity);
            $this->assertArrayHasKey('paymentEnabled', $entity);
            $this->assertArrayHasKey('dailyAmountThreshold', $entity);
            $this->assertArrayHasKey('dailyNumberPaymentsThreshold', $entity);

            $this->assertArrayNotHasKey('smsData', $entity);
            $this->assertArrayNotHasKey('user', $entity);

            $this->assertEquals(count($entity),6);
            break;

        default:
            throw new \Exception('entity not found while testing JSON response');

        }
    }
    public function login($username,$password)
    {
        $crawler = $this->client->request('GET','/logout');
        $crawler = $this->client->request('GET','/login');


        $form = $crawler->selectButton('_submit')->form();
        $form['_username']->setValue($username);
        $form['_password']->setValue($password);
        $crawler = $this->client->submit($form);

        return $this->client->followRedirect();

    }

    protected function mobileLogin($username, $password)
    {
        $firewallName = 'mobile';
        $firewallContext = $firewallName;

        $user = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($username);

        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewallName, $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);

        $session = $this->container->get('session');
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function inputCardKey($crawler, $key)
    {
        $form = $crawler->selectButton('card_save')->form();
        $form['card[field]']->setValue($key);
        return $this->client->submit($form);
    }

    public function assertUserIsEnabled(User $user, $statusChanged)
    {
        $this->assertTrue($user->isEnabled());

        //connect to Cyclos as an admin to get user status on Cyclos side
        $credentials = array('username'=> $this->testAdmin,'password'=>'@@bbccdd');
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),
                                                                                 'login',$credentials);

        $status = $this->container->get('cairn_user_cyclos_user_info')->getUserStatus($user->getCyclosID() );
        $this->assertEquals($status,'ACTIVE');

        /*  if user is activated, the status of targetUser on Cyclos-side must change to 'ACTIVE', 
            and this won't be rolled back in the end of the test whereas targetUser will be enabled again on Symfony side
            Workaround : resetting user status to 'DISABLED' "by hand" at test end
            this is a problem regarding isolation tests and Cyclos
        */
        if($statusChanged){
            $this->container->get('cairn_user.access_platform')->changeUserStatus($user,'DISABLED');
        }
    }

    public function assertUserIsDisabled(User $user, $statusChanged)
    {
        $this->assertFalse($user->isEnabled());

        $phones = $user->getPhones();
        foreach($phones as $phone){
            $this->assertFalse($phone->isPaymentEnabled());
        }

        //connect to Cyclos as an admin to get user status on Cyclos side
        $credentials = array('username'=> $this->testAdmin,'password'=>'@@bbccdd');
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),
                                                                                 'login',$credentials);
        $status = $this->container->get('cairn_user_cyclos_user_info')->getUserStatus($user->getCyclosID() );
        $this->assertEquals($status,'DISABLED');

        /*  if user is opposed the status of targetUser on Cyclos-side must change to 'DISABLED', 
            and this won't be rolled back in the end of the test whereas targetUser will be enabled again on Symfony side
            Workaround : resetting user status to 'ACTIVE' "by hand" at test end
            this is a problem regarding isolation tests and Cyclos
        */
        if($statusChanged){
            $this->container->get('cairn_user.access_platform')->changeUserStatus($user,'ACTIVE');
        }
    }

    public function provideReferentsAndTargets()
    {

        $adminUsername = $this->testAdmin;
        return array(
            array('referent'=>$adminUsername,'target'=>$adminUsername,'isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'DrDBrew','isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'NaturaVie','isReferent'=>false)
        );
    }


}
