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

class BeneficiaryControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

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

        if(!$creditorUser){
            $ICC = 123456789;
        }else{
            $test = $this->container->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($creditorUser->getUsername());
            $ICC = $test->accountNumber;
            $ICC = ($changeICC) ? $ICC + 1 : $ICC;
        }

        $crawler = $this->client->request('GET','/user/beneficiaries/add');

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
        }else{
            $this->assertTrue($this->client->getResponse()->isRedirect() || ($this->client->getResponse()->getStatusCode() == 401));
        }
    }

    public function provideBeneficiariesToAdd()
    {
        return array(
            'self beneficiary'=> array('current'=>'vie_integrative','name'=>'vie','email'=>'vie_integrative@test.fr',
                                       'changeICC'=>false,'isValid'=>false,'expectKey'=>'error'), 

            'user not found'=> array('current'=>'vie_integrative','name'=>'Malt','email'=>'malt@cairn-monnaie.fr',
                                     'changeICC'=>false,'isValid'=>false,'expectMessage'=>'error'),              

            'ICC not found'=>array('current'=>'vie_integrative', 'name'=>'Alter Mag','email'=>'alter_mag@test.fr',
                                   'changeICC'=>true,'isValid'=>false,'expectMessage'=>'error'),              

            'pro adds pro'=>array('current'=>'vie_integrative', 'name'=>'Alter Mag','email'=>'alter_mag@test.fr',
                                 'changeICC'=>false,'isValid'=>true,'expectMessage'=>'success'),              

            'already benef'=>array('current'=>'nico_faus_prod','name'=>'La Bonne Pioche','email'=>'labonneioche@test.fr',
                                   'changeICC'=>false,'isValid'=>false,'expectMessage'=>'info'),              

            'pro adds person'=>array('current'=>'labonnepioche','name'=>'Malik Alberto','email'=>'alberto_malik@test.fr',
                                     'changeICC'=>false,'isValid'=>true,'expectMessage'=>'success'),              

            'person adds person'=>array('current'=>'cretine_agnes','name'=>'Malik Alberto','email'=>'alberto_malik@test.fr',
                                        'changeICC'=>false,'isValid'=>true,'expectMessage'=>'success'),              

            'person adds pro'=>array('current'=>'cretine_agnes','name'=>'La Bonne Pioche','email'=>'labonneioche@test.fr',
                                     'changeICC'=>false,'isValid'=>true,'expectMessage'=>'success'),              

        );
    }

    //    /**
    //     *depends testAddBeneficiary
    //     */
    //    public function testEditBeneficiary()
    //    {
    //        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'));
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

        // ---------- second valid removal from a source ------------
        $crawler = $this->removeBeneficiaryAction('le_marque_page','labonnepioche',true);

        $creditorUser = $userRepo->findOneBy(array('username'=>'labonnepioche'));
        $beneficiary = $benefRepo->findOneBy(array('user'=>$creditorUser));

        //check that beneficiary entity has been removed because its number of sources is 0
        $this->assertEquals($beneficiary,NULL);
        $crawler = $this->client->followRedirect();

        //test invalid removal : beneficiary exists but current user is not a source
        $crawler = $this->removeBeneficiaryAction('nico_faus_prod','ferme_bressot',false);
        $crawler = $this->client->followRedirect();

    }

}
