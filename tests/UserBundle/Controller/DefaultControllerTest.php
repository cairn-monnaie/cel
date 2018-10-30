<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Address;

use Cyclos;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultControllerTest extends BaseControllerTest
{
    function __construct($name = NULL, array $data = array(), $dataName = ''){
        parent::__construct($name, $data, $dataName);
    }
    
    public function testInstall()
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->client->request('GET','/install/');
        $this->assertTrue($this->client->getResponse()->isRedirect('/'));
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert-info')->count());    

    }

//    /**
//     *
//     *@dataProvider provideTypeForRegistration
//     */
//    public function testRegistrationPage($login,$username,$type,$expectValid,$expectMessage)
//    {
//        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
//
//        if($login){
//            $crawler = $this->login($username, '@@bbccdd');
//            $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$username));
//        }else{
//            $currentUser = NULL;
//        }
//
//        $crawler = $this->client->request('GET','/registration/?type='.$type);
//        if(!$expectValid){
//                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
//        }else{
//            $this->assertTrue($this->client->getResponse()->isRedirect('/register/informations/'));
//        }
//    }

    /**
     *@todo : add test with ROLE_ADMIN trying to register new localgroup/super_admin
     *
     */
    public function provideTypeForRegistration($login, $username, $type, $expectValid, $expectMessage)
    {
        array(
            array('log in'=>true,'username'=>'LaBonnePioche','type'=>'', 'expectValid'=>false,'expectMessage'=>'déjà un espace membre'), 
            array('log in'=>true,'username'=>'mazouthm','type'=>'localGroup', 'expectValid'=>true,'expectMessage'=>''),
            array('log in'=>true,'username'=>'mazouthm','type'=>'pro', 'expectValid'=>true, 'expectMessage'=>''),
            array('log in'=>false,'username'=>'','type'=>'pro', 'expectValid'=>true,'expectMessage'=>''),
            array('log in'=>false,'username'=>'','type'=>'localGroup', 'expectValid'=>false,'expectMessage'=>'pas les droits nécessaires'),
            array('log in'=>false,'username'=>'','type'=>'', 'expectValid'=>false,'expectMessage'=>'Qui êtes-vous'),
            array('log in'=>false,'username'=>'','type'=>'adherent', 'expectValid'=>false, 'expectMessage'=>'Inscription impossible'),
            array('log in'=>false,'username'=>'','type'=>'xxx', 'expectValid'=>false,'expectMessage'=>'Qui êtes-vous'),
        );
    }

    /**
     *@dataProvider provideRegistrationUsers
     */
    public function testRegistration($type,$email,$username,$name, $street1,$zipCode,$description, $emailConfirmed)
    {
        //registration by administrator
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $login = $this->container->getParameter('cyclos_global_admin_username');
        $password = '@@bbccdd';
        $crawler = $this->login($login, $password);


        $crawler = $this->client->request('GET','/registration/?type='.$type);
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('registration.submit')->form();
        $form['fos_user_registration_form[email]']->setValue($email);
        $form['fos_user_registration_form[username]']->setValue($username);
        $form['fos_user_registration_form[name]']->setValue($name);
        $form['fos_user_registration_form[address][street1]']->setValue($street1);
        $form['fos_user_registration_form[address][zipCity]']->select($zipCode);
        $form['fos_user_registration_form[description]']->setValue($description);

        $crawler =  $this->client->submit($form);

        $crawler = $this->client->followRedirect();


        $this->assertSame(1,$crawler->filter('html:contains("registration.check_email")')->count());

        $crawler = $this->client->request('GET','/logout');

        $newUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$username));

        if($emailConfirmed){
            $this->client->enableProfiler();

            $crawler = $this->client->request('GET','/register/informations/confirm/'.$newUser->getConfirmationToken());

           //assert email
            $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(1, $mailCollector->getMessageCount());
            $message = $mailCollector->getMessages()[0];
            $this->assertInstanceOf('Swift_Message', $message);
            $this->assertContains('Validation de l\'administrateur', $message->getSubject());
            $this->assertContains('confirmé la validité', $message->getBody());
            $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
            $this->assertSame($newUser->getEmail(), key($message->getTo()));

            $crawler = $this->client->followRedirect();


            $crawler = $this->client->followRedirect();
            $this->assertSame(1,$crawler->filter('html:contains("validé votre adresse mail")')->count());
            $this->assertSame(1, $crawler->filter('div.alert-success')->count());    
        }

        $this->em->refresh($newUser);
        $this->assertTrue(!$newUser->isEnabled());
        if($type == 'pro'){
            $this->assertTrue($newUser->hasRole('ROLE_PRO'));
        }
        if($type == 'localGroup'){
            $this->assertTrue($newUser->hasRole('ROLE_ADMIN'));
        }
    }

    public function provideRegistrationUsers()
    {
        return array(
            array('localGroup','gl_grenoble@cairn-monnaie.com','glGrenoble','Groupe Local Grenoble','7 rue Très Cloîtres','38000','Groupe Local de Grenoble',true),
            array('localGroup','gl_voiron@cairn-monnaie.com','glVoiron','Groupe Local Voiron','12 rue Mainssieux','38500','Groupe Local de Voiron',true),
            array('pro','apogee_du_vin@cairn-monnaie.com','ApogeeDuVin','L\'apogée du Vin','8 rue Lesdiguères','38000','Cave à vins',false),
        );
    }

    /**
     *
     *@depends testRegistration
     *@dataProvider provideRegistrationData
     */
    public function testRegistrationValidator($email,$username,$name,$plainPassword,$street1,$zipCity,$description)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $validator = $this->container->get('validator');

        $user = new User();
        $zip = $this->em->getRepository('CairnUserBundle:ZipCity')->findOneBy(array('zipCode'=>$zipCity['zipCode'],'city'=>$zipCity['city']));
        $address = new Address();                                          
        $address->setZipCity($zip);                                        
        $address->setStreet1($street1);                        
                                                                             
        $user->setAddress($address);
        
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setName($name);
        $user->setPlainPassword($plainPassword);
        $user->setDescription($description);
        
        $errors = $validator->validate($user);

        $this->assertEquals(1,count($errors));

    }

    /**
     *
     *@todo : tester les login avec plusieurs points d'affilée, tirets...
     */
    public function provideRegistrationData()
    {
        //WARNING : do not take ROLE_SUPER_ADMIN installed user as base data
        $existingUser = $this->em->getRepository('CairnUserBundle:User')->myFindByRole(array('ROLE_PRO'))[0];
        $usedUsername = $existingUser->getUsername();
        $usedEmail = $existingUser->getEmail();

        //valid user data
        $baseData = array(
            'email'=>'test@cairn-monnaie.com',
            'username' => 'testUser',
            'name' => 'Test User V@lidation',
            'plainPassword'=> '@@bbccde',
            'street1' => '7 rue Très Cloître',
            'zipCity' => array('zipCode'=>'38000', 'city'=> 'Grenoble'),
            'description' => 'This user is used to test the validator'
        );

        return array(
            'invalid email(no @)'                                     => array_replace($baseData, array('email'=>'test.com')),
            'invalid email(not enough characters)'                    => array_replace($baseData, array('email'=>'test@t.c')),
//            'email already in use'                                    => array_replace($baseData, array('email'=>$usedEmail)),
//
            'too short username'                                      => array_replace($baseData, array('username'=>'test')),
            'too long username'                                       => array_replace($baseData, array('username'=>'testTooLongUsername')),
            'username with special character'                         => array_replace($baseData, array('username'=>'test@')),
//            'username already in use'                                 => array_replace($baseData, array('username'=>$usedUsername)),
          'invalid name(too short)'                                 => array_replace($baseData, array('name'=>'AB')),
            'too short password'                                      => array_replace($baseData, array('plainPassword'=>'@bcdefg')),
            'pseudo included in password'                             => array_replace($baseData, array('plainPassword'=>'@testUser@')),
            'no special character'                                    => array_replace($baseData, array('plainPassword'=>'1testPwd2')),
//            'too simple password (all characters have 0 distance)'    => array_replace($baseData, array('plainPassword'=>'@@@@@@@@')),
//            'too obvious password(mot de passe = mot dans le nom)'    => array_replace($baseData, array('password'=>'V@lidation')),
        );
    }



}
