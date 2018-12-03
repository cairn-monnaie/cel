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

        $this->client = static::createClient();

        $this->container = $this->client->getContainer();
        $this->scriptManager = new ScriptManager();
        $this->userManager = new UserManager();

        $this->em = $this->container->get('doctrine.orm.entity_manager');                          
    }

    /**
     *
     *$userRank is used to generate card creation dates at fixed intervals
     */
    public function createUser($cyclosUser, $superAdmin, $userRank)
    {
        $doctrineUser = new User();

        $cyclosUserData = $this->container->get('cairn_user_cyclos_user_info')->getProfileData($cyclosUser->id);
        $doctrineUser->setCyclosID($cyclosUserData->id);                                      
        $doctrineUser->setUsername($cyclosUserData->username);                           
        $doctrineUser->setName($cyclosUserData->name);
        $doctrineUser->setEmail($cyclosUserData->email);

        $creationDate = new \Datetime($cyclosUserData->activities->userActivationDate);
        $doctrineUser->setCreationDate($creationDate);            
        $doctrineUser->setPlainPassword('@@bbccdd');                      
        $doctrineUser->setEnabled(true);                                      

        if($cyclosUserData->group->nature == 'MEMBER_GROUP'){
            $doctrineUser->addRole('ROLE_PRO');   
        }else{
            $doctrineUser->addRole('ROLE_ADMIN');   
        }                

        $cyclosAddress = $cyclosUserData->addressListData->addresses[0];
        $zip = $this->em->getRepository('CairnUserBundle:ZipCity')->findOneBy(array('city'=>$cyclosAddress->city));
        $address = new Address();                                          
        $address->setZipCity($zip);                                        
        $address->setStreet1('10 rue de la Ciotat');

        $doctrineUser->setAddress($address);                                  
        $doctrineUser->setDescription('Test user blablablabla');             

        $card = new Card($doctrineUser,$this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'),'aaaa');
        $doctrineUser->setCard($card);

        if($doctrineUser->getCity() == $superAdmin->getCity()){
            $doctrineUser->addReferent($superAdmin);
        }

        $this->em->persist($doctrineUser);

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
        $form = $crawler->selectButton('card_save')->form();
        $form['card[field]']->setValue($key);
        return $this->client->submit($form);
    }

    public function getAdminUsername()
    {
        $installedAdmins = $this->em->getRepository('CairnUserBundle:User')->myFindByRole(array('ROLE_SUPER_ADMIN'));
        if($installedAdmins){
            return $installedAdmins[0]->getUsername();
        }

        return NULL;
    }


    public function provideReferentsAndTargets()
    {

        $adminUsername = $this->getAdminUsername();
        return array(
            array('referent'=>$adminUsername,'target'=>$adminUsername,'isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'DrDBrew','isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'MaltOBar','isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'cafeEurope','isReferent'=>false)
        );
    }


}
