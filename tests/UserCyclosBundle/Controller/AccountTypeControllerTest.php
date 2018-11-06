<?php

namespace Tests\UserCyclosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\ProductManager;

use Cyclos;

class AccountTypeControllerTest extends BaseControllerTest
{
    protected $client;
    protected $container;
    protected $userManager;
    protected $productManager;

    protected $em;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = static::createClient();

        $this->container = $this->client->getContainer();
        $this->userManager = new UserManager();
        $this->productManager = new ProductManager();
    }

    /**
     *@dataProvider provideDataForNewAccount
     */
    public function testAddAccountType($nature,$login,$isLegit,$expectForm, $name,$isValid, $creditLimit)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $this->login($login,'@@bbccdd');

        $url = '/config/accounts/accounttype/add/'.$nature;
        $crawler = $this->client->request('GET', $url);

        if(!$isLegit){
            $this->assertEquals(403,$this->client->getResponse()->getStatusCode());
        }else{
        $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));
        
        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        $countBefore = count($this->container->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes(NULL,'USER'));

        if(!$expectForm){
            $this->assertTrue($this->client->getResponse()->isRedirect());
        }else{
            $form = $crawler->selectButton('form_save')->form();
            $form['form[name]']->setValue($name);
            $form['form[creditLimit]']->setValue($creditLimit);
            $crawler =  $this->client->submit($form);

            $accounttypes = $this->container->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes(NULL,'USER');

            if($isValid){
                $this->assertEquals($countBefore + 1, count($accounttypes));
                $crawler = $this->client->followRedirect();
                $this->assertSame(1, $crawler->filter('div.alert-success')->count());    
            }else{
                $this->assertEquals($countBefore, count($accounttypes));
                $this->assertTrue($this->client->getResponse()->isRedirect($url));
            }
        }
        }
    }

    public function provideDataForNewAccount()
    {

        //there is an unique account type
        $accounttypes = $this->container->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes(NULL,'USER');
        $currentName = $accounttypes[0]->name;

        return array(
            'not legit'=> array('nature'=>'SYSTEM','login'=>'LaBonnePioche','isLegit'=>false, 'expectForm'=>false,'name'=>'No Matter',
                                     'isValid'=>false, 'creditLimit'=>'0'),
            'invalid nature'=> array('nature'=>'SYSTEM','login'=>'mazouthm','isLegit'=>true, 'expectForm'=>false,'name'=>'No Matter',
                                     'isValid'=>false, 'creditLimit'=>'0'),
            'confusing name 1'=> array('nature'=>'USER','login'=>'mazouthm','isLegit'=>true,'expectForm'=>true, 'name'=>$currentName.' 2 ',                                       'isValid'=>false, 'creditLimit'=>'0'),
            'confusing name 2'=> array('nature'=>'USER','login'=>'mazouthm','isLegit'=>true,'expectForm'=>true, 
                                       'name'=>substr($currentName, 0, -1),'isValid'=>false, 'creditLimit'=>'0'),
            'valid data'=> array('nature'=>'USER','login'=>'mazouthm','isLegit'=>true,'expectForm'=>true, 'name'=>'Test Account',
                                 'isValid'=>true, 'creditLimit'=>'0'),
        );
    }

    /**
     *@depends testAddAccountType
     *@dataProvider provideDataForAccountRemoval
     */
    public function testRemoveAccountType()
    {


    }

    public function provideDataForAccountRemoval()
    {
        $systemAccounttypes = $this->container->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes(NULL,'SYSTEM');
        $memberAccounttypes = $this->container->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes(NULL,'USER');
        $debitAccount = $this->container->get('cairn_user_cyclos_accounttype_info')->getDebitAccount();


        return array(
            'invalid id'=>array(),
            'debit account type'=>array(),
            'system account type'=>array(),
            'remove first member account type'=>array(),
            'remove unique member account type'=>array() 
        );

    }
}
