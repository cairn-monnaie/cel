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
     * Need to check that UserValidator is called + that user can login with new password later on
     *
     *@dataProvider providePasswordData
     */
    public function testChangePassword($login,$current, $new, $confirm, $isValid, $expectedMessage)
    {
        $crawler = $this->login($login, '@@bbccdd');

        $currentUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));


        $url = '/profile/change-password';
        $crawler = $this->client->request('GET',$url);

        $card = $currentUser->getCard();
        if(!$card->isEnabled()){
            $crawler = $this->client->request('GET', '/card/validate');
            $crawler = $this->inputCardKey($crawler,'1111');
            $crawler = $this->client->request('GET',$url);
        }

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
            $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            $crawler = $this->login($login, $new);
            $this->assertSame(1,$crawler->filter('html:contains("Espace Professionnel")')->count());
            $this->assertSame(1, $crawler->filter('li#id_welcome')->count());    
        }else{
            $this->assertSame(1, $crawler->filter('input#fos_user_change_password_form_current_password')->count());    
            $this->assertSame(0, $crawler->filter('div.alert-success')->count());    
        }
    }

    public function providePasswordData()
    {
        $login = 'vie_integrative';
        $new = '@bcdefgh';
        //valid data
        $baseData = array('login'=>$login,
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
            'valid'                       => $baseData
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
            $this->assertSame(1,$crawler->filter('a[href*="card/home"]')->count());
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


    //    public function testListBeneficiaries()
    //    {
    //        ;
    //    }

    /**
     *@dataProvider provideBeneficiariesToAdd
     */
    public function testAddBeneficiary($current,$name,$email,$changeICC,$isValid,$expectKey)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));
        $creditorUser  = $this->em->getRepository('CairnUserBundle:User')
            ->createQueryBuilder('u')
            ->where('u.name=:name')->orWhere('u.email=:email')
            ->setParameter('name',$name)
            ->setParameter('email',$email)
            ->getQuery()->getOneOrNullResult();

        if($creditorUser){
            if($creditorUser === $debitorUser){
                $account = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($creditorUser->getCyclosID())[0];
                $ICC = $account->number;
            }else{
                $test = $this->container->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($creditorUser->getUsername());
                $ICC = $test->accountNumber;
            }
            $ICC = ($changeICC) ? $ICC + 1 : $ICC;
        }else{
            $ICC = 123456789;
        }

        $crawler = $this->client->request('GET','/user/beneficiaries/add');

        $card = $debitorUser->getCard();
        if(!$card->isEnabled()){
            $crawler = $this->client->request('GET', '/card/validate');
            $crawler = $this->inputCardKey($crawler,'1111');
            $crawler = $this->client->request('GET','/user/beneficiaries/add');
        }

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('form_add')->form();
        $form['form[name]']->setValue($name);
        $form['form[email]']->setValue($email);
        $form['form[ICC]']->setValue($ICC);
        $crawler = $this->client->submit($form);

        if($isValid){
            $beneficiary = $this->em->getRepository('CairnUserBundle:Beneficiary')->findOneBy(array('ICC'=>$ICC));
            $this->assertTrue($this->client->getResponse()->isRedirect('/user/beneficiaries/list'));
            $crawler = $this->client->followRedirect();

            $this->em->refresh($debitorUser);
            $this->assertTrue($debitorUser->hasBeneficiary($beneficiary));
            $this->assertSame(1, $crawler->filter('div.alert-'.$expectKey)->count());    
        }else{
            $this->assertTrue($this->client->getResponse()->isRedirect());
            $crawler = $this->client->followRedirect();

            $this->assertSame(1, $crawler->filter('div.alert-'.$expectKey)->count());    
            $this->assertSame(0, $crawler->filter('div.alert-success')->count());    
        }
    }

    public function provideBeneficiariesToAdd()
    {
        return array(
            'self beneficiary'=> array('current'=>'vie_integrative','name'=>'vie','email'=>'vie_integrative@test.com','changeICC'=>false,'isValid'=>false,'expectKey'=>'error'), 
            'user not found'=> array('current'=>'vie_integrative','name'=>'Malt','email'=>'malt@cairn-monnaie.com','changeICC'=>false,'isValid'=>false,'expectMessage'=>'error'),              
            'ICC not found'=>array('current'=>'vie_integrative', 'name'=>'Alter Mag','email'=>'alter_mag@test.com','changeICC'=>true,'isValid'=>false,'expectMessage'=>'error'),              
            'valid benef'=>array('current'=>'vie_integrative', 'name'=>'Alter Mag','email'=>'alter_mag@test.com','changeICC'=>false,'isValid'=>true,'expectMessage'=>'success'),              
            'already benef'=>array('current'=>'nico_faus_prod','name'=>'La Bonne Pioche','email'=>'labonneioche@test.com','changeICC'=>false,'isValid'=>false,'expectMessage'=>'info'),              

        );
    }

    //    /**
    //     *depends testAddBeneficiary
    //     */
    //    public function testEditBeneficiary()
    //    {
    //        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
    //
    //        $crawler = $this->login($debitor, '@@bbccdd');
    //
    //        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$debitor));
    //        $creditorUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$creditor));
    //
    //        ;
    //    }

    public function removeBeneficiaryAction($current,$beneficiary,$isValid)
    {
        $userRepo = $this->em->getRepository('CairnUserBundle:User');
        $crawler = $this->login($current, '@@bbccdd');

        $debitorUser = $userRepo->findOneBy(array('username'=>$current));
        $creditorUser = $userRepo->findOneBy(array('username'=>$beneficiary));

        $beneficiary = $this->em->getRepository('CairnUserBundle:Beneficiary')->findOneBy(array('user'=>$creditorUser));

        $crawler = $this->client->request('GET','/user/beneficiaries/remove/'.$beneficiary->getID());

        if($isValid){
            $form = $crawler->selectButton('confirmation_save')->form();
            $crawler =  $this->client->submit($form);
        }

        return $crawler;
    }

    /**
     * Here we test the removeBeneficiary controller action with, as testing data, a beneficiary with two sources. This way, we test that
     * after the first removal, the entity is still there, then is removed once the second removal from the second source is done. If a 
     * beneficiary has no source, it must be removed from the database
     */
    public function testRemoveBeneficiary()
    {
        $userRepo = $this->em->getRepository('CairnUserBundle:User');
        $benefRepo = $this->em->getRepository('CairnUserBundle:Beneficiary');
        // ---------- first valid removal from a source ------------
        $crawler = $this->removeBeneficiaryAction('nico_faus_prod','labonnepioche',true);

        $creditorUser = $userRepo->findOneBy(array('username'=>'labonnepioche'));
        $beneficiary = $benefRepo->findOneBy(array('user'=>$creditorUser));

        //check that beneficiary entity still exists but is not in list of $debitor
        $this->assertNotEquals($beneficiary,NULL);
        $this->assertEquals(count($beneficiary->getSources()),1);

        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

        // ---------- second valid removal from a source ------------
        $crawler = $this->removeBeneficiaryAction('le_marque_page','labonnepioche',true);

        $creditorUser = $userRepo->findOneBy(array('username'=>'labonnepioche'));
        $beneficiary = $benefRepo->findOneBy(array('user'=>$creditorUser));

        //check that beneficiary entity has been removed because its number of sources is 0
        $this->assertEquals($beneficiary,NULL);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

        //test invalid removal : beneficiary exists but current user is not a source
        $crawler = $this->removeBeneficiaryAction('nico_faus_prod','ferme_bressot',false);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert-error')->count());    
        $this->assertSame(0, $crawler->filter('div.alert-success')->count());    

    }


    /**
     *@todo : try to remove a ROLE_ADMIN
     *@todo :check that all beneficiaries with user $target have been removed
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
                $this->assertSame(1,$crawler->filter('div.alert-error')->count());    
            }else{
                $this->client->enableProfiler();

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

                    $this->em->refresh($targetUser);

                    $this->assertEquals($targetUser,NULL);
                    $this->assertSame(1,$crawler->filter('html:contains("supprimé avec succès")')->count());
                    $this->assertSame(1,$crawler->filter('div.alert-success')->count());    
                }else{
                    $this->assertTrue($this->client->getResponse()->isRedirect('/logout'));
                    $crawler = $this->client->followRedirect();

                    $this->em->refresh($targetUser);

                    $this->assertNotEquals($targetUser,NULL);
                    $this->assertEquals($targetUser->getRemovalRequest(),true);
                    $this->assertEquals($targetUser->isEnabled(),false);
                    $this->assertSame(1,$crawler->filter('div.alert-success')->count());    
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
//            'non null account' => array($adminUsername,'atelier_eltilo',true,false,false),
//            'valid admin removal' => array($adminUsername,'la_belle_verte',true,true,false),
//            'not legit' => array($adminUsername,'NaturaVie',false,true,false),
            'user auto-removal' => array('lib_colibri','lib_colibri',true,true,true),
        );

    }

}
