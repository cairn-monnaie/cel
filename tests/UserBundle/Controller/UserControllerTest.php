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
    function __construct(){
        parent::__construct();
    }

    public function testIndex()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET','/home');

        $this->assertSame(1,$crawler->filter('html:contains("security.login.username")')->count());
    }


    public function testChangePassword()
    {
        $this->client->followRedirects();   
        $crawler = $this->login('MaltOBar','@@bbccdd');

        $link = $crawler->filter('a:contains("Profil")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("mot de passe")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $this->assertSame(1,$crawler->filter('html:contains("Mot de passe actuel")')->count());

        //success
        $form = $crawler->selectButton('change_password_save')->form();
        $form['change_password[current_password]']->setValue('@@bbccdd');
        $form['change_password[plainPassword][first]']->setValue('@bcdefgh');
        $form['change_password[plainPassword][second]']->setValue('@bcdefgh');
        $crawler = $this->client->submit($form);

        $this->assertSame(1,$crawler->filter('html:contains("modifié avec succès")')->count());

        //failure
        $crawler = $this->client->request('GET', '/password/new/');
        $form = $crawler->selectButton('change_password_save')->form();
        $form['change_password[current_password]']->setValue('@bcdefgh');
        $form['change_password[plainPassword][first]']->setValue('@@bbccdd');
        $form['change_password[plainPassword][second]']->setValue('@bbccdd');
        $crawler = $this->client->submit($form);

        $this->assertSame(0,$crawler->filter('html:contains("modifié avec succès")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("champs ne correspondent pas")')->count());

        //3 wrong current_password in a row, user is disabled
        for($index =0; $index <= 2 ; $index++){
            $form = $crawler->selectButton('change_password_save')->form();
            $form['change_password[current_password]']->setValue('@cdefgh');
            $form['change_password[plainPassword][first]']->setValue('@@bbccdd');
            $form['change_password[plainPassword][second]']->setValue('@bbccdd');
            $crawler = $this->client->submit($form);
        }

        $this->assertSame(1,$crawler->filter('html:contains("security.login.username")')->count());

    }

    /**
     *@todo Make a test once the view are done
     *View own profile as a pro
     *View other pro's profile as a pro
     *View pro's profile as a referent
     *View pro's profile as non referent
     *
     */
    public function testViewProfile()
    {
        $this->client->followRedirects();   
        $crawler = $this->login('mazouthm','admin');

        $link = $crawler->filter('a:contains("membres")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("Malt")')->eq(0)->link();
        $crawler = $this->client->click($link);

         $this->assertSame(1,$crawler->filter('html:contains("Nom du compte")')->count());
         $this->assertSame(1,$crawler->filter('html:contains("Carte de sécurité Cairn")')->count());
         $this->assertSame(1,$crawler->filter('html:contains("Fermer l\'espace membre")')->count());
          $this->assertSame(1,$crawler->filter('html:contains("accès à la plateforme")')->count());
    
        $link = $crawler->filter('a:contains("membres")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("La Dourbie")')->eq(0)->link();
        $crawler = $this->client->click($link);

         $this->assertSame(0,$crawler->filter('html:contains("Nom du compte")')->count());
         $this->assertSame(0,$crawler->filter('html:contains("carte de sécurité Cairn")')->count());
         $this->assertSame(0,$crawler->filter('html:contains("Fermer l\'espace membre")')->count());
         $this->assertSame(0,$crawler->filter('html:contains("accès à la plateforme")')->count());

        $crawler = $this->login('DrDBrew','@@bbccdd');
        $link = $crawler->filter('a:contains("Profil")')->eq(0)->link();
        $crawler = $this->client->click($link);

         $this->assertSame(0,$crawler->filter('html:contains("Nom du compte")')->count());
         $this->assertSame(1,$crawler->filter('html:contains("carte de sécurité Cairn")')->count());
         $this->assertSame(1,$crawler->filter('html:contains("Fermer l\'espace membre")')->count());
         $this->assertSame(0,$crawler->filter('html:contains("accès à la plateforme")')->count());


        $target =  $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'laDourbie'));
        $crawler = $this->client->request('GET', '/user/profile/view/'.$target->getID());
         $this->assertSame(0,$crawler->filter('html:contains("Nom du compte")')->count());
         $this->assertSame(0,$crawler->filter('html:contains("carte de sécurité Cairn")')->count());
         $this->assertSame(0,$crawler->filter('html:contains("Fermer l\'espace membre")')->count());
         $this->assertSame(0,$crawler->filter('html:contains("accès à la plateforme")')->count());

    }

    /**
     * Scénarii pre beneficiary input
     *_pas de carte
     *_carte non active
     *_il n'a jamais rentré de clé
     * Scenarii for input beneficiary :
     *_mauvais nom/mauvais email : pas d utilisateur correspondant
     *_s'ajouter soit même
     *_bon nom/pas d ICC correspondant
     *_bon nom + bon ICC
     */
    public function testAddBeneficiary()
    {
        $this->client->followRedirects();   
        $crawler = $this->login('DrDBrew','@@bbccdd');

        $currentUser =  $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
        $targetOption =  $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'laDourbie'));

        $link = $crawler->filter('a:contains("Virements")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("bénéficiaires")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("Ajouter")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $crawler = $this->login('mazouthm','admin');
        $crawler = $this->client->request('GET', '/card/generate/?id='.$currentUser->getID());

        $this->client->followRedirects();   
        $crawler = $this->login('DrDBrew','@@bbccdd');

        $this->assertSame(1,$crawler->filter('html:contains("pas active")')->count());

        $this->cardKeyInput($crawler,'1112');
        $this->assertSame(1,$crawler->filter('html:contains("invalide")')->count());

        $this->cardKeyInput($crawler,'1111');
        $this->assertSame(1,$crawler->filter('html:contains("Révoquer")')->count());

        $crawler = $this->client->request('GET', '/user/beneficiaries/add/');

        $this->assertSame(1,$crawler->filter('html:contains("Nom du bénéficiaire")')->count());

        $form = $crawler->selectButton('beneficiary_add')->form();
        $form['name']->setValue('La Dourbie');
        $form['ICC']->setValue('1856693867219153118');
        $crawler = $this->client->submit($form);

        $this->assertSame(1,$crawler->filter('html:contains("ICC indiqué ne correspond à aucun")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Nom du bénéficiaire")')->count());

        $form = $crawler->selectButton('beneficiary_add')->form();
        $form['name']->setValue('WHO DIS');
        $form['ICC']->setValue('1856693867219153118');
        $crawler = $this->client->submit($form);

        $this->assertSame(1,$crawler->filter('html:contains("aucun membre")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Nom du bénéficiaire")')->count());

        $form = $crawler->selectButton('beneficiary_add')->form();
        $form['name']->setValue('DrDBrew');
        $form['ICC']->setValue('1856693867219153118');
        $crawler = $this->client->submit($form);

        $this->assertSame(1,$crawler->filter('html:contains("vous-même")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Nom du bénéficiaire")')->count());

        $accounts = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($targetOption->getCyclosID());
        $form = $crawler->selectButton('beneficiary_add')->form();
        $form['name']->setValue('laDourbie');
        $form['ICC']->setValue($accounts[0]->id);
        $crawler = $this->client->submit($form);

        $this->assertSame(1,$crawler->filter('html:contains("succès")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Coordonnées de compte")')->count());

    }

    /**
     *_new beneficiary is not valid xxx
     *_new benef is valid but belongs already to list xxx
     *_new benef is valid and is new
     *_former beneficiary is not associated to any user : remove it
     *_former beneficiary has several associated users
     *@depends testAddBeneficiary
     */
    public function testEditBeneficiary()
    {
        $this->client->followRedirects();   
        $crawler = $this->login('DrDBrew','@@bbccdd');

        $currentUser =  $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
        $targetOption =  $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'laDourbie'));

        $link = $crawler->filter('a:contains("Virements")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("bénéficiaires")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("Modifier")')->eq(0)->link();
        $crawler = $this->client->click($link);

        //the card security layer is not reached(already validated)
        $this->assertSame(1,$crawler->filter('html:contains("carte de sécurité Cairn")')->count());

        $this->cardKeyInput($crawler,'1111');
        $this->assertSame(1,$crawler->filter('html:contains("ICC")')->count());

        $accounts = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($targetOption->getCyclosID());

