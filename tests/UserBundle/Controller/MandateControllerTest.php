<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Mandate;

use Symfony\Component\HttpFoundation\File\UploadedFile;


class MandateControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }


    /**
     *@dataProvider provideMandateToAdd
     *
     * To deal with mandate dates (begin & end) validation, we are forced to use a workaround in the test : if today's date > 25, we start from first day of next month
     */
    public function testDeclareMandate($current,$contractor,$amount,$begin,$end,$isValid,$expectedMessage, $addDocument)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $mandateRepo = $this->em->getRepository('CairnUserBundle:Mandate');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));

        $contractor = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$contractor));

        $nbMandatesBefore = count($mandateRepo->findByContractor($contractor));

        $label = $contractor->getAutocompleteLabel() ;

        $beginAt = date_modify(new \Datetime(),$begin);

        if($beginAt->format('d') >= 25){
            $beginAt->modify('first day of next month');
        }

        $beginAt_format = $beginAt->format('Y-m-d');

        $endAt = date_modify($beginAt,$end);
        $endAt_format = $endAt->format('Y-m-d');


        $crawler = $this->client->request('GET','/admin/mandates/add');

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        if(! $currentUser->hasRole('ROLE_SUPER_ADMIN')){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            return;
        }


        $form = $crawler->selectButton('cairn_userbundle_mandate_forward')->form();
        $form['cairn_userbundle_mandate[contractor]']->setValue($contractor->getAutocompleteLabel());
        $form['cairn_userbundle_mandate[amount]']->setValue($amount);
        $form['cairn_userbundle_mandate[beginAt]']->setValue($beginAt_format);
        $form['cairn_userbundle_mandate[endAt]']->setValue($endAt_format);

        if($addDocument){
        
            //select pdf file
            $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
            $originalName = 'mandate_template.pdf';                                 
            $absolutePath = $absoluteWebDir.$originalName;

            $file = new UploadedFile($absolutePath, $originalName, 'application/pdf');
            $values = $form->getPhpValues();
            $values['cairn_userbundle_mandate']['mandateDocuments']['0']['file'] = $absolutePath; 

             $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        }else{
            $crawler = $this->client->submit($form);
        }

       
        $nbMandatesAfter = count($mandateRepo->findByContractor($contractor));

        $scheduledMandate = $mandateRepo->findOneBy(array('status'=>Mandate::SCHEDULED,'contractor'=>$contractor));

        if($isValid){
            $crawler = $this->client->followRedirect();
            $this->assertSame(1,$crawler->filter('html:contains("succès")')->count());

            $this->assertEquals($nbMandatesAfter, $nbMandatesBefore + 1);
            $this->assertEquals(1, $mandateRepo->findByContractor($contractor)[0]->getMandateDocuments()->count());
            $this->assertNotEquals($scheduledMandate,NULL);
        }else{
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            $this->assertEquals($nbMandatesAfter, $nbMandatesBefore);
        }
    }


    public function provideMandateToAdd()
    {
                
        return array(
            'valid first declaration : person'=>array('admin_network','comblant_michel', 30 , '+1 days', '+7 months',true,'',true),
            'valid first declaration : pro'=>array('admin_network','jardins_epices', 30 , '+1 days', '+7 months',true,'',true),
            'valid second declaration after complete'=>array('admin_network','lacreuse_desiderata', 30 , '+1 days', '+7 months',true,'',true),
            'valid second declaration after uptodate'=>array('admin_network','crabe_arnold', 30 , '+4 months', '+11 months',true,'',true),
            'valid second declaration after overdue'=>array('admin_network','tous_andre', 30 , '+4 months', '+11 months',true,'',true),
            'invalid second declaration after scheduled'=>array('admin_network','gjanssens', 30 , '+8 months', '+14 months',false,'déjà prévu',true),
            'invalid : access disabled to adherents'=>array('comblant_michel','comblant_michel', 30 , '+1 days', '+3 months',false,'au moins 6',true),
            'invalid : access disabled to GL'=>array('gl_grenoble','comblant_michel', 30 , '+1 days', '+3 months',false,'au moins 6',true),
            'invalid : period too short'=>array('admin_network','comblant_michel', 30 , '+1 days', '+3 months',false,'au moins 6',true),
            'invalid : amount too low'=>array('admin_network','comblant_michel', 0.001 , '+1 days', '+6 months',false,'trop faible',true),
            'invalid : contractor not an adherent'=>array('admin_network','gl_grenoble', 20 , '+1 days', '+6 months',false,'adhérent',true),
            'invalid : future mandate already exists '=>array('admin_network','gjanssens', 20 , '+1 days', '+6 months',false,'déjà prévu',true),
            'invalid : intermingle overdued & future mandates '=>array('admin_network','tous_andre', 20 , '+2 months', '+6 months',false,'déjà en cours',true),
            'invalid : intermingle uptodate & future mandates '=>array('admin_network','crabe_arnold', 20 , '+2 months', '+6 months',false,'déjà en cours',true),
            'invalid : no document uploaded' => array('admin_network','comblant_michel', 30,'+1 month', '+8 months',false,'Aucun',false)
        );
    }

    /**
     *@dataProvider provideMandateToEdit
     *
     * WARNING : adding a new document can be done using javascript code. Therefore, 
     */
    public function testEditMandate($current,$contractor,$amount,$end,$expectForm,$isValid,$expectedMessage,$addDocument)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $mandateRepo = $this->em->getRepository('CairnUserBundle:Mandate');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));
        $contractor = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$contractor));

        $mandate = $mandateRepo->findOneByContractor($contractor);

        $nbMandatesBefore = count($mandateRepo->findByContractor($contractor));


        $crawler = $this->client->request('GET','/admin/mandates/edit/'.$mandate->getID());

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        if(!$expectForm){
            if(! $currentUser->hasRole('ROLE_SUPER_ADMIN')){
                $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            }else{
                $crawler = $this->client->followRedirect();
                $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            }

            return;
        }

        $form = $crawler->selectButton('cairn_userbundle_mandate_forward')->form();

        $this->assertTrue($form['cairn_userbundle_mandate[contractor]']->isDisabled());
        $this->assertTrue($form['cairn_userbundle_mandate[beginAt]']->isDisabled());

        if($amount){
            $form['cairn_userbundle_mandate[amount]']->setValue($amount);
        }else{
            $form['cairn_userbundle_mandate[amount]']->setValue($mandate->getAmount());
        }

        if($end){
            $endAt = date_modify($mandate->getBeginAt(),$end);
            $endAt_format = $endAt->format('Y-m-d');

            $form['cairn_userbundle_mandate[endAt]']->setValue($endAt_format);
        }else{
            $form['cairn_userbundle_mandate[endAt]']->setValue($mandate->getEndAt()->format('Y-m-d'));
        }

        //select pdf file
        $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
        $originalName = 'mandate_template.pdf';                                 
        $absolutePath = $absoluteWebDir.$originalName;

        $copyPath1 =$absoluteWebDir.rand(1000,10000).'.pdf'; 
        //copy 
        if(! copy($absolutePath,$copyPath1 )){
            echo "Failed to copy";
            return;
        }
        $file1 = new UploadedFile($copyPath1, $originalName, 'application/pdf',123);


        $values = $form->getPhpValues();

        if($addDocument){
            $copyPath2 =$absoluteWebDir.rand(1000,10000).'.pdf'; 
            //copy 
            if(! copy($absolutePath,$copyPath2 )){
                echo "Failed to copy";
                return;
            }
            $file2 = new UploadedFile($copyPath2, $originalName, 'application/pdf',123);

            $documents = array(
                    '0'=>array('file'=>$file1),
                    '1'=>array('file'=>$file2)
                );
        }else{
            $documents = array(
                    '0'=>array('file'=>$file1)
                );
        }

        $files = array(
            'cairn_userbundle_mandate'=> array(
                'mandateDocuments'=>$documents
            )
        );

        $countDocsBefore = $mandate->getMandateDocuments()->count();

                
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $files);

        if($isValid){
            $crawler = $this->client->followRedirect();

            $this->em->refresh($mandate);

            if($addDocument){
                $this->assertEquals($countDocsBefore + 1, $mandate->getMandateDocuments()->count());
            }
        }else{
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
        }
    }

    public function provideMandateToEdit()
    {
                
        return array(
            'invalid : access disabled to adherents'=>array('comblant_michel','crabe_arnold', 30 ,'+3 months',false,false,'',false),
            'invalid : access disabled to GL'=>array('gl_grenoble','crabe_arnold', 30 ,'+3 months',false,false,'',false),
            'valid : uptodate mandate amount + doc added' => array('admin_network','gjanssens', 40, '+7 months',true,true,'',true),
            'valid : uptodate mandate amount + doc not added'=>array('admin_network','gjanssens', 40, '+7 months',true,true,'',false),
            'valid : mandate end date + doc added'=>array('admin_network','crabe_arnold', NULL, '+8 months',true, true,'',true),
            'valid : mandate end date + doc not added'=>array('admin_network','crabe_arnold', NULL, '+8 months',true, true,'',false),
            'invalid : mandate is complete'=>array('admin_network','lacreuse_desiderata', 30 , '+7 months',false,false,'achevé',false),
            'invalid : mandate is canceled'=>array('admin_network','barbare_cohen', 30 , '+7 months',false,false,'achevé',false),
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

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        if(! $currentUser->hasRole('ROLE_SUPER_ADMIN')){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            return;
        }


        if($isValid){
            $form = $crawler->selectButton('confirmation_save')->form();
    
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

        $crawler = $this->client->followRedirect();
        $crawler = $this->inputCardKey($crawler,'1111');
        $crawler = $this->client->followRedirect();

        if(!$expectForm){
            if(! $currentUser->hasRole('ROLE_SUPER_ADMIN')){
                $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            }else{
                $crawler = $this->client->followRedirect();
                $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
            }

            return;
        }
        
        $form = $crawler->selectButton('confirmation_save')->form();

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
