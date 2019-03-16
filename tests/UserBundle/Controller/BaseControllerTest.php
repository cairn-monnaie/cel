<?php

namespace Tests\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\ProductManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;


use Cyclos;

class BaseControllerTest extends WebTestCase
{
    protected $client;
    protected $container;
    protected $scriptManager;
    protected $userManager;
    protected $productManager;

    protected $em;
    protected $testAdmin;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $this->client = static::createClient();

        $this->container = $this->client->getContainer();
        $this->scriptManager = new ScriptManager();
        $this->userManager = new UserManager();

        $this->em = $this->container->get('doctrine.orm.entity_manager');                          
        $this->testAdmin = 'admin_network';
    }

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
         
        $this->container = $kernel->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');                          

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

    public function inputCardKey($crawler, $key)
    {
        $form = $crawler->selectButton('Valider')->form();
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
