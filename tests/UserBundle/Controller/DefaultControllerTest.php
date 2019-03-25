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

use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            var_dump($res->error);
            $this->assertTrue(strpos($res->error,$expectMessage) !== false);
        }
    }

    public function provideDataForSmsParsing()
    {
        return array(
            'invalid format : no action'=>array('12.5 SHOP', false, true, 'Envoyer PAYER, SOLDE'),
            'invalid format : no amount'=>array('PAYER SHOP',false,true,'Format du montant'),
            'invalid format : negative amount'=>array('PAYER -12.5 SHOP',false,true,'Format du montant'),
            'invalid format : no identifier'=>array('PAYER 12.5',false,true,'identifiant SMS'),
            'invalid format : action SOLDE'=>array('SOLD',false,false,'Envoyer PAYER, SOLDE'),
            'invalid format : action SOLDE + texte'=>array('SOLDE OUT!',false,false,'Demande de solde invalide'),
            'invalid format : code with some letter'=>array('12e74',false,false,'Envoyer PAYER, SOLDE'),
            'invalid format : code with two many figures'=>array('123456',false,false, '4 chiffres'),
            'invalid format : code with not enough figures'=>array('123',false,false,'Envoyer PAYER, SOLDE'),
            'valid format : uppercase'=>array('PAYER 12.5 SHOP',true,true,''),
            'valid format : lowercase'=>array('payer 12.5 shop',true,true,''),
            'valid format : mix upper/lowercase'=>array('PaYer 12.5 SHoP',true,true,''),
            'valid format : action PAYE 2'=>array('PAYE 12.5 SHOP',true,true,''),
            'valid format : action PAYER 3'=>array('PAY 12.5 SHOP',true,true,''),
            'valid format : action PAYER 4'=>array('PAYEZ 12.5 SHOP',true,true,''),
            'valid format : float amount with 3 decimals'=>array('PAYER 12.522 SHOP',true,true,''),
            'valid format : float amount with 6 decimals'=>array('PAYER 12.522000 SHOP',true,true,''),
            'invalid format : float amount with got'=>array('PAYER 12. SHOP',false,true,'Format du montant'),
            'invalid format : float amount with comma'=>array('PAYER 12, SHOP',false,true,'Format du montant'),
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
     * nbEmails does not count for the code if needed. It is only the "conclusion" mails && texts
     *
     *@dataProvider provideDataForSmsOperation
     */
    public function testSmsOperation($phoneNumber, $content, $needsCode, $code, $isValidCode,$expectMessages,$nbEmails = 1)
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$content);

        if($expectMessages){
            //TODO : replace email by sms
            $mailCollector = $client->getProfile()->getCollector('swiftmailer');

            if(! $needsCode){
                $this->assertEquals($nbEmails, $mailCollector->getMessageCount());
                $body = '';

                //we gather all the emails content in one
                for($i=0; $i < $nbEmails; $i++){
                    $message = $mailCollector->getMessages()[$i]->getBody();
                    $body .= $message;
                }

                //then we assert that each expected message is contained in at least one of the emails
                for($i=0; $i < $nbEmails; $i++){
                    $this->assertContains($expectMessages[$i], $body);
                }

            }else{
                $this->assertEquals(1, $mailCollector->getMessageCount());
                $this->assertContains($expectMessages[0], $mailCollector->getMessages()[0]->getBody());
                $client->enableProfiler();
                $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$code);

                if($isValidCode){
                    $mailCollector = $client->getProfile()->getCollector('swiftmailer');

                    $this->assertTrue( $nbEmails == $mailCollector->getMessageCount());

                    $body = '';
                    //we gather all the emails content in one
                    for($i=0; $i < $nbEmails; $i++){
                        $message = $mailCollector->getMessages()[$i]->getBody();
                        $body .= $message;
                    }
    
                    //then we assert that each expected message is contained in at least one of the emails
                    //First expected message is for code, so we start from one
                    for($i=1; $i < $nbEmails+1; $i++){
                        $this->assertContains($expectMessages[$i], $body);
                    }

                }
            }
        }else{
            $mailCollector = $client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(0, $mailCollector->getMessageCount());
        }

    }
  
    /**
     * nbEmails does not count for the code if needed. It is only the "conclusion" mails && texts
     */
    public function provideDataForSmsOperation()
    {
        $askCodeMsg = 'code de sécurité';
        $wrongCodeMsg = 'Échec du code';
        $validDebMsg = 'été accepté';
        $validCredMsg = 'avez reçu';

        return array(
            'balance : phone number not registered'=>array('0612121212','SOLDE',false,'1111',true,NULL),
            'balance : user opposed'=>array('0744444444','SOLDE',false,'1111',true,array('opposition de compte')),
            'balance : valid code + sms for pro & person'=>array('0612345678','SOLDE',true,'1111',true,
                                                                  array($askCodeMsg,'Votre solde compte')),

            'balance : invalid code + sms for pro & person'=>array('0612345678','SOLDE',true,'2222',false,
                                                                    array($askCodeMsg,$wrongCodeMsg)),

            'login : no pro'=>array('0612345678','LOGIN',false,'1111',true,NULL),
            'login : pro + valid code '=>array('0611223344','LOGIN',true,'1111',true,array($askCodeMsg,'Identifiant SMS')),
            'login : pro + wrong code '=>array('0611223344','LOGIN',true,'2222',false,array($askCodeMsg,$wrongCodeMsg)),

            'balance : invalid sms'=>array('0612345678','SOLD',false,'1111',true,array('SMS INVALIDE')),
            'balance : invalid sms'=>array('0612345678','SOLDEADO',false,'1111',true,array('SMS INVALIDE')),
            'payment : wrong creditor identifier'=>array('0612345678','PAYER12.5BOOYASHAKA',false,'1111',true,'aucun professionnel'),
            'payment mistake : person to person with ID SMS'=>array('0612345678','PAYER10CRABEARNOLD',false,'1111',true,NULL),

            'payment : balance error'=>array('0612345678','PAYER1000000MALTOBAR',false,'1111',true,
                                                                array('Solde insuffisant')),
            'payment : creditor has sms disabled'=>array('0612345678','PAYER100DRDBREW',false,'1111',true,array('pas été autorisées pour')),

            'payment : valid,creditor has payments disabled but reception enabled'=>array('0612345678','PAYER10AMANSOL',false,'1111',true,
                                                                                                array($validDebMsg,$validCredMsg),2),

            'payment : debitor has sms disabled'=>array('0733333333','PAYER100MALTOBAR',false,'1111',true,
                                                                                array('opposition aux SMS depuis')),

            'payment : debitor is disabled'=>array('0744444444','PAYER100MALTOBAR',false,'1111',true,array('opposition de compte')),

            'payment : debitor has payments disabled'=>array('0655667788','PAYER100MALT',false,'1111',true,array('opposition aux SMS')),

            'payment : creditor=debitor'=>array('0611223344','PAYER100MALTOBAR',false,'1111',true,
                                                        array('identiques')),

            'payment : too low amount'=>array('0612345678','PAYER0.001MALTOBAR',false,'1111',true,array('trop faible')),
            'payment : valid, no code'=>array('0612345678','PAYER15MALTOBAR',false,'1111',true,array($validDebMsg,$validCredMsg),2),
            'payment : pro to pro,valid, no code'=>array('0611223344','PAYER15NICOPROD',false,'1111',true,array($validDebMsg,$validCredMsg),2),
          'payment : valid + code'=>array('0612345678','PAYER100MALTOBAR',true,'1111',true,array($askCodeMsg,$validDebMsg,$validCredMsg),2),
            'payment : person to pro,valid, no code'=>array('0612345678','PAYER12.522maltobar',false,'1111',true,
                                                                    array($validDebMsg,$validCredMsg),2),
          'payment : valid,no code'=>array('0612345678','PAYER12.5220000maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),
            'payment : invalid sms'=>array('0612345678','PAYER12.maltobar',false,'1111',true,array('SMS INVALIDE')),
            'payment : invalid sms'=>array('0612345678','PAYERSHOP',false,'1111',true,array('Format du montant')),
          'payment : valid amount'=>array('0612345678','PAYER00012maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),
          'payment : valid PAYEZ'=>array('0612345678','PAYEZ00012maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),
          'payment : valid PAYE'=>array('0612345678','PAYE00012maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),
          'payment : valid PAY'=>array('0612345678','PAYE00012maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),

          'payment : invalid access client'=>array('0788888888','PAYER00012maltobar',false,'1111',true,
                                                        array('ERREUR TECHNIQUE','Accès client invalide'),2),

            'validation  : nothing to validate'=>array('0612345678','1111',false,'1111',true,NULL),

            'suspicious payment'=>array('0612345678','PAYER1500maltobar',false,'1111',true,array('SMS bloqués','tentative de paiement','tentative de paiement'),3),

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
//            array('login'=>true,'username'=>$adminUsername,'type'=>'localGroup', 'expectValid'=>true,'expectMessage'=>''),
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
    public function testRegistrationAction($type,$email,$name, $street1,$zipCode,$description, $emailConfirmed)
    {
        $adminUsername = $this->testAdmin;

        //registration by administrator
        $login = $adminUsername;
        $password = '@@bbccdd';
        $crawler = $this->login($login, $password);


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

        $form['fos_user_registration_form[identityDocument][file]']->upload('path/to/photo.png');

        $crawler =  $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertSame(1,$crawler->filter('html:contains("Inscription enregistrée")')->count());
        $this->assertContains('Inscription enregistrée',$this->client->getResponse()->getContent());
        $this->assertContains($email,$this->client->getResponse()->getContent());
        $this->assertContains(htmlspecialchars('lien d\'activation',ENT_QUOTES),$this->client->getResponse()->getContent());

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

            $this->assertSame(1,$crawler->filter('html:contains("validé votre adresse mail")')->count());
        }

        $this->em->refresh($newUser);
        $this->assertFalse($newUser->isEnabled());

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
            'pro grenoble'=>array('pro','hmorgan@test.com','Librairie Harry Morgan','10 rue Millet','38000 Grenoble','Librairie',true),
//            'pro no GL'=>array('pro','hmorgan@test.com','Librairie Harry Morgan','10 rue Millet','38540 Grenay','Librairie',true),
//            'person'=>array('person','john_doe@test.com','John Doe','15 rue du test','38000 Grenoble','Je suis cairnivore',true),
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
