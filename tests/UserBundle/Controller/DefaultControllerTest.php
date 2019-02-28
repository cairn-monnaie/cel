<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Cairn\UserBundle\Controller\SmsController;
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

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

    }

    /**
     *
     *@dataProvider provideDataForSmsParsing
     */
    public function testSmsParsing($content, $isValid, $isPayment, $expectMessage)
    {
        $controller = new SmsController();
        $res = $controller->parseSms($content);
        if($isValid){
            $this->assertTrue($res->error == NULL);
            if($isPayment){
                $this->assertTrue($res->isPaymentRequest);
                $this->assertTrue(is_numeric($res->amount) );
            }
        }else{
            $this->assertTrue(strpos($res->error,$expectMessage) !== false);
        }
    }

    public function provideDataForSmsParsing()
    {
        return array(
            'invalid format : no action'=>array('12.5 SHOP', false, true, 'Action invalide'),
            'invalid format : no amount'=>array('PAYER SHOP',false,true,'Format du montant'),
            'invalid format : negative amount'=>array('PAYER -12.5 SHOP',false,true,'Format du montant'),
            'invalid format : no identifier'=>array('PAYER 12.5',false,true,'IDENTIFIANT INCONNU'),
            'invalid format : action SOLDE'=>array('SOLD',false,false,'Action invalide'),
            'invalid format : action SOLDE + texte'=>array('SOLDE OUT!',false,false,'Demande de solde invalide'),
            'invalid format : code with some letter'=>array('12e74',false,false,'Action invalide'),
            'invalid format : code with two many figures'=>array('123456',false,false, '4 chiffres'),
            'invalid format : code with not enough figures'=>array('123',false,false,'Action invalide'),
            'valid format : uppercase'=>array('PAYER 12.5 SHOP',true,true,''),
            'valid format : lowercase'=>array('payer 12.5 shop',true,true,''),
            'valid format : mix upper/lowercase'=>array('PaYer 12.5 SHoP',true,true,''),
            'invalid format : action PAYER 2'=>array('PAYE 12.5 SHOP',false,true,'Action invalide'),
            'invalid format : action PAYER 3'=>array('PAY 12.5 SHOP',false,true,'Action invalide'),
            'valid format : float amount with 3 decimals'=>array('PAYER 12.522 SHOP',true,true,''),
            'valid format : float amount with 6 decimals'=>array('PAYER 12.522000 SHOP',true,true,''),
            'invalid format : float amount with 0 decimals'=>array('PAYER 12. SHOP',false,true,'Format du montant'),
            'invalid format : float amount with 0 decimals'=>array('PAYER 12, SHOP',false,true,'Format du montant'),
            'valid format : float amount with . character'=>array('PAYER 12.5 SHOP',true,true,''),
            'valid format : float amount with , character'=>array('PAYER 12,5 SHOP',true,true,''),
            'valid format : amount with 1 decimal'=>array('PAYER 12.5 SHOP',true,true,''),
            'valid format : amount with 2 decimals'=>array('PAYER 12.52 SHOP',true,true,''),
            'valid format : integer amount'=>array('PAYER 12 SHOP',true,true,''),
            'valid format : integer amount with useless 0s before'=>array('PAYER 00012 SHOP',true,true,''),
            'valid format : username starts with figures'=> array('PAYER 12.5 12SHOP',true,true,''),
            'valid format : no whitespace'=> array('PAYER12.5SHOP',true,true,''),
            'valid format : random spaces'=> array('       PAYER12. 5    SH OP',true,true,''),
            'valid format : SOLDE uppercase'=> array('SOLDE',true,false,''),
            'valid format : SOLDE lowercase'=> array('solde',true,false,''),
            'valid format : SOLDE mix upper/lowercase'=> array('SoLDe',true,false,''),
            'valid format : LOGIN mix upper/lowercase'=> array('LogiN',true,false,''),
            'valid format : LOGIN lowercase'=> array('login',true,false,''),
            'valid format : LOGIN uppercase'=> array('LOGIN',true,false,''),

        );
    }

     /**
     *
     *@dataProvider provideDataForSmsOperation
     */
    public function testSmsOperation($phoneNumber, $content, $needsCode, $code, $isValidCode,$expectMessages)
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$content);

        if($expectMessages){
            //TODO : replace email by sms
            $mailCollector = $client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(1, $mailCollector->getMessageCount());
            $message = $mailCollector->getMessages()[0];
            $this->assertInstanceOf('Swift_Message', $message);
            $this->assertContains($expectMessages[0], $message->getBody());
            $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));

            if($needsCode){
                $client->enableProfiler();
                $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$code);

                if($isValidCode){
                    $mailCollector = $client->getProfile()->getCollector('swiftmailer');
                    $this->assertSame(1, $mailCollector->getMessageCount());
                    $message = $mailCollector->getMessages()[0];
                    $this->assertInstanceOf('Swift_Message', $message);
                    $this->assertContains($expectMessages[1], $message->getBody());
                }else{
                    $mailCollector = $client->getProfile()->getCollector('swiftmailer');
                    $this->assertSame(1, $mailCollector->getMessageCount());
                    $message = $mailCollector->getMessages()[0];
                    $this->assertInstanceOf('Swift_Message', $message);
                    $this->assertContains($expectMessages[1], $message->getBody());
                }
            }
            //committing modifications
