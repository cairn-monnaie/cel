<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Mandate;


class MandateControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }


    /**
     *@dataProvider provideMandateToAdd
     */
    public function testDeclareMandate($current,$contractor,$amount,$begin,$end,$isValid,$expectedMessage)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $mandateRepo = $this->em->getRepository('CairnUserBundle:Mandate');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));

        $contractor = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$contractor));

        $nbMandatesBefore = count($mandateRepo->findByContractor($contractor));

        $label = $contractor->getAutocompleteLabel() ;

        $beginAt = date_modify(new \Datetime(),$begin);
        $beginAt_format = $beginAt->format('Y-m-d');

        $endAt = date_modify(new \Datetime(),$end);
        $endAt_format = $endAt->format('Y-m-d');


        $crawler = $this->client->request('GET','/admin/mandates/add');

        //$crawler = $this->client->followRedirect();
        //$crawler = $this->inputCardKey($crawler,'1111');
        //$crawler = $this->client->followRedirect();

        if(! $currentUser->hasRole('ROLE_SUPER_ADMIN')){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            return;
        }

        $form = $crawler->selectButton('cairn_userbundle_mandate_forward')->form();
        $form['cairn_userbundle_mandate[contractor]']->setValue($contractor->getID());
        $form['cairn_userbundle_mandate[amount]']->setValue($amount);
        $form['cairn_userbundle_mandate[beginAt]']->setValue($beginAt_format);
        $form['cairn_userbundle_mandate[endAt]']->setValue($endAt_format);

        $crawler = $this->client->submit($form);

        $nbMandatesAfter = count($mandateRepo->findByContractor($contractor));

        $scheduledMandate = $mandateRepo->findOneBy(array('status'=>Mandate::SCHEDULED,'contractor'=>$contractor));

        if($isValid){
            $crawler = $this->client->followRedirect();
            $this->assertSame(1,$crawler->filter('html:contains("succès")')->count());

            $this->assertEquals($nbMandatesAfter, $nbMandatesBefore + 1);
            $this->assertNotEquals($scheduledMandate,NULL);
        }else{
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            $this->assertEquals($nbMandatesAfter, $nbMandatesBefore);
        }
    }


    public function provideMandateToAdd()
    {
        $today = new \Datetime();
        $today_format = $today->format('Y-m-d');

        
        return array(
            'valid first declaration : person'=>array('admin_network','comblant_michel', 30 , '+1 days', '+7 months',true,''),
            'valid first declaration : pro'=>array('admin_network','jardins_epices', 30 , '+1 days', '+7 months',true,''),
            'valid second declaration after complete'=>array('admin_network','lacreuse_desiderata', 30 , '+1 days', '+7 months',true,''),
            'valid second declaration after uptodate'=>array('admin_network','crabe_arnold', 30 , '+4 months', '+11 months',true,''),
            'valid second declaration after overdue'=>array('admin_network','tous_andre', 30 , '+4 months', '+11 months',true,''),
            'invalid second declaration after scheduled'=>array('admin_network','gjanssens', 30 , '+8 months', '+14 months',false,'déjà prévu'),
            'invalid : access disabled to adherents'=>array('comblant_michel','comblant_michel', 30 , '+1 days', '+3 months',false,'au moins 6'),
            'invalid : access disabled to GL'=>array('gl_grenoble','comblant_michel', 30 , '+1 days', '+3 months',false,'au moins 6'),
            'invalid : period too short'=>array('admin_network','comblant_michel', 30 , '+1 days', '+3 months',false,'au moins 6'),
            'invalid : amount too low'=>array('admin_network','comblant_michel', 0.001 , '+1 days', '+6 months',false,'trop faible'),
            'invalid : contractor not an adherent'=>array('admin_network','gl_grenoble', 20 , '+1 days', '+6 months',false,'adhérent'),
            'invalid : future mandate already exists '=>array('admin_network','gjanssens', 20 , '+1 days', '+6 months',false,'déjà prévu'),
            'invalid : intermingle overdued & future mandates '=>array('admin_network','tous_andre', 20 , '+2 months', '+6 months',false,'déjà en cours'),
            'invalid : intermingle uptodate & future mandates '=>array('admin_network','crabe_arnold', 20 , '+2 months', '+6 months',false,'déjà en cours'),
        );
    }

    /**
     * In the current dataset, there is no adherent with several mandates
     * Therefore, fetching mandate id for a specific user can be done safely with a findByOne method
     *
     *@dataProvider provideMandateToCancel
     */
    public function testCancelMandate($current,$contractor,$isValid,$expectedMessage)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));

        $contractor = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$contractor));

        $mandate = $this->em->getRepository('CairnUserBundle:Mandate')->findOneByContractor($contractor);
        $statusBefore = $mandate->getStatus();

        $crawler = $this->client->request('GET','/admin/mandates/cancel/'.$mandate->getID());

        //$crawler = $this->client->followRedirect();
        //$crawler = $this->inputCardKey($crawler,'1111');
        //$crawler = $this->client->followRedirect();

        if(! $currentUser->hasRole('ROLE_SUPER_ADMIN')){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            return;
        }


        if($isValid){
            $form = $crawler->selectButton('form_execute')->form();
    
            $crawler = $this->client->submit($form);
    
            $this->em->refresh($mandate);

            $crawler = $this->client->followRedirect();
            $this->assertSame(1,$crawler->filter('html:contains("succès")')->count());

            $this->assertEquals($mandate->getStatus(), Mandate::CANCELED);

        }else{
            $crawler = $this->client->followRedirect();

            $this->em->refresh($mandate);
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            $this->assertEquals($statusBefore, $mandate->getStatus());
        }

    }

    public function provideMandateToCancel()
    {
        return array(
            'valid uptodate cancellation' => array('admin_network','crabe_arnold',true,''),
            'valid scheduled declaration' => array('admin_network','gjanssens',true,''),
            'invalid : overdued mandate' => array('admin_network','tous_andre',false,'ne peut donc être révoqué'),
            'invalid : mandate already completed' => array('admin_network','lacreuse_desiderata',false,'ne peut donc être annulé'),
            'invalid : mandate already canceled' => array('admin_network','barbare_cohen',false,'déjà été'),
            'invalid : access disabled to adherents'=>array('comblant_michel','crabe_arnold',false,''),
            'invalid : access disabled to GL'=>array('gl_grenoble','crabe_arnold',false,''),
        );
    }

    /**
     *@dataProvider provideMandateToHonour
     */
    public function testHonourMandate($current,$contractor,$expectForm,$isValid,$expectedMessage)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));

        $contractor = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$contractor));

        $mandate = $this->em->getRepository('CairnUserBundle:Mandate')->findOneByContractor($contractor);
        $nbOperationsBefore = $mandate->getOperations()->count();

        $crawler = $this->client->request('GET','/admin/mandates/honour/'.$mandate->getID());

        //$crawler = $this->client->followRedirect();
        //$crawler = $this->inputCardKey($crawler,'1111');
        //$crawler = $this->client->followRedirect();

        if(!$expectForm){
            if(! $currentUser->hasRole('ROLE_SUPER_ADMIN')){
                $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            }else{
                $crawler = $this->client->followRedirect();
                $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            }

            return;
        }
        
        $form = $crawler->selectButton('form_execute')->form();

        $crawler = $this->client->submit($form);

        $this->em->refresh($mandate);
        $crawler = $this->client->followRedirect();

        if($isValid){
            $nbOperationsAfter = $mandate->getOperations()->count();
            $this->assertEquals($nbOperationsAfter, $nbOperationsBefore + 1);

            $status = $mandate->getStatus();
            $boolStatus = ($status == Mandate::COMPLETE || $status == Mandate::UP_TO_DATE);

            $this->assertTrue($boolStatus);
            $this->assertEquals($mandate->getOperations()[$nbOperationsAfter -1]->getType() , Operation::TYPE_MANDATE);
        }else{
            $crawler = $this->client->followRedirect();

            $this->em->refresh($mandate);
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            $this->assertEquals($nbOperationsAfter, $nbOperationsBefore);
        }

    }

    public function provideMandateToHonour()
    {
        $statusErrorMessage = 'est à jour';

        return array(
            'invalid : mandate is uptodate' => array('admin_network','crabe_arnold',false,false,$statusErrorMessage),
            'valid : overdued mandate' => array('admin_network','tous_andre',true,true,''),
            'invalid : mandate is scheduled' => array('admin_network','gjanssens',false,false,$statusErrorMessage),
            'invalid : mandate already completed' => array('admin_network','lacreuse_desiderata',false,false,$statusErrorMessage),
            'invalid : mandate already canceled' => array('admin_network','barbare_cohen',false,false,$statusErrorMessage),
            'invalid : access disabled to adherents'=>array('comblant_michel','crabe_arnold',false,false,''),
            'invalid : access disabled to GL'=>array('gl_grenoble','crabe_arnold',false,false,''),
        );

    }

    
}
