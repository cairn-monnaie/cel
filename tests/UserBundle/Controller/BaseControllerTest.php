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

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
      parent::__construct($name, $data, $dataName);
      $this->client = static::createClient();

      $this->container = $this->client->getContainer();
      $this->scriptManager = new ScriptManager();
      $this->userManager = new UserManager();
      $this->productManager = new ProductManager();

      $crawler = $this->client->request('GET','/install/');
      $this->login('mazouthm','admin');

        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        //generate doctrine users or not
        $this->em = $this->container->get('doctrine')->getManager();                          
        
        $user = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'MaltOBar'));

        if(!$user){
            $memberGroupName = $this->container->getParameter('cyclos_group_pros');
            $adminGroupName = $this->container->getParameter('cyclos_group_network_admins');

            $scriptResult = $this->scriptManager->runScript(file_get_contents($this->container->getParameter('kernel.project_dir').'/tests/script_test_db.groovy',false));

            $nb = 0;
//            while($nb != 5){ //delay between running script and database update

              $memberGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($memberGroupName ,'MEMBER_GROUP');
              $adminGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($adminGroupName,'ADMIN_GROUP');

              $cyclosMembers = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($memberGroup->id);
              $cyclosAdmins = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($adminGroup->id);

              $cyclosUsers = array_merge($cyclosMembers, $cyclosAdmins);
              $nb = count($cyclosUsers);
//          }

          $superAdmin  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'mazouthm'));

//            foreach($cyclosAdmins as $cyclosAdmin){
//                $this->createUser($cyclosAdmin, $superAdmin);
//            }

          foreach($cyclosMembers as $cyclosMember){
              $this->createUser($cyclosMember,$superAdmin);
          }

            $this->em->flush();

      }
    }

    public function createUser($cyclosUser, $superAdmin)
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

        $card = new Card($doctrineUser,$this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'));
        $card->setCreationDate($creationDate);
        $doctrineUser->setCard($card);

        if($doctrineUser->getCity() == $superAdmin->getCity()){
            $doctrineUser->addReferent($superAdmin);
        }

        $this->em->persist($doctrineUser);

    }

    public function login($username,$password)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->client->request('GET','/logout');
        $crawler = $this->client->request('GET','/login');


        $form = $crawler->selectButton('_submit')->form();
        $form['_username']->setValue($username);
        $form['_password']->setValue($password);
        $crawler = $this->client->submit($form);
        
        return $this->client->followRedirect();

    }

    public function testNada(){
        $this->assertSame(0,0);
    }


    public function cardKeyInput($crawler,$key)
    {
        $form = $crawler->selectButton('card_save')->form();
        $form['card[field]']->setValue($key);
        $crawler = $this->client->submit($form);
    }


}
