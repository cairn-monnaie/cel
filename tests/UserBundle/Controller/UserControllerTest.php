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
     * Need to check that UserPhoneNumberValidator is called + that user can make payment with his new number
     *
     *@dataProvider provideDataForEditSmsData
     */
    public function testEditSmsData($login,$target,$isExpectedForm, $smsData,$isValidData,$isPhoneNumberEdit,$code,$isValidCode,$hasPreviousPhoneNumber,$isSmsEnabled, $expectedMessages)
    {
        $crawler = $this->login($login, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $url = '/user/sms-data/edit/'.$targetUser->getID();
        $crawler = $this->client->request('GET',$url);

        $crawler = $this->client->followRedirect();

        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        $previous_phoneNumberActivationTries = $currentUser->getPhoneNumberActivationTries();
        $previous_nbPhoneNumberRequests = $currentUser->getNbPhoneNumberRequests();
        $previous_phoneNumber = $currentUser->getPhoneNumber();

        $isReferent = $targetUser->hasReferent($currentUser);

        if(! ($currentUser === $targetUser || $isReferent)){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }elseif(! $isExpectedForm){
            $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
            $crawler = $this->client->followRedirect();
            $this->assertContains($expectedMessages,$this->client->getResponse()->getContent());

        }else{
            $formSmsData = $crawler->selectButton('cairn_userbundle_smsdata_save')->form();

            if($currentUser->isAdmin()){
                $identifierField = $formSmsData['cairn_userbundle_smsdata[identifier]'];
                $this->assertNotContains('cairn_userbundle_smsdata[phoneNumber]',$this->client->getResponse()->getContent());

                if($currentUser->hasRole('ROLE_SUPER_ADMIN')){
                    $this->assertFalse($identifierField->isDisabled());
                    $identifierField->setValue($smsData['identifier']);
                }else{
                    $this->assertTrue($identifierField->isDisabled());
                }
            }else{
                $formSmsData['cairn_userbundle_smsdata[phoneNumber]']->setValue($smsData['phoneNumber']);
            }

            if(!$isSmsEnabled){
                $formSmsData['cairn_userbundle_smsdata[smsEnabled]']->untick();
            }else{
                $formSmsData['cairn_userbundle_smsdata[smsEnabled]']->tick();
            }

//            $formPhoneNumber['cairn_userbundle_smsdata[dailyNumberPaymentsThreshold]']->setValue($smsData['paymentsPerDay']);
//            $formPhoneNumber['cairn_userbundle_smsdata[dailyAmountThreshold]']->setValue($smsData['amountPerDay']);

            $this->assertNotContains('dailyNumberPaymentsThreshold',$this->client->getResponse()->getContent());
            $this->assertNotContains('dailyAmountThreshold',$this->client->getResponse()->getContent());

            $crawler = $this->client->submit($formSmsData);

            $this->em->refresh($currentUser);
            $this->em->refresh($targetUser);

            if($isPhoneNumberEdit){
                if($isValidData){

                    $this->assertEquals($currentUser->getNbPhoneNumberRequests(),$previous_nbPhoneNumberRequests + 1);
                    $this->assertTrue($currentUser->getPhoneNumber() == $previous_phoneNumber);
                    $this->assertFalse($currentUser->getPhoneNumber() == $smsData['phoneNumber']);
                    $this->assertContains($expectedMessages[0],$this->client->getResponse()->getContent());

                    $formCode = $crawler->selectButton('form_save')->form();
                    $formCode['form[code]']->setValue($code);
                    $crawler = $this->client->submit($formCode);

                    $this->em->refresh($currentUser);

                    if($isValidCode){
                        $this->assertEquals($currentUser->getPhoneNumberActivationTries(),0);
                        $this->assertEquals($currentUser->getNbPhoneNumberRequests(),0);

                        $this->assertEquals($targetUser->getPhoneNumber(),$smsData['phoneNumber']);
                        $this->assertTrue($targetUser->getSmsData() != NULL);
                        $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
                        $crawler = $this->client->followRedirect();
                        $this->assertContains($expectedMessages[1],$this->client->getResponse()->getContent());

                        //Plus, we assert that access client exists on Cyclos side. It is either ACTIVE or BLOCKED 
//                        $status = ($currentUser->getSmsData()->isSmsEnabled()) ? 'ACTIVE' : 'BLOCKED';
                        $accessClientVO = $this->container->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($targetUser->getCyclosID(), 'ACTIVE');
                        $this->assertTrue($accessClientVO != NULL);

                        //if this is first phone number association, an access client is created for the current user on Cyclos side.
                        //But at the end of the test, the phone number will be rolled back on Symfony side whereas access client will stay
                        //on Cyclos side, breaking up the logic of our application
                        //Workaround : removing the access client "by hand" at test end
                        //this is a problem regarding isolation tests and Cyclos
                        if(! $hasPreviousPhoneNumber){
                            $this->container->get('cairn_user.security')->changeAccessClientStatus($accessClientVO,'REMOVED');
                        }

                        if($isSmsEnabled){
                            $this->assertTrue($targetUser->getSmsData()->isSmsEnabled());
                        }else{
                            $this->assertFalse($targetUser->getSmsData()->isSmsEnabled());
                        }
                    }else{
                        $this->assertTrue($this->client->getResponse()->isRedirect($url));
                        $crawler = $this->client->followRedirect();

                        $this->assertEquals($currentUser->getPhoneNumberActivationTries(),$previous_phoneNumberActivationTries + 1);


                        if($currentUser->getPhoneNumberActivationTries() >= 3){
                            $this->assertTrue($this->client->getResponse()->isRedirect('/logout'));
                            $crawler = $this->client->followRedirect();
                            $crawler = $this->client->followRedirect();

                            $this->assertUserIsDisabled($currentUser,true);

                            $this->assertContains($expectedMessages,$this->client->getResponse()->getContent());

                        }else{
                            $this->assertUserIsEnabled($currentUser, false);
//                            $this->assertTrue($this->client->getResponse()->isRedirect($url));
//                            $crawler = $this->client->followRedirect();

                            $this->assertContains($expectedMessages,$this->client->getResponse()->getContent());

                        }
                    }
                }else{//assert nothing changed
                    $this->assertEquals($currentUser->getNbPhoneNumberRequests(),$previous_nbPhoneNumberRequests);
                    $this->assertTrue($targetUser->getPhoneNumber() == $previous_phoneNumber);
                    $this->assertFalse($targetUser->getPhoneNumber() == $smsData['phoneNumber']);

                    $this->assertContains($expectedMessages,$this->client->getResponse()->getContent());
                }
            }else{ //phone number did not change
                if($isValidData){
                    $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
                    $crawler = $this->client->followRedirect();
                    $this->assertContains($expectedMessages,$this->client->getResponse()->getContent());


                    if($isSmsEnabled){
                        $this->assertTrue($targetUser->getSmsData()->isSmsEnabled());
                    }else{
                        $this->assertFalse($targetUser->getSmsData()->isSmsEnabled());
                    }

                }else{
                    $this->assertFalse($this->client->getResponse()->isRedirect());
                    $this->assertContains($expectedMessages,$this->client->getResponse()->getContent());
                }
            }

        }

    }

    public function provideDataForEditSmsData()
    {
        $admin = $this->testAdmin;
        $baseData = array('login'=>'','target'=>'',
            'isExpectedForm'=>true,
            'smsData'=>array('phoneNumber'=>'0699999999','identifier'=>'IDSMS'),
            'isValidData'=>true,
            'isPhoneNumberEdit'=>true,
            'code'=>'1111',
            'isValidCode'=>true,
            'hasPreviousPhoneNumber'=>true,
            'isSmsEnabled'=>true,
            'expectedMessages'=>array('')
        );

        $validDataMsg = 'Un code vous a été envoyé';
        $validCodeMsg = 'enregistré';
        return array(
            'not referent'=>array_replace($baseData, array('login'=>$admin,'target'=>'vie_integrative', 'isValidData'=>false,
                                                                     'isPhoneNumberEdit'=>false
             )),

            'too many requests'=>array_replace($baseData, array('login'=>'crabe_arnold','target'=>'crabe_arnold',
                                                                    'isExpectedForm'=>false,
                                                                    'expectedMessages'=>'3 demandes de changement')),

           'current number'=>array_replace_recursive($baseData, array('login'=>'maltobar','target'=>'maltobar',
                                                              'isPhoneNumberEdit'=>false,
                                                              'smsData'=>array('phoneNumber'=>'0611223344'),'isValidData'=>true,
                                                              'expectedMessages'=>$validCodeMsg
                                                          )),

         'current number, disable sms'=>array_replace_recursive($baseData, array('login'=>'maltobar','target'=>'maltobar',
                                                            'isPhoneNumberEdit'=>false,'smsData'=>array('phoneNumber'=>'0611223344'),
                                                            'isValidData'=>true,'isSmsEnabled'=>false,
                                                            'expectedMessages'=>$validCodeMsg
                                                        )),

        'admin enables sms'=>array_replace($baseData, array('login'=>$admin,'target'=>'la_mandragore',
                                                                'isExpectedForm'=>true,'isPhoneNumberEdit'=>false,
                                                                'expectedMessages'=>$validCodeMsg)),

          'admin disables sms'=>array_replace($baseData, array('login'=>$admin,'target'=>'maltobar','isExpectedForm'=>true,
                                                                  'isPhoneNumberEdit'=>false,'isSmsEnabled'=>false,
                                                                  'expectedMessages'=>$validCodeMsg)),

         'new number, disable sms'=>array_replace_recursive($baseData, array('login'=>'maltobar','target'=>'maltobar',
                                                            'isPhoneNumberEdit'=>true,
                                                            'isValidData'=>true,'isSmsEnabled'=>false,
                                                            'expectedMessages'=>$validCodeMsg
                                                        )),

            'used by pro & person'=>array_replace_recursive($baseData, array('login'=>'maltobar','target'=>'maltobar',
                                            'smsData'=>array('phoneNumber'=>'0612345678'), 'isValidData'=>false,
                                            'expectedMessages'=>'déjà utilisé')),

            'pro request : used by pro'=>array_replace_recursive($baseData, array('login'=>'maltobar','target'=>'maltobar',
                                                        'isValidData'=>false,'smsData'=>array('phoneNumber'=>'0612345678'),
                                                        'expectedMessages'=>'déjà utilisé')),

            'person request : used by person'=>array_replace_recursive($baseData, array('login'=>'benoit_perso','target'=>'benoit_perso',
                                                        'isValidData'=>false,'smsData'=>array('phoneNumber'=>'0612345678'),
                                                        'expectedMessages'=>'déjà utilisé')),

            'pro request : used by person'=>array_replace_recursive($baseData,array('login'=>'maltobar','target'=>'maltobar',
                                            'smsData'=>array('phoneNumber'=>'0644332211'),
                                                                'expectedMessages'=>array($validDataMsg,$validCodeMsg)
                                                            )),

            'person request : used by pro'=>array_replace_recursive($baseData, array('login'=>'benoit_perso','target'=>'benoit_perso',
                                            'smsData'=>array('phoneNumber'=>'0611223344'),
                                                              'expectedMessages'=>array($validDataMsg,$validCodeMsg)
                                                            )),

        'last remaining try : wrong code'=>array_replace($baseData, array('login'=>'hirundo_archi','target'=>'hirundo_archi',
                                                                'isValidCode'=>false,'hasPreviousPhoneNumber'=>true, 'code'=>'2222',
                                                                'expectedMessages'=>'compte a été bloqué')),

            'last remaining try : valid code'=>array_replace($baseData, array('login'=>'hirundo_archi','target'=>'hirundo_archi',
                                                                              'hasPreviousPhoneNumber'=>false,
                                                                'expectedMessages'=>array($validDataMsg,$validCodeMsg)
                                                            )),

          'never had phone number : valid code'=>array_replace($baseData, array('login'=>'jardins_epices','target'=>'jardins_epices',
                                                                                'hasPreviousPhoneNumber'=>false,
                                                                                'expectedMessages'=>array($validDataMsg,$validCodeMsg)
                                                                            )),

            'no phone number : referent'=>array_replace($baseData, array('login'=>$admin,'target'=>'labonnepioche',
                                                            'isExpectedForm'=>false,'expectedMessages'=>'pas saisi ses coordonnées')),

            '2 accounts associated before: valid code'=>array_replace($baseData,array('login'=>'nico_faus_prod','target'=>'nico_faus_prod',
                                                        'expectedMessages'=>array($validDataMsg,'peut désormais réaliser')
                                                            )),
        );
    }

    public function changePassword($current, $new, $confirm)
    {
        $url = '/profile/change-password';
        $crawler = $this->client->request('GET',$url);

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('fos_user_change_password_form_save')->form();
        $form['fos_user_change_password_form[current_password]']->setValue($current);
        $form['fos_user_change_password_form[plainPassword][first]']->setValue($new);
        $form['fos_user_change_password_form[plainPassword][second]']->setValue($confirm);
        return $this->client->submit($form);

    }

    /**
     * Need to check that UserValidator is called + that user can login with new password later on
     *
     *@dataProvider providePasswordData
     */
    public function testChangePassword($login,$loginPwd,$currentPwd, $newPwd, $confirmPwd, $isValid, $expectedMessage)
    {
        $crawler = $this->login($login, $loginPwd);

        $currentUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));

        $crawler = $this->changePassword($currentPwd, $newPwd, $confirmPwd);

        if($isValid){
            $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$currentUser->getID()));
            $crawler = $this->client->followRedirect();
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());

            //then test login with new password
            $crawler = $this->login($login, $newPwd);
            $this->assertSame(1,$crawler->filter('html:contains("Espace Professionnel")')->count());

            //as password is not rolled-back on Cyclos side,we would have different passwords between our application BDD and
            //Cyclos BDD.
            //Workaround : call the password change again to have, in the end of the test, the same password than at the beginning
            $crawler = $this->changePassword($newPwd, $currentPwd, $currentPwd);
            $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$currentUser->getID()));
            $crawler = $this->client->followRedirect();

            //then test login with initial password
            $crawler = $this->login($login, $currentPwd);

        }else{
            $this->assertSame(1, $crawler->filter('input#fos_user_change_password_form_current_password')->count());    
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());

        }
    }

    public function providePasswordData()
    {
        //WARNING : put here the login of an user who will be used ONLY for this specific test and anywhere else
        //because the password is also changed on Cyclos-side. The password will be rolledback on Symfony-side but not on Cyclos-side
        //It will provok an exception "LOGIN" and no other test will work as we need user to login and expect password to be 
        //@@bbccdd// In order to be able to chain data provided, we must commit changes at the end of the test before the rollback 
        //is called
        $login = 'denis_ketels';

        $new = '@@bbccdd';

        //invalid data
        $baseData = array('login'=>$login,
            'loginpwd'=>'@@bbccdd',
            'current'=>'@@bbccdd',
            'new'=>$new,
            'confirm'=>$new,
            'expectValid'=>true,
            'expectedMessage'=>'modifié'
        );
        // all special characters`@!"#$%&'()*+,-./:;<=>?[\]^_{}~'
        // WARNING : bug reported in Cyclos 4.11 : character < throws ValidationException
        return array(
            'invalid current'             => array_replace($baseData, array('current'=>'@bbccdd','expectValid'=>false,
                                                                        'expectedMessage'=>'passe invalide')),          

            'new != confirm'              => array_replace($baseData, array('confirm'=>'@bcdefg','expectValid'=>false,
                                                                        'expectedMessage'=>'correspondent pas')),          

            'too short new password'      => array_replace($baseData, array('new'=>'@bcdefg','confirm'=>'@bcdefg','expectValid'=>false,
                                                                        'expectedMessage'=>'plus de 8 caractères')),          

            'pseudo included in password' => array_replace($baseData, array('new'=>'@'.$login.'@','confirm'=>'@'.$login.'@',
                                                                  'expectValid'=>false,'expectedMessage'=>'contenu dans le mot de passe')),

            'no special character'        => array_replace($baseData, array('new'=>'1testPwd2' ,'confirm'=>'1testPwd2',
                                                                  'expectValid'=>false, 'expectedMessage'=>'caractère spécial')),

            'new = current'               => array_replace($baseData, array('expectValid'=>false, 'expectedMessage'=>'déjà utilisé')),          
            //we make it invalid because of cyclos bug
            'invalid <'                  => array_replace($baseData, array('new'=>'i<3cairn','confirm'=>'i<3cairn',
                                                                  'expectValid'=>false,'expectedMessage'=>'pas autorisés')),          

            'valid \\ at begin'             => array_replace($baseData, array('new'=>'\bcdefgh','confirm'=>'\bcdefgh', 
                                                                  'expectValid'=>false,'expectedMessage'=>'pas autorisés')),          

            'valid \\ at end '              => array_replace($baseData, array('new'=>'bcdefgh\\','confirm'=>'bcdefgh\\', 
                                                                  'expectValid'=>false,'expectedMessage'=>'pas autorisés')),          

            'invalid >'                  => array_replace($baseData, array('new'=>'i>3cairn','confirm'=>'i>3cairn',
                                                                  'expectValid'=>false,'expectedMessage'=>'pas autorisés')),          

            'invalid §'                  => array_replace($baseData, array('new'=>'§bcdefgh','confirm'=>'§bcdefgh',
                                                                   'expectValid'=>false,'expectedMessage'=>'pas autorisés')),          

            'invalid ù'                  => array_replace($baseData, array('new'=>'ùbcdefgh','confirm'=>'ùbcdefgh',
                                                                   'expectValid'=>false,'expectedMessage'=>'pas autorisés')),          

            'invalid ä'                  => array_replace($baseData, array('new'=>'äbcdefgh','confirm'=>'äbcdefgh',
                                                                   'expectValid'=>false,'expectedMessage'=>'pas autorisés')),          

            'valid `'                       => array_replace($baseData, array('new'=>"`bcdefgh",'confirm'=>"`bcdefgh")),          
            'valid @'                       => array_replace($baseData, array('new'=>'@bcdefgh','confirm'=>'@bcdefgh')),          
            'valid !'                       => array_replace($baseData, array('new'=>'!bcdefgh','confirm'=>'!bcdefgh')),          
            'valid "'                       => array_replace($baseData, array('new'=>'"bcdefgh','confirm'=>'"bcdefgh')),          
            'valid #'                       => array_replace($baseData, array('new'=>'#bcdefgh','confirm'=>'#bcdefgh')),          
            'valid $'                       => array_replace($baseData, array('new'=>'$bcdefgh','confirm'=>'$bcdefgh')),          
            'valid %'                       => array_replace($baseData, array('new'=>'%bcdefgh','confirm'=>'%bcdefgh')),          
            'valid &'                       => array_replace($baseData, array('new'=>'&bcdefgh','confirm'=>'&bcdefgh')),          
            'valid \''                       => array_replace($baseData, array('new'=>'\'bcdefgh','confirm'=>'\'bcdefgh')),          
            'valid ()'                       => array_replace($baseData, array('new'=>'(bcdefgh)','confirm'=>'(bcdefgh)')),          
            'valid {}'                       => array_replace($baseData, array('new'=>'{bcdefgh}','confirm'=>'{bcdefgh}')),          
            'valid []'                       => array_replace($baseData, array('new'=>'[bcdefgh]','confirm'=>'[bcdefgh]')),          
            'valid *'                       => array_replace($baseData, array('new'=>'*bcdefgh','confirm'=>'*bcdefgh')),          
            'valid +'                       => array_replace($baseData, array('new'=>'+bcdefgh','confirm'=>'+bcdefgh')),          
            'valid ,'                       => array_replace($baseData, array('new'=>',bcdefgh','confirm'=>',bcdefgh')),          
            'valid -'                       => array_replace($baseData, array('new'=>'-bcdefgh','confirm'=>'-bcdefgh')),          
            'valid .'                       => array_replace($baseData, array('new'=>'.bcdefgh','confirm'=>'.bcdefgh')),          
            'valid /'                       => array_replace($baseData, array('new'=>'/bcdefgh','confirm'=>'/bcdefgh')),          
            'valid :'                       => array_replace($baseData, array('new'=>':bcdefgh','confirm'=>':bcdefgh')),          
            'valid ;'                       => array_replace($baseData, array('new'=>';bcdefgh','confirm'=>';bcdefgh')),          
            'valid ='                       => array_replace($baseData, array('new'=>'=bcdefgh','confirm'=>'=bcdefgh')),          
            'valid ?'                       => array_replace($baseData, array('new'=>'?bcdefgh','confirm'=>'?bcdefgh')),          
            'valid ^'                       => array_replace($baseData, array('new'=>'^bcdefgh','confirm'=>'^bcdefgh')),          
            'valid _'                       => array_replace($baseData, array('new'=>'_bcdefgh','confirm'=>'_bcdefgh')),          
            'valid ~'                       => array_replace($baseData, array('new'=>'~bcdefgh','confirm'=>'~bcdefgh')),          
        );
    }

    /**
     *
     *@dataProvider provideDataForResetPassword 
     */
    public function testResetPassword($identifier,$isCorrectUsername, $isLegit, $isEmailSent, $expectedMessage)
    {
        $crawler = $this->client->request('GET','resetting/request');

        $this->client->enableProfiler();

        $form = $crawler->selectButton('Réinitialiser le mot de passe')->form();
        $form['username']->setValue($identifier); //username also means email here
        $crawler = $this->client->submit($form);

        if($isCorrectUsername){
            $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$identifier));
            $this->em->refresh($currentUser);

            // If legit, we just assert content of the email, because this controller action if used from FOS
            // therefore, it does not need to be tested
            if($isLegit){
                ;
            }else{
                if($isEmailSent){
                    $this->assertUserIsDisabled($currentUser, true);

                    $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
                    $this->assertTrue($mailCollector->getMessageCount() == 1);
                }else{
                    $this->assertUserIsDisabled($currentUser, false);
                }
            }
        }else{
            $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
            $this->assertTrue($mailCollector->getMessageCount() == 0);
        }

    }

    public function provideDataForResetPassword()
    {
        return array(
            'user is disabled'=>array('tout_1_fromage',true,false,false,'est bloqué'),
            'user has never log in'=>array('NaturaVie',true,false,true,'Vous ne pouvez pas changer'),
        );
    }

    /**
     *
     *@dataProvider provideReferentsAndTargets
     */
    public function testViewProfile($referent,$target,$isLegit)
    {
        $crawler = $this->login($referent, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','user/profile/view/'.$targetUser->getID());


        if(! $isLegit){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            return;
        }

        $this->assertContains(htmlspecialchars($targetUser->getName(),ENT_QUOTES),$this->client->getResponse()->getContent());
        $this->assertContains($targetUser->getUsername(),$this->client->getResponse()->getContent());
        $this->assertContains($targetUser->getEmail(),$this->client->getResponse()->getContent());
        $this->assertContains(htmlspecialchars($targetUser->getDescription(),ENT_QUOTES),$this->client->getResponse()->getContent());
        $this->assertContains(htmlspecialchars($targetUser->getCity()),$this->client->getResponse()->getContent());
        $this->assertContains(htmlspecialchars($targetUser->getAddress()->getStreet1(),ENT_QUOTES),$this->client->getResponse()->getContent());
        $this->assertContains($targetUser->getAddress()->getZipCity()->getZipCode(),$this->client->getResponse()->getContent());

        $hasCard = $targetUser->getCard();

        if($targetUser->isAdherent()){

            $this->assertSame(1,$crawler->filter('a[href*="user/remove/'.$targetUser->getID().'"]')->count());
            $this->assertSame(1,$crawler->filter('a[href*="user/sms-data/edit/'.$targetUser->getID().'"]')->count());
            $this->assertSame(1,$crawler->filter('a[href*="user/id-document/download/'.$targetUser->getID().'"]')->count());

            if($currentUser === $targetUser){//adherent watching his own profile --> is enabled if so
                $this->assertSame(1,$crawler->filter('a[href*="user/block/'.$targetUser->getID().'"]')->count());
                $this->assertSame(1,$crawler->filter('a[href*="profile/change-password"]')->count());
                $this->assertSame(1,$crawler->filter('a[href*="profile/edit"]')->count());

                if($targetUser->hasRole('ROLE_PRO')){
                    $this->assertSame(1,$crawler->filter('html:contains("groupe local référent")')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="user/referents/assign"]')->count());
                }else{//ROLE_PERSON
                    $this->assertSame(0,$crawler->filter('html:contains("groupe local référent")')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="user/referents/assign"]')->count());
                }

                $this->assertsame(0,$crawler->filter('a[href*="card/download"]')->count());

                if($hasCard){
                    $this->assertSame(1,$crawler->filter('a[href*="card/revoke/'.$targetUser->getID().'"]')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="card/associate"]')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="card/order"]')->count());
                }else{
                    $this->assertSame(0,$crawler->filter('a[href*="card/revoke"]')->count());
                    $this->assertSame(1,$crawler->filter('a[href*="card/associate/'.$targetUser->getID().'"]')->count());
                    $this->assertSame(1,$crawler->filter('a[href*="card/order"]')->count());
                }

            }else{//admin, as referent, watching adherent's profile

                if($targetUser->isEnabled()){
                    $this->assertSame(1,$crawler->filter('a[href*="user/block/'.$targetUser->getID().'"]')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="admin/users/activate"]')->count());
                }else{
                    $this->assertSame(0,$crawler->filter('a[href*="user/block"]')->count());
                    $this->assertSame(1,$crawler->filter('a[href*="admin/users/activate/'.$targetUser->getID().'"]')->count());
                }

                $this->assertSame(0,$crawler->filter('a[href*="profile/change-password"]')->count());
                $this->assertSame(0,$crawler->filter('a[href*="profile/edit"]')->count());

                if($targetUser->hasRole('ROLE_PRO')){
                    if($currentUser->hasRole('ROLE_SUPER_ADMIN')){
                        $this->assertSame(1,$crawler->filter('html:contains("groupe local référent")')->count());
                        $this->assertSame(1,$crawler->filter('a[href*="user/referents/assign/'.$targetUser->getID().'"]')->count());
                    }else{//is GL --> cannot assign referent
                        $this->assertSame(0,$crawler->filter('html:contains("groupe local référent")')->count());
                        $this->assertSame(0,$crawler->filter('a[href*="user/referents/assign"]')->count());
                    }
                }else{//person's profile : no referent data
                    $this->assertSame(0,$crawler->filter('html:contains("groupe local référent")')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="user/referents/assign"]')->count());
                }

                if($hasCard){
                    $this->assertSame(0,$crawler->filter('a[href*="card/download"]')->count());
                    $this->assertSame(1,$crawler->filter('a[href*="card/revoke/'.$targetUser->getID().'"]')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="card/associate"]')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="card/order"]')->count());
                }else{
                    $this->assertSame(1,$crawler->filter('a[href*="card/download/'.$targetUser->getID().'"]')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="card/revoke"]')->count());
                    $this->assertSame(1,$crawler->filter('a[href*="card/associate/'.$targetUser->getID().'"]')->count());
                    $this->assertSame(0,$crawler->filter('a[href*="card/order"]')->count());
                }

            }
        }

    }

    /**
     *
     *@dataProvider provideReferentsAndTargets
     */
    public function testDownloadIdDocument($referent,$target,$isLegit)
    {
        $crawler = $this->login($referent, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        $crawler = $this->client->request('GET','user/id-document/download/'.$targetUser->getID());

        if(! $isLegit){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type','image/png'));
        }
    }

    public function provideReferentsAndTargets()
    {

        $adminUsername = $this->testAdmin;
        return array(
            'superadmin for enabled pro'=>array('referent'=>$adminUsername,'target'=>'DrDBrew','isLegit'=>true),
            'admin for enabled pro'=>array('referent'=>'gl_grenoble','target'=>'episol','isLegit'=>true),
          'admin not referent'=>array('referent'=>$adminUsername,'target'=>'vie_integrative','isLegit'=>false),
          'superadmin for enabled person'=>array('referent'=>$adminUsername,'target'=>'crabe_arnold','isLegit'=>true),
            'enabled pro for himself'=>array('referent'=>'DrDBrew','target'=>'DrDBrew','isLegit'=>true),
            'superadmin for disabled pro'=>array('referent'=>$adminUsername,'target'=>'la_mandragore','isLegit'=>true),
            'superadmin for pro without card'=>array('referent'=>$adminUsername,'target'=>'episol','isLegit'=>true),
            'pro without card for himself'=>array('referent'=>'episol','target'=>'episol','isLegit'=>true),
            'person for himself'=>array('referent'=>'benoit_perso','target'=>'benoit_perso','isLegit'=>true),
            'pro for other pro'=>array('referent'=>'episol','target'=>'DrDBrew','isLegit'=>false),
            'person for other person'=>array('referent'=>'crabe_arnold','target'=>'nico_faus_perso','isLegit'=>false),
            'pro for person'=>array('referent'=>'maltobar','target'=>'benoit_perso','isLegit'=>false),
            'person for pro'=>array('referent'=>'benoit_perso','target'=>'maltobar','isLegit'=>false),
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
                    $this->assertContains('supprimé avec succès', $message->getBody());
                    $this->assertContains($currentUser->getName(), $message->getBody());

                    $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                    $this->assertSame($targetUser->getEmail(), key($message->getTo()));

                    $this->assertTrue($this->client->getResponse()->isRedirect());
                    $crawler = $this->client->followRedirect();

                    $operationRepo = $this->em->getRepository('CairnUserBundle:Operation');

                    $operations = $operationRepo->findBy(array('creditorName'=>$saveName));
                    $this->assertTrue( count($operations) != 0);
                    foreach($operations as $operation){
                        $this->assertEquals($operation->getCreditor(),NULL);
                    }

                    $operations = $operationRepo->findBy(array('debitorName'=>$saveName));
                    $this->assertTrue( count($operations) != 0);
                    foreach($operations as $operation){
                        $this->assertEquals($operation->getDebitor(),NULL);
                    }

                    $this->em->refresh($targetUser);

                    $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

                    $this->assertSame(1,$crawler->filter('html:contains("Espace membre supprimé")')->count());

                }else{
                    $this->assertTrue($this->client->getResponse()->isRedirect('/logout'));
                    $crawler = $this->client->followRedirect();

                    $this->em->refresh($targetUser);

                    $this->assertNotEquals($targetUser,NULL);
                    $this->assertEquals($targetUser->getRemovalRequest(),true);
                    $this->assertUserIsDisabled($targetUser, true);
                }
            }       
        }
    }

    /**
     *@TODO : add user removing himself who is under admin's responsiblity (and not admin..)
     * only pros from Grenoble have non null accounts (see script to generate users and initial payments : init_test_data.py)
     *
     *@WARNING: Avoid to remove users with access clients on Cyclos-side. These would not be rolled back and would damage stable Cyclos
     * database state
     */
    public function provideUsersToRemove()
    {

        $adminUsername = $this->testAdmin;

        return array(
            'non null account' => array($adminUsername,'atelier_eltilo',true,false,false),
            'valid admin removal, user involved in operations' => array($adminUsername,'trankilou',true,true,false),
            'not referent' => array($adminUsername,'NaturaVie',false,true,false),
            'user auto-removal' => array('lib_colibri','lib_colibri',true,true,true),
        );

    }

    /**
     *
     *@dataProvider provideDataForRemovePendingUsers
     */
    public function testRemovePendingUsers($login, $removedUsers, $notRemovedUsers )
    {
        $crawler = $this->login($login,'@@bbccdd');

        $this->client->enableProfiler();

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));

        //sensible operation
        $url = '/user/remove-pending';
        $crawler = $this->client->request('GET',$url);
        $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('confirmation_save')->form();
        $crawler =  $this->client->submit($form);

        foreach($removedUsers as $username){
            $user = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$username));
            $this->assertTrue($user == NULL);
        }
        foreach($notRemovedUsers as $username){
            $user = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$username));
            $this->assertTrue($user != NULL);
        }

//        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
//        $this->assertTrue($mailCollector->getMessageCount() >= 1);
//        $message = $mailCollector->getMessages()[0];
//        $this->assertInstanceOf('Swift_Message', $message);
//        //                    $this->assertContains('Nouvelle carte', $message->getSubject());
//        $this->assertContains('supprimé avec succès', $message->getBody());
//        $this->assertContains($currentUser->getName(), $message->getBody());
//
//        $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
//        $this->assertSame($targetUser->getEmail(), key($message->getTo()));

    }

    public function provideDataForRemovePendingUsers()
    {
        return array(
            'result'=> array($this->testAdmin, array('Biocoop'), array('Alpes_EcoTour') )
        );
    }
}
