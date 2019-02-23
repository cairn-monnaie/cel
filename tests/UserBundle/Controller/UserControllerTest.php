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

class UserControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     *@dataProvider provideDataForDisableSms
     */
    public function testDisableSms($current, $target, $isReferent,$isValid, $expectMessage)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));
        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $url = '/user/sms/disable/'.$targetUser->getID();
        $crawler = $this->client->request('GET',$url);

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        if(! $isReferent){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            if(! $isValid){
                $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();

                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
            }else{
                $formConfirm = $crawler->selectButton('Confirmer')->form();
                $crawler = $this->client->submit($formConfirm);

                $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();

                $this->em->refresh($targetUser);

                $this->assertFalse($targetUser->getSmsData()->isSmsEnabled());
                $this->assertTrue($targetUser->getSmsData()->getSmsClient() != NULL);

                //assert that access client exists and is BLOCKED
                $crawler = $this->login($target, '@@bbccdd');
                $accessClientVO = $this->container->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($targetUser->getCyclosID(), 'BLOCKED');
                $this->assertTrue($accessClientVO != NULL);

                //assert that access client encoded in database allows to connect to Cyclos
                $networkInfo = $this->container->get('cairn_user_cyclos_network_info');       
                $networkName = $this->container->getParameter('cyclos_currency_cairn');       
                $accessClient = $this->container->get('cairn_user.security')->getSmsClient($targetUser);      

                $networkInfo->switchToNetwork($networkName,'access_client', $accessClient);

                try{
                    $userVO = $this->container->get('cairn_user_cyclos_user_info')->getCurrentUser();

                    //While disabling SMS operations, the access client is blocked for the current user on Cyclos side.
                    //But at the end of the test, the sms client will be rolled back on Symfony side whereas access client will stay
                    //on Cyclos side, breaking up the logic of our application
                    //Workaround : unblock the access client "by hand" at test end
                    //this is a problem regarding isolation tests and Cyclos
                    

                    //this is executed if Cyclos connection is successful, which should not be the case here
                    $this->assertTrue(false);

                }catch(\Exception $e){
                    //normal process is to get refused access
                    if($e->errorCode == 'INVALID_ACCESS_CLIENT'){
                        $this->assertTrue(true);
                        $networkInfo->switchToNetwork($networkName,'login', array('username'=>$target,'password'=>'@@bbccdd'));
                        $this->container->get('cairn_user.security')->changeAccessClientStatus($accessClientVO,'UNBLOCKED');

                    }else{
                        throw $e;
                    }
                }
            }
        }

    }

    public function provideDataForDisableSms()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'valid user request for himself'=>array('current'=>'maltobar','target'=>'maltobar','isReferent'=>true,
                                                    'isValid'=>true,'expectMessage'=>'désormais bloqué'),

          'valid referent request'=>array('current'=>$adminUsername,'target'=>'maltobar','isReferent'=>true,
                                          'isValid'=>true,'expectMessage'=>'désormais bloqué'),

            'already disabled sms'=>array('current'=>'benoit_perso', 'target'=>'benoit_perso','isReferent'=>true,
                                         'isValid'=>false,'expectMessage'=>'déjà bloqué'),

            'no phone number associated'=>array('current'=>'pain_beauvoir', 'target'=>'pain_beauvoir','isReferent'=>true,
                                         'isValid'=>false,'expectMessage'=>'Aucun numéro'),

            'not referent'=>array('current'=>$adminUsername,'target'=>'vie_integrative','isReferent'=>false,
                                            'isValid'=>true,'expectMessage'=>'désormais autorisé'),

        );
    }

    /**
     *@dataProvider provideDataForEnableSms
     */
    public function testEnableSms($current, $target, $isReferent,$isValid, $expectMessage)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));
        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $url = '/user/sms/enable/'.$targetUser->getID();
        $crawler = $this->client->request('GET',$url);

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        if(! $isReferent){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            if(! $isValid){
                $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();

                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
            }else{
                $formConfirm = $crawler->selectButton('Confirmer')->form();
                $crawler = $this->client->submit($formConfirm);

                $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();

                $this->em->refresh($targetUser);

                $this->assertTrue($targetUser->getSmsData()->isSmsEnabled());
                $this->assertTrue($targetUser->getSmsData()->getSmsClient() != NULL);

                //assert that access client exists and is ACTIVE
                $crawler = $this->login($target, '@@bbccdd');
                $accessClientVO = $this->container->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($targetUser->getCyclosID(), 'ACTIVE');
                $this->assertTrue($accessClientVO != NULL);

                //assert that access client encoded in database allows to connect to Cyclos
                $networkInfo = $this->container->get('cairn_user_cyclos_network_info');       
                $networkName = $this->container->getParameter('cyclos_currency_cairn');       
                $accessClient = $this->container->get('cairn_user.security')->getSmsClient($targetUser);      

                $networkInfo->switchToNetwork($networkName,'access_client', $accessClient);

                try{
                    $userVO = $this->container->get('cairn_user_cyclos_user_info')->getCurrentUser();

                    //While enabling SMS operations, an access client is assigned for the current user on Cyclos side.
                    //But at the end of the test, the sms client will be rolled back on Symfony side whereas access client will stay
                    //on Cyclos side, breaking up the logic of our application
                    //Workaround : unassign the access client "by hand" at test end
                    //this is a problem regarding isolation tests and Cyclos
                    $this->container->get('cairn_user.security')->changeAccessClientStatus($accessClientVO,'BLOCKED');

                }catch(\Exception $e){
                    var_dump($e->errorCode);
                    if($e->errorCode == 'INVALID_ACCESS_CLIENT'){
                        var_dump($e->getMessage());
                        $this->assertTrue(false);
                    }else{
                        throw $e;
                    }
                }
            }
        }

    }

    public function provideDataForEnableSms()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'valid user request for himself'=>array('current'=>'benoit_perso','target'=>'benoit_perso','isReferent'=>true,
                                                    'isValid'=>true,'expectMessage'=>'désormais autorisé'),

          'valid referent request'=>array('current'=>$adminUsername,'target'=>'DrDBrew','isReferent'=>true,
                                          'isValid'=>true,'expectMessage'=>'désormais autorisé'),

            'already enabled sms'=>array('current'=>'nico_faus_prod', 'target'=>'nico_faus_prod','isReferent'=>true,
                                         'isValid'=>false,'expectMessage'=>'déjà autorisé'),

            'no phone number associated'=>array('current'=>'pain_beauvoir', 'target'=>'pain_beauvoir','isReferent'=>true,
                                         'isValid'=>false,'expectMessage'=>'Aucun numéro'),

            'not referent'=>array('current'=>$adminUsername,'target'=>'vie_integrative','isReferent'=>false,
                                            'isValid'=>true,'expectMessage'=>'désormais autorisé'),

            'not active'=>array('current'=>$adminUsername,'target'=>'la_mandragore','isReferent'=>true,
                                            'isValid'=>false,'expectMessage'=>'inactif'),
        );
    }

    /**
     * Need to check that UserPhoneNumberValidator is called + that user can make payment with his new number
     *
     *@dataProvider provideDataForEditSmsData
     */
    public function testEditSmsData($login,$newPhoneNumber,$isValidNumber,$isPhoneNumberEdit,$code,$isValidCode,$hasPreviousPhoneNumber,$expectMessage)
    {
        $crawler = $this->login($login, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));

        $url = '/user/sms-data/edit';
        $crawler = $this->client->request('GET',$url);

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        $previous_phoneNumberActivationTries = $currentUser->getPhoneNumberActivationTries();
        $previous_nbPhoneNumberRequests = $currentUser->getNbPhoneNumberRequests();
        $previous_phoneNumber = $currentUser->getPhoneNumber();

        $formPhoneNumber = $crawler->selectButton('cairn_userbundle_smsdata_save')->form();
        $formPhoneNumber['cairn_userbundle_smsdata[phoneNumber]']->setValue($newPhoneNumber);
        $crawler = $this->client->submit($formPhoneNumber);

        if($isPhoneNumberEdit){
            if($isValidNumber){
                $this->em->refresh($currentUser);

                $this->assertEquals($currentUser->getNbPhoneNumberRequests(),$previous_nbPhoneNumberRequests + 1);
                $this->assertTrue($currentUser->getPhoneNumber() == $previous_phoneNumber);
                $this->assertFalse($currentUser->getPhoneNumber() == $newPhoneNumber);
                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());

                $formCode = $crawler->selectButton('form_save')->form();
                $formCode['form[code]']->setValue($code);
                $crawler = $this->client->submit($formCode);

                $this->em->refresh($currentUser);

                if($isValidCode){
                    $this->assertEquals($currentUser->getPhoneNumberActivationTries(),0);

                    $this->assertEquals($currentUser->getNbPhoneNumberRequests(),0);
                    $this->assertEquals($currentUser->getPhoneNumber(),$newPhoneNumber);
                    $this->assertTrue($currentUser->getSmsData() != NULL);
                    $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$currentUser->getID()));
                    $crawler = $this->client->followRedirect();
                    $this->assertContains($expectMessage,$this->client->getResponse()->getContent());

                    //Plus, we assert that access client exists on Cyclos side. It is either ACTIVE or BLOCKED 
                    $status = ($currentUser->getSmsData()->isSmsEnabled()) ? 'ACTIVE' : 'BLOCKED';
                    $accessClientVO = $this->container->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($currentUser->getCyclosID(), $status);
                    $this->assertTrue($accessClientVO != NULL);

                    //if this is first phone number association, an access client is created for the current user on Cyclos side.
                    //But at the end of the test, the phone number will be rolled back on Symfony side whereas access client will stay
                    //on Cyclos side, breaking up the logic of our application
                    //Workaround : removing the access client "by hand" at test end
                    //this is a problem regarding isolation tests and Cyclos
                    if(! $hasPreviousPhoneNumber){
                        $this->container->get('cairn_user.security')->changeAccessClientStatus($accessClientVO,'REMOVED');
                    }
                }else{
                    $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
                    $this->assertEquals($currentUser->getPhoneNumberActivationTries(),$previous_phoneNumberActivationTries + 1);


                    if($currentUser->getPhoneNumberActivationTries() >= 3){
                        $this->assertFalse($currentUser->isEnabled());
                        $this->assertTrue($this->client->getResponse()->isRedirect($url));
                        $crawler = $this->client->followRedirect();
                        $this->assertTrue($this->client->getResponse()->isRedirect('/logout'));
                    }else{
                        $this->assertTrue($currentUser->isEnabled());
                        $this->assertTrue($this->client->getResponse()->isRedirect($url));
                    }
                }
            }else{
                $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
            }
        }else{ //only security values regarding sms may have changed
            $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$currentUser->getID()));
            $crawler = $this->client->followRedirect();
            $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
        }


    }

    public function provideDataForEditSmsData()
    {
        $baseData = array('login'=>'',
            'phoneNumber'=>'0699999999',
            'isValidNumber'=>true,
            'isPhoneNumberEdit'=>true,
            'code'=>'1111',
            'isValidCode'=>true,
            'hasPreviousPhoneNumber'=>true,
            'expectedMessage'=>''
        );

        return array(
            'too many requests'=>array_replace($baseData, array('login'=>'crabe_arnold', 'isValidNumber'=>false,
                                                                'expectMessage'=>'Trop de demandes')),

            'current number'=>array_replace($baseData, array('login'=>'maltobar','phoneNumber'=>'0611223344','isPhoneNumberEdit'=>false,
                                                              'isValidNumber'=>true,'expectMessage'=>'enregistrées')),

            'used by pro & person'=>array_replace($baseData, array('login'=>'maltobar','phoneNumber'=>'0612345678',
                                                             'isValidNumber'=>false,'expectMessage'=>'déjà utilisé')),

            'pro request : used by pro'=>array_replace($baseData, array('login'=>'maltobar','isValidNumber'=>false,
                                                            'phoneNumber'=>'0612345678','expectMessage'=>'déjà utilisé')),

            'person request : used by person'=>array_replace($baseData, array('login'=>'benoit_perso','isValidNumber'=>false,
                                                            'phoneNumber'=>'0612345678','expectMessage'=>'déjà utilisé')),

            'pro request : used by person'=>array_replace($baseData,array('login'=>'maltobar','phoneNumber'=>'0644332211',
                                                                          'expectMessage'=>'Un code vous a été envoyé')),

            'person request : used by pro'=>array_replace($baseData, array('login'=>'benoit_perso','phoneNumber'=>'0611223344',
                                                                            'expectMessage'=>'Un code vous a été envoyé')),

//          'last remaining try : wrong code'=>array_replace($baseData, array('login'=>'hirundo_archi','isValidCode'=>false,
//                                                                  'code'=>'2222','expectMessage'=>'compte a été bloqué')),
//
            'several remaining tries : wrong code'=>array_replace($baseData, array('login'=>'DrDBrew','isValidCode'=>false,
                                                                    'code'=>'2222','expectMessage'=>'Code invalide')),

////            'last remaining try : valid code'=>array_replace($baseData, array('login'=>'hirundo_archi','expectMessage'=>'enregistrées')),
//
            'never had phone number : valid code'=>array_replace($baseData, array('login'=>'jardins_epices',
                                                            'hasPreviousPhoneNumber'=>false,'expectMessage'=>'enregistré')),

            '2 accounts associated before: valid code'=>array_replace($baseData, array('login'=>'nico_faus_prod',
                                                                     'expectMessage'=>'peut désormais réaliser')),
        );
    }

    /**
     * Need to check that UserValidator is called + that user can login with new password later on
     *
     *@dataProvider providePasswordData
     */
    public function testChangePassword($login,$loginpwd,$current, $new, $confirm, $isValid, $expectedMessage)
    {
        $crawler = $this->login($login, $loginpwd);

        $currentUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));

        $url = '/profile/change-password';
        $crawler = $this->client->request('GET',$url);

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('fos_user_change_password_form_save')->form();
        $form['fos_user_change_password_form[current_password]']->setValue($current);
        $form['fos_user_change_password_form[plainPassword][first]']->setValue($new);
        $form['fos_user_change_password_form[plainPassword][second]']->setValue($confirm);
        $crawler = $this->client->submit($form);

        if($isValid){
            $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$currentUser->getID()));
            $crawler = $this->client->followRedirect();

            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            $crawler = $this->login($login, $new);
            $this->assertSame(1,$crawler->filter('html:contains("Espace Professionnel")')->count());
        }else{
            $this->assertSame(1, $crawler->filter('input#fos_user_change_password_form_current_password')->count());    
        }

        //committing modifications
        \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();

        //Right after, we begin a new transaction in order to avoid the execption from PDO "there is no active transaction" which occurs
        //on rollBack (automatically called after each test by DoctrineTestBundle listener) to keep a stable state of the DB
        \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::beginTransaction();

    }

    public function providePasswordData()
    {
        //WARNING : put here the login of an user who will be used ONLY for this specific test and anywhere else
        //because the password is also changed on Cyclos-side. The password will be rolledback on Symfony-side but not on Cyclos-side
        //It will provok an exception "LOGIN" and no other test will work as we need user to login and expect password to be 
        //@@bbccdd// In order to be able to chain data provided, we must commit changes at the end of the test before the rollback
        $login = 'denis_ketels';

        //keep the same password as the new one, because the password value is rolled back on MySQL BDD, but not on Cyclos side.
        //This would result in a dissociation of passwords
        $new = '@@bbccdd';

        //invalid data
        $baseData = array('login'=>$login,
            'loginpwd'=>'@@bbccdd',
            'current'=>'@@bbccdd',
            'new'=>$new,
            'confirm'=>$new,
            'expectValid'=>true,
            'expectedMessage'=>'succès'
        );

        return array(
            'invalid current'             => array_replace($baseData, array('current'=>'@bbccdd','expectValid'=>false)),          
            'new != confirm'              => array_replace($baseData, array('confirm'=>'@bcdefg','expectValid'=>false,
            'expectedMessage'=>'correspondent pas')),          
            'too short new password'      => array_replace($baseData, array('new'=>'@bcdefg','confirm'=>'@bcdefg','expectValid'=>false,
            'expectedMessage'=>'plus de 8 caractères')),          
            'pseudo included in password' => array_replace($baseData, array('new'=>'@'.$login.'@','confirm'=>'@'.$login.'@','expectValid'=>false,
            'expectedMessage'=>'contenu dans le mot de passe')),
            'no special character'        => array_replace($baseData, array('new'=>'1testPwd2' ,'confirm'=>'1testPwd2','expectValid'=>false,
            'expectedMessage'=>'caractère spécial')),
            'new = current'               => array_replace($baseData, array('expectValid'=>false)),          
//            'valid'               => array_replace($baseData, array('new'=>'@bcdefgh','confirm'=>'@bcdefgh')),          
//            'valid back'               => array_replace($baseData, array('loginpwd'=>'@bcdefgh','current'=>'@bcdefgh',
//                                                                    'new'=>'@@bbccdd','confirm'=>'@@bbccdd')),          
        );
    }

    /**
     *
     *@dataProvider provideReferentsAndTargets
     */
    public function testViewProfile($referent,$target,$isReferent)
    {
        $crawler = $this->login($referent, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','user/profile/view/'.$targetUser->getID());

        //        $this->assertContains(htmlspecialchars($targetUser->getName()),$this->client->getResponse()->getContent());
        $this->assertContains($targetUser->getUsername(),$this->client->getResponse()->getContent());
        $this->assertContains($targetUser->getEmail(),$this->client->getResponse()->getContent());
        $this->assertContains(htmlspecialchars($targetUser->getDescription()),$this->client->getResponse()->getContent());
        $this->assertContains(htmlspecialchars($targetUser->getCity()),$this->client->getResponse()->getContent());
        $this->assertContains(htmlspecialchars($targetUser->getAddress()->getStreet1()),$this->client->getResponse()->getContent());
        $this->assertContains($targetUser->getAddress()->getZipCity()->getZipCode(),$this->client->getResponse()->getContent());

        if($targetUser->getPhoneNumber()){
            if($targetUser->getPhoneNumberValidationCode()){
                $this->assertNotContains($targetUser->getPhoneNumber(),$this->client->getResponse()->getContent());
            }else{
                 $this->assertContains($targetUser->getPhoneNumber(),$this->client->getResponse()->getContent());
            }

        }

        if($targetUser->hasRole('ROLE_PRO')){
            if($currentUser->hasRole('ROLE_ADMIN')){
                $this->assertSame(1,$crawler->filter('html:contains("groupe local référent")')->count());
                $this->assertSame(0,$crawler->filter('a[href*="user/referents/assign"]')->count());
            }elseif($currentUser->hasRole('ROLE_SUPER_ADMIN')){
                $this->assertSame(1,$crawler->filter('html:contains("groupe local référent")')->count());
                $this->assertSame(1,$crawler->filter('a[href*="user/referents/assign"]')->count());
            }else{
                $this->assertSame(1,$crawler->filter('html:contains("groupe local référent")')->count());
                $this->assertSame(0,$crawler->filter('a[href*="user/referents/assign"]')->count());
            }
        }
        if( ($isReferent || $targetUser === $currentUser)){
            $this->assertSame(1,$crawler->filter('a[href*="user/remove"]')->count());

            if($isReferent){
                $this->assertSame(1,$crawler->filter('a.user_access')->count());
            }else{
                $this->assertSame(0,$crawler->filter('a.user_access')->count());
            }

            if($targetUser == $currentUser){
                $this->assertSame(1,$crawler->filter('a[href*="password/new"]')->count());
                $this->assertSame(1,$crawler->filter('a[href*="profile/edit"]')->count());
            }else{
                $this->assertSame(0,$crawler->filter('a[href*="password/new"]')->count());
                $this->assertSame(0,$crawler->filter('a[href*="profile/edit"]')->count());
            }
        }else{
            $this->assertSame(0,$crawler->filter('a[href*="card/home"]')->count());
            $this->assertSame(0,$crawler->filter('a[href*="user/remove"]')->count());
        }
    }

    public function provideReferentsAndTargets()
    {

        $adminUsername = $this->testAdmin;
        return array(
            array('referent'=>$adminUsername,'target'=>$adminUsername,'isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'DrDBrew','isReferent'=>true),
            array('referent'=>$adminUsername,'target'=>'NaturaVie','isReferent'=>false),
            array('referent'=>'hirundo_archi','target'=>'hirundo_archi','isReferent'=>false),
            array('referent'=>'maltobar','target'=>'maltobar','isReferent'=>false),
            array('referent'=>$adminUsername,'target'=>'nico_faus_prod','isReferent'=>true),

        );
    }


    /**
     *@todo : try to remove a ROLE_ADMIN
     *@todo :check that all beneficiaries with user $target have been removed
     *@todo : try to remove user who is stakeholder of a given operation
     *@dataProvider provideUsersToRemove
     */
    public function testRemoveUser($referent,$target,$isLegit,$nullAccount,$isPending)
    {
        $crawler = $this->login($referent,'@@bbccdd');

        $operationRepo = $this->em->getRepository('CairnUserBundle:Operation');
        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        //sensible operation
        $url = '/user/remove/'.$targetUser->getID();
        $crawler = $this->client->request('GET',$url);
        $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if(! $isLegit){
            //access denied exception
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            if(!$nullAccount){
                $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
                $crawler = $this->client->followRedirect();
                $this->assertSame(1,$crawler->filter('html:contains("solde non nul")')->count());
            }else{
                $this->client->enableProfiler();

                $saveName = $targetUser->getName();

                $form = $crawler->selectButton('confirmation_save')->form();
                $form['confirmation[current_password]']->setValue('@@bbccdd');
                $crawler =  $this->client->submit($form);

                if(! $isPending){
                    //assert email sent to referents
                    $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
                    $this->assertTrue($mailCollector->getMessageCount() >= 1);
                    $message = $mailCollector->getMessages()[0];
                    $this->assertInstanceOf('Swift_Message', $message);
                    //                    $this->assertContains('Nouvelle carte', $message->getSubject());
                    $this->assertContains('supprimé de la plateforme', $message->getBody());
                    $this->assertContains($currentUser->getName(), $message->getBody());

                    $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                    $this->assertSame($targetUser->getEmail(), key($message->getTo()));

                    $this->assertTrue($this->client->getResponse()->isRedirect());
                    $crawler = $this->client->followRedirect();

                    $operations = $operationRepo->findBy(array('stakeholderName'=>$saveName));
                    $this->assertTrue( count($operations) != 0);

                    foreach($operations as $operation){
                        $this->assertEquals($operation->getStakeholder(),NULL);
                    }

                    $this->em->refresh($targetUser);

                    $this->assertEquals($targetUser,NULL);
                    $this->assertSame(1,$crawler->filter('html:contains("supprimé avec succès")')->count());
                    $this->assertSame(1,$crawler->filter('div.alert-success')->count());    

                    //check that operations involving removed user as stakeholder do still exist with stakeholder = NULL and stakeholderName with value
                }else{
                    $this->assertTrue($this->client->getResponse()->isRedirect('/logout'));
                    $crawler = $this->client->followRedirect();

                    $this->em->refresh($targetUser);

                    $this->assertNotEquals($targetUser,NULL);
                    $this->assertEquals($targetUser->getRemovalRequest(),true);
                    $this->assertEquals($targetUser->isEnabled(),false);

                }
            }       
        }
    }

    /**
     *@TODO : add user removing himself who is under admin's responsiblity (and not admin..)
     * only pros from Grenoble have non null accounts (see script to generate users and initial payments : init_test_data.py)
     */
    public function provideUsersToRemove()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'non null account' => array($adminUsername,'atelier_eltilo',true,false,false),
            'valid admin removal, user as operation stakeholder' => array($adminUsername,'trankilou',true,true,false),
            'not legit' => array($adminUsername,'NaturaVie',false,true,false),
            'user auto-removal' => array('lib_colibri','lib_colibri',true,true,true),
        );

    }

}