//        $form = $crawler->selectButton('beneficiary_edit')->form();
//        if(count($accounts > 1)){
//            $form['ICC']->setValue($accounts[1]->id);
//            $this->assertSame(1,$crawler->filter('html:contains("aucun compte de")')->count());
//
//        }

        $form = $crawler->selectButton('beneficiary_edit')->form();
        $form['ICC']->setValue($accounts[0]);
        $this->assertSame(1,$crawler->filter('html:contains("fait déjà partie")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("Coordonnées de compte")')->count());

        $crawler = $crawler->filter('a:contains("Modifier")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('beneficiary_edit')->form();
        $form['ICC']->setValue('1856693867219153118');
        $this->assertSame(1,$crawler->filter('html:contains("ne correspond à aucun compte")')->count());

    }

    /**
     *_removes beneficiary
     *_if it had only one source : remove entity from db
     *@depends testEditBeneficiary
     */
    public function testRemoveBeneficiary()
    {
        $this->client->followRedirects();   
        $crawler = $this->login('DrDBrew','@@bbccdd');

        $currentUser =  $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'DrDBrew'));
        $targetOption =  $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'laDourbie'));

        $link = $crawler->filter('a:contains("Virements")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("bénéficiaires")')->eq(0)->link();
        $crawler = $this->client->click($link);

        $link = $crawler->filter('a:contains("Supprimer")')->eq(0)->link();
        $crawler = $this->client->click($link);

        //check that entity is no more accessible in database
        $targetBenef =  $this->em->getRepository('CairnUserBundle:Beneficiary')->findOneBy(array('user'=>$targetOption));
        $this->assertSame($targetBenef,NULL);
    }

    //scenarii :
    //asked for card (connect with new user)
    //id does not exist
    //_user wants to remove its own member area
    //_user is referent of the one to remove
    //_user is not referent of the one to remove
    //_non_zero account
    //all zeros accounts
    //redirect to login / redirect to list of members
    public function testRemoveUser()
    {
        $this->client->followRedirects();   
        $crawler = $this->login('mazouthm','admin');

        $targetOption1 = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'MaltOBar'));
        $targetOption2 = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'LaDourbie'));

        //security card is asked
        $crawler = $this->client->request('GET', '/user/remove/?id='.$targetOption2->getID());