//            \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();
//            \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::beginTransaction();
        }else{
            $mailCollector = $client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(0, $mailCollector->getMessageCount());
        }

    }
  
    public function provideDataForSmsOperation()
    {
        $wrongCodeMsg = 'ECHEC CODE';
        $validPaymentMsg = 'données du paiement';
        return array(
            'balance : phone number not registered'=>array('0612121212','SOLDE',false,'1111',true,array('COMPTE E-CAIRN INTROUVABLE')),
            'balance : not active'=>array('0744444444','SOLDE',false,'1111',true,array('inactif')),
            'balance : valid code + sms for pro & person'=>array('0612345678','SOLDE',true,'1111',true,
                                                                    array('CONFIRMER CODE','SOLDE COMPTE E-CAIRN')),

            'balance : invalid code + sms for pro & person'=>array('0612345678','SOLDE',true,'2222',false,
                                                                    array('CONFIRMER CODE',$wrongCodeMsg)),

            'login : no pro'=>array('0612345678','LOGIN',false,'1111',true,NULL),
            'login : pro + valid code '=>array('0611223344','LOGIN',true,'1111',true,array('CONFIRMER CODE','IDENTIFIANT SMS E-CAIRN')),
            'login : pro + wrong code '=>array('0611223344','LOGIN',true,'2222',false,array('CONFIRMER CODE',$wrongCodeMsg)),

            'balance : invalid sms'=>array('0612345678','SOLD',false,'1111',true,array('SMS INVALIDE')),
            'balance : invalid sms'=>array('0612345678','SOLDEADO',false,'1111',true,array('SMS INVALIDE')),
            'payment : wrong creditor identifier'=>array('0612345678','PAYER12.5BOOYASHAKA',false,'1111',true,
                                                                array('IDENTIFIANT SMS INTROUVABLE')),
            'payment mistake : person to person with ID SMS'=>array('0612345678','PAYER10CRABEARNOLD',false,'1111',true,
                                                                array('CREDITEUR NON PRO')),

            'payment : balance error'=>array('0612345678','PAYER1000000MALTOBAR',false,'1111',true,
                                                                array('SOLDE INSUFFISANT')),
            'payment : creditor inactive'=>array('0612345678','PAYER100MANDRAGORE',false,'1111',true,array('CREDITEUR INACTIF')),
            'payment : creditor has sms disabled'=>array('0612345678','PAYER100DRDBREW',false,'1111',true,array('NON AUTORISÉ')),
            'payment : debitor has sms disabled'=>array('0733333333','PAYER100MALTOBAR',false,'1111',true,array('NON AUTORISÉ')),

            'payment : creditor=debitor'=>array('0611223344','PAYER100MALTOBAR',false,'1111',true,
                                                        array('DEBITEUR ET CREDITEUR IDENTIQUES')),

            'payment : too low amount'=>array('0612345678','PAYER0.001MALTOBAR',false,'1111',true,array('MONTANT TROP FAIBLE')),
            'payment : valid, no code'=>array('0612345678','PAYER15MALTOBAR',false,'1111',true,array($validPaymentMsg)),
            'payment : pro to pro,valid, no code'=>array('0611223344','PAYER15NICOPROD',false,'1111',true,array($validPaymentMsg)),
            'payment : valid + code'=>array('0612345678','PAYER100MALTOBAR',true,'1111',true,array('CONFIRMER CODE',$validPaymentMsg)),
            'payment : person to pro,valid, no code'=>array('0612345678','PAYER12.522maltobar',false,'1111',true,array($validPaymentMsg)),
          'payment : valid, no code'=>array('0612345678','PAYER12.5220000maltobar',false,'1111',true,array($validPaymentMsg)),
            'payment : invalid sms'=>array('0612345678','PAYER12.maltobar',false,'1111',true,array('SMS INVALIDE')),
            'payment : invalid sms'=>array('0612345678','PAYERSHOP',false,'1111',true,array('Format du montant')),
            'payment : valid sms'=>array('0612345678','PAYER00012maltobar',false,'1111',true,array($validPaymentMsg)),
        );
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

        $crawler = $this->client->request('GET','/inscription?type='.$type);
        if(!$expectValid){
            $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
        }else{
            $this->assertTrue($this->client->getResponse()->isRedirect('/register/informations/?type='.$type));
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
            array('login'=>false,'username'=>'','type'=>'', 'expectValid'=>false,'expectMessage'=>'Qui êtes-vous'),
            array('login'=>false,'username'=>'','type'=>'xxx', 'expectValid'=>false,'expectMessage'=>'Qui êtes-vous'),
        );
    }

    /**
     *@dataProvider provideRegistrationUsers
     */
    public function testRegistration($type,$email,$name, $street1,$zipCode,$description, $emailConfirmed)
    {
        $adminUsername = $this->testAdmin;

        //registration by administrator
        $login = $adminUsername;
        $password = '@@bbccdd';
        $crawler = $this->login($login, $password);


        $crawler = $this->client->request('GET','/inscription?type='.$type);
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('Inscription')->form();
        $form['fos_user_registration_form[email]']->setValue($email);
        $form['fos_user_registration_form[name]']->setValue($name);
        $form['fos_user_registration_form[address][street1]']->setValue($street1);
        $form['fos_user_registration_form[address][zipCity]']->select($zipCode);
        $form['fos_user_registration_form[description]']->setValue($description);

        $crawler =  $this->client->submit($form);

        $crawler = $this->client->followRedirect();


        $this->assertSame(1,$crawler->filter('html:contains("registration.check_email")')->count());

        $crawler = $this->client->request('GET','/logout');

        $newUser = $this->em->getRepository('CairnUserBundle:User')->findOneByEmail($email);

        if($emailConfirmed){
            $this->client->enableProfiler();

            $crawler = $this->client->request('GET','/register/informations/confirm/'.$newUser->getConfirmationToken());

            //assert email of email validation
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
//            array('localGroup','gl_grenoble@cairn-monnaie.com','Groupe Local Grenoble','7 rue Très Cloîtres','38000','Groupe Local de Grenoble',true),
//            array('localGroup','gl_voiron@cairn-monnaie.com','Groupe Local Voiron','12 rue Mainssieux','38500','Groupe Local de Voiron',true),
            array('pro','lib_harry_morgan@test.com','Librairie Harry Morgan','10 rue Millet','38000','Librairie',false),
            array('person','john_doe@test.com','John Doe','15 rue du test','38000','Je suis cairnivore',false),
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
            //            'email already in use'                                    => array_replace($baseData, array('email'=>$usedEmail)),
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
