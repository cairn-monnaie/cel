<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Address;

class DefaultControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

    }

    /**
     *@dataProvider provideTypeForRegistration
     */
    public function testRegistrationPage($login,$username,$type,$expectValid,$expectMessage)
    {
        $crawler = $this->client->request('GET','/logout');

        if($login){
            $crawler = $this->login($username, '@@bbccdd');
            $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$username));
        }else{
            $currentUser = NULL;
        }

        $crawler = $this->client->request('GET','/inscription/'.$type);
        if(!$expectValid){
            if($this->client->getResponse()->isRedirect()){
                $isRedirectToLogin = $this->client->getResponse()->isRedirect('http://localhost/login');
                $isRedirectToSubmit = $this->client->getResponse()->isRedirect('/inscription/');
                $this->assertTrue($isRedirectToLogin || $isRedirectToSubmit);
            }else{
                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
            }
        }else{
            $this->assertContains('fos_user_registration_form',$this->client->getResponse()->getContent());
        }
    }

    public function provideTypeForRegistration()
    {
        $adminUsername = $this->testAdmin;

        return array(
            array('login'=>true,'username'=>'labonnepioche','type'=>'', 'expectValid'=>false,'expectMessage'=>'déjà un espace membre'), 
            array('login'=>true,'username'=>'comblant_michel','type'=>'', 'expectValid'=>false,'expectMessage'=>'déjà un espace membre'), 
            array('login'=>true,'username'=>$adminUsername,'type'=>'localGroup', 'expectValid'=>true,'expectMessage'=>''),
            array('login'=>true,'username'=>$adminUsername,'type'=>'pro', 'expectValid'=>true, 'expectMessage'=>''),
            array('login'=>true,'username'=>$adminUsername,'type'=>'person', 'expectValid'=>true, 'expectMessage'=>''),
            array('login'=>false,'username'=>'','type'=>'pro', 'expectValid'=>true,'expectMessage'=>''),
            array('login'=>false,'username'=>'','type'=>'person', 'expectValid'=>true,'expectMessage'=>''),
            array('login'=>false,'username'=>'','type'=>'localGroup', 'expectValid'=>false,'expectMessage'=>'pas les droits'),
            array('login'=>false,'username'=>'','type'=>'xxx', 'expectValid'=>false,'expectMessage'=>'Qui êtes-vous'),
        );
    }

    /**
     *@dataProvider provideRegistrationUsers
     */
    public function testRegistrationAction($isLoggedAdmin,$type,$email,$name, $street1,$zipCode,$description, $emailConfirmed)
    {
        $adminUsername = $this->testAdmin;

        //registration by administrator
        $login = $adminUsername;
        $password = '@@bbccdd';

        if($isLoggedAdmin){
            $crawler = $this->login($login, $password);
        }

        $crawler = $this->client->request('GET','/inscription/'.$type);

        $form = $crawler->selectButton('Inscription')->form();
        $form['fos_user_registration_form[email]']->setValue($email);
        $form['fos_user_registration_form[name]']->setValue($name);
        $form['fos_user_registration_form[address][street1]']->setValue($street1);
        $form['fos_user_registration_form[address][zipCity]']->setValue($zipCode);
        $form['fos_user_registration_form[description]']->setValue($description);

        $this->assertNotContains('fos_user_registration_form[username]',$this->client->getResponse()->getContent());

        if( $type == 'adherent'){
            $this->assertNotContains('fos_user_registration_form[image]',$this->client->getResponse()->getContent());
        }

        $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
        $originalName = 'john-doe-id.png';                                 
        $absolutePath = $absoluteWebDir.$originalName;
        $form['fos_user_registration_form[identityDocument][file]']->upload($absolutePath);

        $crawler =  $this->client->submit($form);

        $newUser = $this->em->getRepository('CairnUserBundle:User')->findOneByEmail($email);
        $this->assertFalse($newUser->isEnabled());

        if($isLoggedAdmin){
            $crawler = $this->client->followRedirect();
            $this->assertNull($newUser->getConfirmationToken());
            
            return;
        }else{
            $crawler = $this->client->followRedirect();

            $this->assertNotNull($newUser->getConfirmationToken());

            $this->assertSame(1,$crawler->filter('html:contains("Inscription enregistrée")')->count());
            $this->assertContains('Inscription enregistrée',$this->client->getResponse()->getContent());
            $this->assertContains($email,$this->client->getResponse()->getContent());
            $this->assertContains(htmlspecialchars('lien d\'activation',ENT_QUOTES),$this->client->getResponse()->getContent());
        }
        $crawler = $this->client->request('GET','/logout');

        
        if($emailConfirmed){
            $this->client->enableProfiler();

            $crawler = $this->client->request('GET','/register/informations/confirm/'.$newUser->getConfirmationToken());

            //assert email of email validation
            $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(1, $mailCollector->getMessageCount());
            $message = $mailCollector->getMessages()[0];
            $this->assertInstanceOf('Swift_Message', $message);
            $this->assertContains('Adresse mail [e]-Cairn', $message->getSubject());
            $this->assertContains('confirmé la validité', $message->getBody());
            $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
            $this->assertSame($newUser->getEmail(), key($message->getTo()));

            $crawler = $this->client->followRedirect();

            $this->assertSame(1,$crawler->filter('html:contains("votre adresse électronique")')->count());
        }

        $this->em->refresh($newUser);
        $this->assertFalse($newUser->isEnabled());
        $this->assertNull($newUser->getConfirmationToken());

        if($type == 'pro'){
            $this->assertTrue($newUser->hasRole('ROLE_PRO'));
        }elseif($type == 'person'){
            $this->assertTrue($newUser->hasRole('ROLE_PERSON'));
        }elseif($type == 'localGroup'){
            $this->assertTrue($newUser->hasRole('ROLE_ADMIN'));
        }
    }

    public function provideRegistrationUsers()
    {
        return array(
            'pro grenoble by himself'=>array(false,'pro','hmorgan@test.com','Librairie Harry Morgan','10 rue Millet','38000 Grenoble','Librairie',true),
            'pro grenoble by admin'=>array(true,'pro','hmorgan@test.com','Librairie Harry Morgan','10 rue Millet','38000 Grenoble','Librairie',true),
            'pro no GL by himself'=>array(false,'pro','hmorgan@test.com','Librairie Harry Morgan','10 rue Millet','38540 Grenay','Librairie',true),
            'pro no GL by admin'=>array(true,'pro','hmorgan@test.com','Librairie Harry Morgan','10 rue Millet','38540 Grenay','Librairie',true),
            'person by himself'=>array(false,'person','john_doe@test.com','John Doe','15 rue du test','38000 Grenoble','Je suis cairnivore',true),
            'person by admin'=>array(true,'person','john_doe@test.com','John Doe','15 rue du test','38000 Grenoble','Je suis cairnivore',true),
        );
    }

    /**
     *
     *@dataProvider provideRegistrationData
     */
    public function testRegistrationValidator($email,$username,$name,$plainPassword,$street1,$zipCity,$description)
    {
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
            'invalid name(too short)'                                 => array_replace($baseData, array('name'=>'AB')),
            'too short password'                                      => array_replace($baseData, array('plainPassword'=>'@bcdefg')),
            'pseudo included in password'                             => array_replace($baseData, array('plainPassword'=>'@testUser@')),
            'no special character'                                    => array_replace($baseData, array('plainPassword'=>'1testPwd2')),
        );
    }



}