//        $this->assertSame(1,$crawler->filter('html:contains("carte de sécurité Cairn")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("pas référent de")')->count());
     
 //       $form = $crawler->selectButton('card_save')->form();
 //       $form['card[field]']->setValue('1111');
 //       $crawler = $this->client->submit($form);

        //two pros can't be referent of each other
 //       $this->assertSame(1,$crawler->filter('html:contains("pas référent de")')->count());

        //remove itself but non null balances
        $crawler = $this->client->request('GET', '/user/remove/?id='.$targetOption1->getID());

        $this->assertSame(1,$crawler->filter('html:contains("solde non nul")')->count());


        //remove itself and null balance : reidrection to login page
        $crawler = $this->login('LaDourbie','@@bbccdd');
        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>'LaDourbie'));
        $crawler = $this->client->request('GET', '/user/remove/?id='.$currentUser->getID());

        $form = $crawler->selectButton('card_save')->form();
        $form['card[field]']->setValue('1111');
        $crawler = $this->client->submit($form);

        $form = $crawler->selectButton('form_save')->form();
        $form['form[plainPassword]']->setValue('@@bbccdd');
        $crawler = $this->client->submit($form);

        $this->assertSame(1,$crawler->filter('html:contains("supprimé avec succès")')->count());
        $this->assertSame(1,$crawler->filter('html:contains("security.login.username")')->count());

    }

}
