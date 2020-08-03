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

        $this->client = static::createClient();

        //necessary in order to get data from the container on data providers, before these are called BEFORE setUp function
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');                          

    }

    //cleanup and create new client before each test function
    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');                          
    }

    //a data has been returned and/or created
    public function isSuccessfulResponse($response)
    {
        return (($response->getStatusCode() < 300) && (! array_key_exists('errors',json_decode($response->getContent(),true))));
    }

    public function errorContains($errors,$expectedMessage)
    {
        foreach($errors as $error){
            $keyContains = strpos($error['key'], $expectedMessage);
            $messageContains = strpos($error['message'], $expectedMessage);
            if(($keyContains !== false) || ($messageContains !== false)){
                return true;
            }
        }
        return false;
    }

    public function assertSerializedEntityContent($entity,$entityType){
        switch($entityType){
        case 'user':
            $this->assertArrayHasKey('id', $entity);
            $this->assertArrayHasKey('username', $entity);
            $this->assertArrayHasKey('email', $entity);
            $this->assertArrayHasKey('name', $entity);
            $this->assertArrayHasKey('enabled', $entity);
            $this->assertArrayHasKey('mainICC', $entity);
            $this->assertArrayHasKey('address', $entity);
            $this->assertArrayHasKey('description', $entity);
            $this->assertArrayHasKey('image', $entity);
            $this->assertArrayHasKey('firstLogin', $entity);
            $this->assertArrayHasKey('autocompleteLabel', $entity);
            $this->assertArrayHasKey('city', $entity);
            $this->assertArrayHasKey('lastLogin', $entity);
            $this->assertArrayHasKey('roles', $entity);
            $this->assertArrayHasKey('adherent', $entity);
            $this->assertArrayHasKey('phones', $entity);
            $this->assertArrayHasKey('url', $entity);
            $this->assertArrayHasKey('keywords', $entity);
            $this->assertArrayHasKey('excerpt', $entity);

            $this->assertArrayNotHasKey('publish', $entity);
            $this->assertArrayNotHasKey('creationDate', $entity);
            $this->assertArrayNotHasKey('cyclosID', $entity);
            $this->assertArrayNotHasKey('superAdmin', $entity);
            $this->assertArrayNotHasKey('admin', $entity);
            $this->assertArrayNotHasKey('passwordRequestedAt', $entity);
            $this->assertArrayNotHasKey('identityDocument', $entity);
            $this->assertArrayNotHasKey('passwordTries', $entity);
            $this->assertArrayNotHasKey('cardKeyTries', $entity);
            $this->assertArrayNotHasKey('phoneNumberActivationTries', $entity);
            $this->assertArrayNotHasKey('cardAssociationTries', $entity);
            $this->assertArrayNotHasKey('removalRequest', $entity);
            $this->assertArrayNotHasKey('nbPhoneNumberRequests', $entity);
            $this->assertArrayNotHasKey('phoneNumbers', $entity);
            $this->assertArrayNotHasKey('referents', $entity);
            $this->assertArrayNotHasKey('beneficiaries', $entity);
            $this->assertArrayNotHasKey('card', $entity);
            $this->assertArrayNotHasKey('smsData', $entity);
            $this->assertArrayNotHasKey('apiClient', $entity);
            $this->assertArrayNotHasKey('firstname', $entity);
            $this->assertArrayNotHasKey('cyclosToken', $entity);
            $this->assertArrayNotHasKey('confirmationToken', $entity);
            $this->assertArrayNotHasKey('salt', $entity);
            $this->assertArrayNotHasKey('password', $entity);
            $this->assertArrayNotHasKey('plainPassword', $entity);
            $this->assertArrayNotHasKey('singleReferent', $entity);
            $this->assertArrayNotHasKey('localGroupReferent', $entity);
            $this->assertArrayNotHasKey('webPushSubscriptions', $entity);
            $this->assertArrayNotHasKey('usernameCanonical', $entity);
            $this->assertArrayNotHasKey('emailCanonical', $entity);
            $this->assertArrayNotHasKey('accountNonExpired', $entity);
            $this->assertArrayNotHasKey('accountNonLocked', $entity);
            $this->assertArrayNotHasKey('credentialsNonExpired', $entity);
            $this->assertArrayNotHasKey('groups', $entity);
            $this->assertArrayNotHasKey('groupNames', $entity);

            $this->assertEquals(19,count($entity));
            break;

        case 'phone':
            $this->assertArrayHasKey('id', $entity);
            $this->assertArrayHasKey('phoneNumber', $entity);
            $this->assertArrayHasKey('identifier', $entity);
            $this->assertArrayHasKey('paymentEnabled', $entity);
            $this->assertArrayHasKey('smsData', $entity);

            
            $this->assertArrayNotHasKey('dailyAmountThreshold', $entity);
            $this->assertArrayNotHasKey('dailyNumberPaymentsThreshold', $entity);
            $this->assertArrayNotHasKey('user', $entity);

            $this->assertEquals(5,count($entity));
            break;

        case 'beneficiary':
            $this->assertArrayHasKey('id', $entity);
            $this->assertArrayHasKey('autocompleteLabel', $entity);
            $this->assertArrayHasKey('ICC', $entity);
            $this->assertArrayHasKey('user', $entity);

            $this->assertArrayNotHasKey('sources', $entity);

            $this->assertEquals(4,count($entity));
            break;

        case 'operation':
            $this->assertArrayHasKey('id', $entity);
            $this->assertArrayHasKey('type', $entity);
            $this->assertArrayHasKey('paymentID', $entity);
            $this->assertArrayHasKey('recurringID', $entity);
            $this->assertArrayHasKey('amount', $entity);
            $this->assertArrayHasKey('submissionDate', $entity);
            $this->assertArrayHasKey('executionDate', $entity);
            $this->assertArrayHasKey('description', $entity);
            $this->assertArrayHasKey('reason', $entity);
            $this->assertArrayHasKey('fromAccountNumber', $entity);
            $this->assertArrayHasKey('toAccountNumber', $entity); 
            $this->assertArrayHasKey('smsPayment', $entity);
            $this->assertArrayHasKey('creditor', $entity);
            $this->assertArrayHasKey('creditorName', $entity);
            $this->assertArrayHasKey('debitor', $entity);
            $this->assertArrayHasKey('debitorName', $entity);
            $this->assertArrayHasKey('updatedAt', $entity);

            //$this->assertArrayNotHasKey('fromAccount', $entity);
            //$this->assertArrayNotHasKey('toAccount', $entity);
            //$this->assertArrayNotHasKey('mandate', $entity);
            //$this->assertEquals(17,count($entity));
            break;

        case 'account':
            $this->assertArrayHasKey('id', $entity);
            $this->assertArrayHasKey('owner', $entity);
            $this->assertArrayHasKey('status', $entity);
            $this->assertArrayHasKey('number', $entity);
            $this->assertArrayHasKey('currency', $entity);
            $this->assertArrayHasKey('type', $entity);
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

    protected function generateApiAuthorizationHeader($timestamp,$method,$uri,$bodyContent=NULL)
    {
        $data = $timestamp.$method.$uri;

        if($bodyContent){
            $payloadAsString = $this->container->get('cairn_user.api')->fromArrayToStringDeterministicOrder($bodyContent);
            $data .= hash('md5',preg_replace('/\s+/','', $payloadAsString)) ; 
        }
        $key = hash_hmac('sha256',$data,$this->container->getParameter('api_secret'));

        return 'HMAC-SHA256 Bearer XXX Signature='.$timestamp.':'.$key;
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

        $phones = $user->getPhones();
        foreach($phones as $phone){
            $this->em->refresh($phone);
            $this->assertFalse($phone->isPaymentEnabled());
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
