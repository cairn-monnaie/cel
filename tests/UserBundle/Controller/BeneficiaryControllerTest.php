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
    public function testAddBeneficiary($current,$value,$isValid)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));

        $nbBeneficiariesBefore = count($debitorUser->getBeneficiaries());

        $crawler = $this->client->request('GET','/user/beneficiaries/add');

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('form_add')->form();
        $form['form[cairn_user]']->setValue($value);
        $crawler = $this->client->submit($form);

        if($isValid){
            $this->assertTrue($this->client->getResponse()->isRedirect('/user/beneficiaries/list'));
            $crawler = $this->client->followRedirect();

            $this->em->refresh($debitorUser);
            $this->assertTrue( count($debitorUser->getBeneficiaries()) == $nbBeneficiariesBefore + 1);
        }else{
            $this->assertTrue( count($debitorUser->getBeneficiaries()) == $nbBeneficiariesBefore);

            $isRedirect = $this->client->getResponse()->isRedirect();
            $this->assertTrue($isRedirect || strpos($this->client->getResponse()->getContent(), 'Aucun compte') !== false);
        }
    }

    public function provideBeneficiariesToAdd()
    {
        $creditorUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername('alter_mag');
        $ICC = $creditorUser->getMainICC();

        return array(
            'self beneficiary'=> array('current'=>'vie_integrative','value'=>'vie_integrative@test.fr','isValid'=>false),

            'user not found'=> array('current'=>'vie_integrative','value'=>'malt@cairn-monnaie.fr','isValid'=>false),

            'ICC not found'=>array('current'=>'vie_integrative', 'value'=>'123456789','isValid'=>false),

            'ICC found'=>array('current'=>'vie_integrative', 'value'=>$ICC,'isValid'=>false),

            'pro adds pro'=>array('current'=>'vie_integrative','value'=>'alter_mag@test.fr','isValid'=>true),

            'already benef'=>array('current'=>'nico_faus_prod','value'=>'labonnepioche@test.fr','isValid'=>false),

            'pro adds person'=>array('current'=>'labonnepioche','value'=>'alberto_malik@test.fr','isValid'=>true),

            'person adds person'=>array('current'=>'cretine_agnes', 'value'=>'alberto_malik@test.fr','isValid'=>true),

            'person adds pro'=>array('current'=>'cretine_agnes','value'=>'labonnepioche@test.fr','isValid'=>true),              

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

        $crawler = $this->client->request('GET','/user/beneficiaries/remove/'.$creditorUser->getMainICC());

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
