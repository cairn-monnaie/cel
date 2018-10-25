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

    function __construct($name = NULL, array $data = array(), $dataName = ''){
        parent::__construct($name, $data, $dataName);
    }


    /**
     * Need to check that UserValidator is called + that user can login with new password later on
     *
     *@dataProvider providePasswordData
     */
    public function testChangePassword($login,$current, $new, $confirm, $isValid, $expectedMessage)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $currentUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$login));

        $crawler = $this->login($login, '@@bbccdd');

        $crawler = $this->client->request('GET','/password/new/');

        $card = $currentUser->getCard();
        if(!$card->isEnabled()){
            $crawler = $this->client->request('GET', '/card/validate');
            $crawler = $this->inputCardKey($crawler,'1111');
            $crawler = $this->client->request('GET','/password/new/');
        }

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('change_password_save')->form();
        $form['change_password[current_password]']->setValue($current);
        $form['change_password[plainPassword][first]']->setValue($new);
        $form['change_password[plainPassword][second]']->setValue($confirm);
        $crawler = $this->client->submit($form);

        if($isValid){
            $this->assertTrue($this->client->getResponse()->isRedirect('user/profile/view/'.$currentUser->getID()));
            $crawler = $this->client->followRedirect();
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            $crawler = $this->login($login, $new);
            $this->assertSame(1,$crawler->filter('html:contains("Espace Professionnel")')->count());
        }else{
            ;
//            $crawler = $this->client->followRedirect();
//            $this->assertTrue($this->client->getRequest()->isMethod('GET'));
//            $this->assertSame(1,$crawler->filter('html:contains("Mot de passe actuel")')->count());
        }
    }

    public function providePasswordData()
    {
        $login = 'locavore';

        //valid data
        $baseData = array('login'=>$login,
            'current'=>'@@bbccdd',
            'new'=>'@bcdefgh',
            'confirm'=>'@bcdefgh',
            'expectValid'=>true,
            'expectedMessage'=>'succès'
        );

        return array(
            'invalid current'             => array_replace($baseData, array('current'=>'@bbccdd','expectValid'=>false)),          
            'current = new'               => array_replace($baseData, array('new'=>'@@bbccdd','confirm'=>'@@bbccdd','expectValid'=>false,
            'expectedMessage'=>'identiques')),          
            'new != confirm'              => array_replace($baseData, array('confirm'=>'@bcdefg','expectValid'=>false,
            'expectedMessage'=>'correspondent pas')),          
            'too short new password'      => array_replace($baseData, array('new'=>'@bcdefg','confirm'=>'@bcdefg','expectValid'=>false,
            'expectedMessage'=>'plus de 8 caractères')),          
            'pseudo included in password' => array_replace($baseData, array('new'=>'@'.$login.'@','confirm'=>'@'.$login.'@','expectValid'=>false,
            'expectedMessage'=>'contenu dans le mot de passe')),
            'no special character'        => array_replace($baseData, array('new'=>'1testPwd2' ,'confirm'=>'1testPwd2','expectValid'=>false,
            'expectedMessage'=>'caractère spécial')),
//            'valid'                       => $baseData
        );
    }

    /**
     *
     *@dataProvider provideReferentsAndTargets
     */
    public function testViewProfile($referent,$target)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($referent[0], $referent[1]);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
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
        if( ($targetUser->hasReferent($currentUser) || $targetUser === $currentUser)){
            $this->assertSame(1,$crawler->filter('a[href*="card/home"]')->count());
            $this->assertSame(1,$crawler->filter('a[href*="user/remove"]')->count());

            if($targetUser->hasReferent($currentUser)){
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
//
//    /**
//     *@depends testValidateCard
//     *@dataProvider provideBeneficiariesToAdd
//     */
//    public function testAddBeneficiary($current,$name,$email,$changeICC,$isValid,$expectMessage)
//    {
//        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
//
//        $crawler = $this->login($current, '@@bbccdd');
//
//        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));
//        $creditorUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('email'=>$email));
//
//        $ICC = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($creditorUser->getCyclosID())[0];
//        $ICC = ($changeICC) ? $ICC + 1 : $ICC;
//        $crawler = $this->client->request('GET','user/beneficiaries/add');
//
//        $card = $debitorUser->getCard();
//        if(!$card->isEnabled()){
//            $crawler = $this->client->request('GET', '/card/validate');
//            $crawler = $this->inputCardKey($crawler,'1111');
//            $crawler = $this->client->request('GET','user/beneficiaries/add');
//        }
//
//        $crawler = $this->inputCardKey($crawler,'1111');
//        $crawler = $this->client->followRedirect();
//
//        $form = $crawler->selectButton('form_add')->form();
//        $form['form[name]']->setValue($name);
//        $form['form[email]']->setValue($email);
//        $form['form[ICC]']->setValue($ICC);
//        $crawler = $this->client->submit($form);
//
//        if($isValid){
//            $beneficiary = $this->em->getRepository('CairnUserBundle:Beneficiary')->findOneBy(array('ICC'=>$ICC));
//            $this->assertTrue($this->client->getResponse()->isRedirect('user/beneficiaries/list'));
//            $crawler = $this->client->followRedirect();
//
//            $this->em->refresh($debitorUser);
//            $this->assertTrue($debitorUser->hasBeneficiary($beneficiary));
//        }else{
//            $crawler = $this->client->followRedirect();
//            $this->assertTrue($this->client->getResponse()->isRedirect());
//            $crawler = $this->client->followRedirect();
//
//            $this->assertContains($expectMessage,$this->client->getResponse()->getContent());
//        }
//    }
//
//    public function provideBeneficiariesToAdd()
//    {
//        return array(
//             'self beneficiary'=> array('current'=>'DrDBrew','name'=>'Le DocteurD BrewPub','email'=>'docteurd@cairn-monnaie.com','changeICC'=>false,'isValid'=>false,'expectMessage'=>'ajouter vous-même'), 
//             'user not found'=> array('current'=>'DrDBrew','name'=>'Malt','email'=>'malt@cairn-monnaie.com','changeICC'=>false,'isValid'=>false,'expectMessage'=>'aucun membre'),              
//              'ICC not found'=>array('current'=>'DrDBrew', 'name'=>'Malt’O’Bar','email'=>'maltobar@cairn-monnaie.com','changeICC'=>true,'isValid'=>false,'expectMessage'=>'ne correspond à aucun compte'),              
//              'valid benef'=>array('current'=>'DrDBrew','name'=>'Malt’O’Bar','email'=>'maltobar@cairn-monnaie.com','changeICC'=>false,'isValid'=>true,'expectMessage'=>''),              
//              'valid benef'=>array('current'=>'LaBonnePioche','name'=>'La Dourb','email'=>'dourbie@cairn-monnaie.com','changeICC'=>false,'isValid'=>true,'expectMessage'=>''),              
//              'already benef'=>array('current'=>'DrDBrew','name'=>'Malt’O’Bar','email'=>'maltobar@cairn-monnaie.com','changeICC'=>false,'isValid'=>false,'expectMessage'=>'déjà partie de vos bénéficiaires'),              
//
//        );
//    }
//    /**
//     *@depends testAddBeneficiary
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

//    /**
//     *@dataProvider provideBeneficiariesToRemove
//     *@depends testAddBeneficiary
//     */
//    public function testRemoveBeneficiary($beneficiary, $isValid, $expectMessage)
//    {
//        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
//
//        $crawler = $this->login('DrDBrew', '@@bbccdd');
//
//        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
//        $creditorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$beneficiary));
//
//        $beneficiary = $this->em->getRepository('CairnUserBundle:Beneficiary')->findOneBy(array('user'=>$creditorUser));
//
//        if($isValid){
//            $form = $crawler->selectButton('confirmation_save')->form();
//            $crawler =  $this->client->submit($form);
//
//        }else{
//
//        }
//    }

    public function provideBeneficiariesToRemove()
    {
        array(
            array('current'=>'LaBonnePioche','beneficiary'=>'MaltOBar','isValid'=>false,'expectMessage'=>'pas partie'),
            array('current'=>'LaBonnePioche','beneficiary'=>'LaDourbie','isValid'=>true,'expectMessage'=>''),

        );
    }

    /**
     *@todo : try to remove a ROLE_ADMIN
     *@todo :check that all beneficiaries with user $target have been removed
     *@dataProvider provideUsersToRemove
     */
    public function testRemoveUser($referent,$target,$nullAccount)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($referent[0], $referent[1]);

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$referent[0]));
        $targetUser  = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$target));

        //sensible operation
        $url = '/user/remove/'.$targetUser->getID();
        $crawler = $this->client->request('GET',$url);
        $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));
        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler, '1111');
        $crawler = $this->client->followRedirect();

        if($targetUser->getUsername() == $this->container->getParameter('cyclos_global_admin_username')){
            $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
            $crawler = $this->client->followRedirect();
            $this->assertSame(1,$crawler->filter('html:contains("ne peut être supprimé")')->count());
        }else{
            if(! ($targetUser->hasReferent($currentUser) || $targetUser === $currentUser)){
                //access denied exception
                $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            }else{
                if(!$nullAccount){
                   $this->assertTrue($this->client->getResponse()->isRedirect('/user/profile/view/'.$targetUser->getID()));
                   $crawler = $this->client->followRedirect();
                   $this->assertSame(1,$crawler->filter('html:contains("solde non nul")')->count());
                }else{
                    $this->client->enableProfiler();

                    $form = $crawler->selectButton('confirmation_save')->form();
                    $form['confirmation[password]']->setValue('@@bbccdd');
                    $crawler =  $this->client->submit($form);

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

                }       
            }
        }
    }

    public function provideUsersToRemove()
    {
        return array(
            array(array('mazouthm','@@bbccdd'),'mazouthm',false),
//            array(array('glGrenoble','@@bbccdd'),'DrDBrew',false),
//            array(array('glGrenoble','@@bbccdd'),'cafeEurope',true),
//            array(array('glVoiron','@@bbccdd'),'cafeEurope',true),
        );

    }
}
