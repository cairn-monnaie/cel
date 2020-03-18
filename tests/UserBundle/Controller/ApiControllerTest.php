<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApiControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     *
     *@dataProvider provideDataForSmsData
     */
    public function testGetPhonesAction($username, $isEmpty, $httpResponse)
    {
        $this->mobileLogin($username,'@@bbccdd');
        $crawler = $this->client->request('GET','/mobile/phones');

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){

            if($isEmpty){
                $this->assertEquals($responseData, []);
            }else{
                foreach($responseData as $phone){
                    $this->assertSerializedEntityContent($phone,'phone');
                }
            }

        }
    }

    public function provideDataForSmsData()
    {
        return array(
            'user has no phone number'=>array('username'=>'gjanssens',true, Response::HTTP_OK),
            'user has phone number'=>array('username'=>'nico_faus_prod',false, Response::HTTP_OK)
        );
    }

    /**
     *
     *
     *@dataProvider provideDataForUsers
     */
    public function testGetUsersAction($username, $httpResponse, $nbUsers)
    {
        $this->mobileLogin($username,'@@bbccdd');
        $crawler = $this->client->request('POST','/mobile/users');

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            foreach($responseData as $user){
                $this->assertSerializedEntityContent($user,'user');
            }
        }
    }

    public function provideDataForUsers()
    {
        return array(
            'request as a person' => array('username'=>'gjanssens', Response::HTTP_OK,49),
            'request as a pro' => array('username'=>'nico_faus_prod', Response::HTTP_OK,49),
            'request as GL Gre' => array('username'=>'gl_grenoble', Response::HTTP_OK,1),
            'request as a super admin' => array('username'=>$this->testAdmin, Response::HTTP_OK,57)
        );
    }

    /**
     *
     *@dataProvider provideDataForProfile
     */
    public function testGetProfileAction($current,$target,$httpResponse)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($target);

        $crawler = $this->client->request('GET','/mobile/users/'.$targetUser->getID());

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            $this->assertSerializedEntityContent($responseData,'user');
        }
    }

    public function provideDataForProfile()
    {
        return array(
            'valid self'=>array('gjanssens','gjanssens',Response::HTTP_OK),
            'valid referent'=>array('admin_network','gjanssens',Response::HTTP_OK),
            'valid super admin to self'=>array('admin_network','admin_network',Response::HTTP_OK),
            'valid super admin to admin'=>array('admin_network','gl_grenoble',Response::HTTP_OK),
            'invalid referent'=>array('admin_network','NaturaVie',Response::HTTP_FORBIDDEN),
            'invalid referent'=>array('gjanssens','comblant_michel',Response::HTTP_FORBIDDEN),
            'invalid admin to superadmin'=>array('gl_grenoble','admin_network',Response::HTTP_FORBIDDEN),
        );
    }

    /**
     *
     *@dataProvider provideDataForBeneficiaries
     */
    public function testGetBeneficiariesAction($current,$isEmpty,$httpResponse)
    {
        $this->mobileLogin($current,'@@bbccdd');
        $crawler = $this->client->request('GET','/mobile/beneficiaries');

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            if($isEmpty){
                $this->assertEquals($responseData, []);
            }else{
                foreach($responseData as $beneficiary){
                    $this->assertSerializedEntityContent($beneficiary,'beneficiary');
                }
            }
        }
    }

    public function provideDataForBeneficiaries()
    {
        return array(
            'valid empty'=>array('gjanssens',true,Response::HTTP_OK),
            'valid has beneficiary'=>array('nico_faus_prod',false,Response::HTTP_OK),
            'valid has beneficiary 2'=>array('le_marque_page',false,Response::HTTP_OK),
        );
    }

    /**
     *
     *@dataProvider provideDataForAddBeneficiary
     */
    public function testRemoteAddBeneficiaryAction($current,$beneficiaryValue,$httpResponse)
    {
        $this->mobileLogin($current,'@@bbccdd');
        $crawler = $this->client->request(
            'POST',
            '/mobile/beneficiaries',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                array(
                    'cairn_user'=>$beneficiaryValue,

                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            $this->assertSerializedEntityContent($responseData,'beneficiary');
        }
    }

    public function provideDataForAddBeneficiary()
    {
        return array(
            'self beneficiary'=> array('vie_integrative','vie_integrative@test.fr',Response::HTTP_BAD_REQUEST),
            'user not found'=> array('vie_integrative','malt@cairn-monnaie.fr',Response::HTTP_BAD_REQUEST),
            'ICC not found'=>array('vie_integrative', '123456789',Response::HTTP_BAD_REQUEST),
            'pro adds pro'=>array('vie_integrative','alter_mag@test.fr',Response::HTTP_CREATED),
            'already benef'=>array('nico_faus_prod','labonnepioche@test.fr',Response::HTTP_BAD_REQUEST),
            'pro adds person'=>array('labonnepioche','alberto_malik@test.fr',Response::HTTP_CREATED),
            'person adds person'=>array('cretine_agnes','alberto_malik@test.fr',Response::HTTP_CREATED),
            'person adds pro'=>array('cretine_agnes','labonnepioche@test.fr',Response::HTTP_CREATED),              
        );
    }

    /**
     *
     *@dataProvider provideDataForDeleteBeneficiary
     */
    public function testRemoteDeleteBeneficiaryAction($current,$beneficiary,$httpResponse)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $ICC = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($beneficiary)->getMainICC();

        $crawler = $this->client->request(
            'DELETE',
            '/mobile/beneficiaries/'.$ICC,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                array(
                    'save'=> ""
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

    }

    public function provideDataForDeleteBeneficiary()
    {
        return array(
            'valid removal'=> array('nico_faus_prod','labonnepioche',Response::HTTP_OK),
            'valid removal 2'=> array('le_marque_page','labonnepioche',Response::HTTP_OK),
            'invalid : beneficiary does not exist'=> array('nico_faus_prod','comblant_michel',Response::HTTP_NOT_FOUND),
            'invalid : target not beneficiary of user'=> array('labonnepioche','ferme_bressot',Response::HTTP_BAD_REQUEST),
        );
    }

    /**
     *
     *@dataProvider provideDataForRemoteAddPhone
     */
    public function testRemoteAddPhone($current, $newPhoneSubmit, $httpPhoneStatusCode,$code,$httpValidationStatusCode)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($current);

        $crawler = $this->client->request(
            'POST',
            '/mobile/phones',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                array(
                    'phoneNumber'=> $newPhoneSubmit['phoneNumber'],
                    'paymentEnabled'=> 'true'
                )
            )
        );

        
        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpPhoneStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){

            $crawler = $this->client->request(
                'POST',
                '/mobile/phones',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode(
                    array(
                        'activationCode'=> $code,
                    )
                )
            );

            $response = $this->client->getResponse();

            
            $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
            $this->assertJson($response->getContent());
            $this->assertEquals($httpValidationStatusCode, $response->getStatusCode());

            $responseData = json_decode($response->getContent(),true);

            if($response->isSuccessful()){
                $this->assertSerializedEntityContent($responseData,'phone');
            }else{
                $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($current);

                //this assertation is necessary to keep data consistent between Cyclos & Symfony
                if($currentUser->getPhoneNumberActivationTries() >= 3){
                    $this->assertUserIsDisabled($currentUser,true);
                }
            }
        }
    }

    public function provideDataForRemoteAddPhone()
    {
        $admin = $this->testAdmin;
        $baseData = array('current'=>'stuart_andrew',
            'newPhone'=>array('phoneNumber'=>'+33699999999','identifier'=>'IDSMS'),
            'httpPhoneStatusCode'=>Response::HTTP_OK,
            'code'=>'1111',
            'httpValidationStatusCode'=>Response::HTTP_CREATED,
        );

        $validDataMsg = 'Un code vous a été envoyé';
        $validCodeMsg = 'enregistré';
        $usedMsg = 'déjà utilisé';
        return array(
            'user in admin' => array_replace($baseData, array('current'=>$admin, 'httpPhoneStatusCode'=>Response::HTTP_FORBIDDEN)),

            'too many requests'=>array_replace($baseData, array('current'=>'crabe_arnold','httpPhoneStatusCode'=>Response::HTTP_FORBIDDEN)),

            'current number'=>array_replace_recursive($baseData, array(
                        'newPhone'=>array('phoneNumber'=>'+33743434343'),'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST)
                    ),

            'current number, disable sms'=>array_replace_recursive($baseData, array(
                        'newPhone'=>array('phoneNumber'=>'+33743434343'),'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST)
                    ),

            'used by pro & person'=>array_replace_recursive($baseData, array(
                        'newPhone'=>array('phoneNumber'=>'+33612345678'),'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                        )),

            'pro request : used by pro'=>array_replace_recursive($baseData, array('current'=>'maltobar',
                    'newPhone'=>array('phoneNumber'=>'+33612345678'), 'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                    )),

            'person request : used by person'=>array_replace_recursive($baseData, array(
                    'newPhone'=>array('phoneNumber'=>'+33612345678'),  'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                )),

            'pro request : used by person'=>array_replace_recursive($baseData,array('current'=>'maltobar',
                    'newPhone'=>array('phoneNumber'=>'+33644332211')
                )),

            'person request : used by pro'=>array_replace_recursive($baseData, array('current'=>'benoit_perso',
                    'newPhone'=>array('phoneNumber'=>'+33611223344')
                    )),

            'last remaining try : valid code'=>array_replace($baseData, array('current'=>'hirundo_archi'
                )),

            'last remaining try : wrong code'=>array_replace($baseData, array('current'=>'hirundo_archi',
                    'httpValidationStatusCode'=>Response::HTTP_BAD_REQUEST, 'code'=>'2222'
                    )),

            'user with no phone number'=>array_replace($baseData, array('current'=>'noire_aliss'
                )),

        );
    }

    /**
     *
     *@dataProvider provideDataForRemoteEditPhone
     */
    public function testRemoteEditPhone($current,$target, $newPhoneSubmit,$isNewPhoneNumber, $httpPhoneStatusCode,$code,$httpValidationStatusCode)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($current);
        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($target);

        $phones = $targetUser->getPhones();

        if(empty($phones)){
            echo 'TEST SKIPPED : INVALID DATA';
            return;
        }

        $crawler = $this->client->request(
            'POST',
            '/mobile/phones/'.$phones[0]->getID(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                $newPhoneSubmit
            )
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpPhoneStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            if(!$isNewPhoneNumber){
                $this->assertSerializedEntityContent($responseData['phone'],'phone');
                return;
            }

            $crawler = $this->client->request(
                'POST',
                $responseData['validation_url'],
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode(
                    array(
                        'activationCode'=> $code,
                    )
                )
            );

            $response = $this->client->getResponse();

            $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
            $this->assertJson($response->getContent());
            $this->assertEquals($httpValidationStatusCode, $response->getStatusCode());

            $responseData = json_decode($response->getContent(),true);

            if($response->isSuccessful()){
                $this->assertSerializedEntityContent($responseData,'phone');
            }else{
                $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($current);

                //this assertation is necessary to keep data consistent between Cyclos & Symfony
                if($currentUser->getPhoneNumberActivationTries() >= 3){
                    $this->assertUserIsDisabled($currentUser,true);
                }
            }

        }
    }

    public function provideDataForRemoteEditPhone()
    {
        $admin = $this->testAdmin;
        $baseData = array('current'=>'stuart_andrew','target'=>'stuart_andrew',
            'newPhone'=>array('phoneNumber'=>'+33699999999','paymentEnabled'=>true),
            'isPhoneNumberEdit'=>true,
            'httpPhoneStatusCode'=>Response::HTTP_OK,
            'code'=>'1111',
            'httpValidationStatusCode'=>Response::HTTP_CREATED,
        );

        $baseAdminData = array('current'=>$admin,'target'=>'stuart_andrew',
            'newPhone'=>array('paymentEnabled'=>true,'identifier'=>'IDSMS'),
            'isPhoneNumberEdit'=>true,
            'httpPhoneStatusCode'=>Response::HTTP_OK,
            'code'=>'1111',
            'httpValidationStatusCode'=>Response::HTTP_CREATED,
        );

        return array(
            'not referent'=>array_replace($baseData, array('current'=>$admin,'target'=>'stuart_andrew', 'httpPhoneStatusCode'=>Response::HTTP_FORBIDDEN)),

            'too many requests'=>array_replace($baseData, array('current'=>'crabe_arnold','target'=>'crabe_arnold', 'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST)),

            'current number'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar','isPhoneNumberEdit'=>false,
                                                              'newPhone'=>array('phoneNumber'=>'+33611223344')
                                                    )),

            'invalid number'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
                                                            'newPhone'=>array('phoneNumber'=>'+33811223344'), 'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                                                    )),

            'admin enables sms'=>array_replace($baseAdminData, array('current'=>$admin,'target'=>'la_mandragore','isPhoneNumberEdit'=>false)),

            'new number'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar')),

            'used by pro & person'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
                            'newPhone'=>array('phoneNumber'=>'+33612345678'),'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                            )),

            'pro request : used by pro'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
                                                        'newPhone'=>array('phoneNumber'=>'+33612345678'),'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                                                    )),

            'person request : used by person'=>array_replace_recursive($baseData, array('current'=>'benoit_perso','target'=>'benoit_perso',
                                                        'newPhone'=>array('phoneNumber'=>'+33612345678'),'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                                                    )),

            'pro request : used by person'=>array_replace_recursive($baseData,array('current'=>'maltobar','target'=>'maltobar',
                                                        'newPhone'=>array('phoneNumber'=>'+33644332211')
                                                    )),

            'person request : used by pro'=>array_replace_recursive($baseData, array('current'=>'benoit_perso','target'=>'benoit_perso',
                                                        'newPhone'=>array('phoneNumber'=>'+33611223344'),
                                                    )),
    
            'last remaining try : valid code'=>array_replace($baseData, array('current'=>'hirundo_archi','target'=>'hirundo_archi')),

            'last remaining try : wrong code'=>array_replace($baseData, array('current'=>'hirundo_archi','target'=>'hirundo_archi',
                                                                'code'=>'2222','httpValidationStatusCode'=>Response::HTTP_BAD_REQUEST
                                                        )),

            '2 accounts associated before: valid code'=>array_replace($baseData,array('current'=>'nico_faus_perso','target'=>'nico_faus_perso')),
        );
    }

    /**
     *
     *@dataProvider provideDataForRemoteDeletePhone
     */
    public function testRemoteDeletePhone($current, $target, $httpStatusCode)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($target);

        $crawler = $this->client->request(
            'DELETE',
            '/mobile/phones/'.$targetUser->getPhones()[0]->getID(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            []
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);
    }

    public function provideDataForRemoteDeletePhone()
    {   
        return array(
            'valid self'=>array('nico_faus_prod','nico_faus_prod',Response::HTTP_OK),
            'valid referent'=>array('admin_network','nico_faus_prod',Response::HTTP_OK),
            'invalid referent'=>array('admin_network','stuart_andrew',Response::HTTP_FORBIDDEN),
            'invalid referent'=>array('gjanssens','nico_faus_prod',Response::HTTP_FORBIDDEN),
        );
    }

    /**
     *
     *@dataProvider provideDataForTransaction
     */
    public function testRemoteTransaction($debitor, $formSubmit, $httpStatusCode)
    {
        $this->mobileLogin($debitor,'@@bbccdd');

        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($debitor);

        $crawler = $this->client->request(
            'POST',
            '/mobile/transaction/request/new-unique',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                array_merge(array('fromAccount'=>$debitorUser->getMainICC()), $formSubmit)
            )
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            $this->assertSerializedEntityContent($responseData['operation'],'operation');

            $this->assertNull($responseData['operation']['paymentID']);

            $this->mobileLogin($debitor,'@@bbccdd');
            $crawler = $this->client->request(
                'POST',
                '/mobile/transaction/confirm/'.$responseData['operation']['id'],
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode(
                    array(
                        'api_secret' => hash('sha256',$this->container->getParameter('api_secret').$responseData['operation']['id']), 
                        "confirmationCode"=> "1111",
                        'save'=>""
                    )
                )
            );

            $response = $this->client->getResponse();

            $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
            $this->assertJson($response->getContent());
            $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

            $responseData = json_decode($response->getContent(),true);

            $this->assertNotNull($responseData['paymentID']);
            $this->assertSerializedEntityContent($responseData,'operation');
        }
    }

    public function provideDataForTransaction()
    {
        $now = new \Datetime();
        $nowFormat = date('Y-m-d');
        $later = $now->modify('+2 days')->format('Y-m-d');
        $before = $now->modify('-10 days')->format('Y-m-d');
        $inconsistent = $now->modify('+4 years')->format('Y-m-d');

        $validLogin = 'benoit_perso';
        $baseSubmit = array(
                    'toAccount'=>'labonnepioche@test.fr',
                    'amount'=>25,
                    'reason'=>'Test reason',
                    'description'=>'Test description',
                    'executionDate'=> time($nowFormat),
                    'api_secret' => hash('sha256',$this->container->getParameter('api_secret').time($nowFormat)) 
            );

        return array(
            'invalid amount too low'=>array($validLogin,array_replace($baseSubmit, array('amount'=>0.0001)),Response::HTTP_BAD_REQUEST),
            'invalid negative amount'=>array($validLogin,array_replace($baseSubmit, array('amount'=>-5)),Response::HTTP_BAD_REQUEST),
            'invalid insufficient balance'=>array($validLogin,array_replace($baseSubmit, array('amount'=>1000000000)),Response::HTTP_BAD_REQUEST),
            'invalid : identical creditor & debitor'=>array($validLogin,array_replace($baseSubmit, array('toAccount'=>$validLogin.'@test.fr')),Response::HTTP_UNAUTHORIZED),
            'invalid : no creditor data'=>array($validLogin,array_replace($baseSubmit, array('toAccount'=>'')),Response::HTTP_BAD_REQUEST),
            'invalid : no phone number associated'=>array('gjanssens',$baseSubmit,Response::HTTP_UNAUTHORIZED),
            'valid now'=>array($validLogin,$baseSubmit,Response::HTTP_CREATED),
            'invalid execution date format'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$later)),Response::HTTP_BAD_REQUEST),
            'invalid API key'=>array($validLogin,array_replace($baseSubmit,
                                     array('executionDate'=>time($later),'api_secret'=>'ABCDE')),Response::HTTP_UNAUTHORIZED),
            'valid after'=>array($validLogin,array_replace($baseSubmit, 
                                    array('executionDate'=>time($later),
                                        'api_secret'=>hash('sha256',$this->container->getParameter('api_secret').time($later)) )),
                                            Response::HTTP_CREATED),
            'invalid before'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$before)),Response::HTTP_BAD_REQUEST),
            'invalid inconsistent'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$inconsistent)),Response::HTTP_BAD_REQUEST),
        );

    }

    /**
     *
     *@dataProvider provideDataForAccountOperations
     */
    public function testGetAccountOperations($current,$target,$httpStatusCode,$beginDate,$endDate)
    {
        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($target);

        //connect to Cyclos to get account ID of the target
        $credentials = array('username'=>$target,'password'=>'@@bbccdd');
        $network = $this->container->getParameter('cyclos_currency_cairn');
        $group = $this->container->getParameter('cyclos_group_network_admins');
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($network,'login',$credentials);

        $accounts = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($targetUser->getCyclosID());

        $this->mobileLogin($current,'@@bbccdd');

        $crawler = $this->client->request(
            'POST',
            '/mobile/account/operations/'.$accounts[0]->id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                array(
                    "begin"=> $beginDate,
                    "end"=> $endDate,
                    "minAmount"=> "",
                    "maxAmount"=> "",
                    "keywords"=> "",
                    "types"=> [],
                    "orderBy"=> "ASC"
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            foreach($responseData as $operation){
                $this->assertSerializedEntityContent($operation,'operation');
                $this->assertNotNull($operation['paymentID']);
            }
        }
    }

    public function provideDataForAccountOperations()
    {
        $now = new \Datetime();
        $beginDate =$now->modify('-1 month')->format('Y-m-d'); 
        $endDate = date('Y-m-d'); 
        return array(
            'invalid adherent to adherent'=>array('gjanssens','labonnepioche',Response::HTTP_NOT_FOUND,'',''),
            'invalid admin referent to adherent'=>array('gl_grenoble','episol',Response::HTTP_FORBIDDEN,'',''),
            'valid super admin referent to adherent'=>array('admin_network','gjanssens',Response::HTTP_OK,$beginDate,$endDate),
            'valid super admin to self'=>array('admin_network','admin_network',Response::HTTP_OK,$beginDate,$endDate),
            'valid pro to self'=>array('nico_faus_prod','nico_faus_prod',Response::HTTP_OK,$beginDate,$endDate),
            'valid person to self'=>array('gjanssens','gjanssens',Response::HTTP_OK,$beginDate,$endDate),
        );

    }

    /**
     *
     *@dataProvider provideDataForGetTransfer
     */
    public function testGetTransfer($current,$target,$httpStatusCode)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($target);
        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($current);

        $operationRepo = $this->em->getRepository('CairnUserBundle:Operation');
        $ob = $operationRepo->createQueryBuilder('o');
        $operationRepo->whereInvolvedAccountNumber($ob, $targetUser->getMainICC());
        $ob->andWhere('o.paymentID is not NULL');

        $operations = $ob->getQuery()->getResult();

        $crawler = $this->client->request('GET','/mobile/operations/'.$operations[0]->getPaymentID());

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            $this->assertSerializedEntityContent($responseData,'operation');
            $this->assertNotNull($responseData['paymentID']);
        }
    }

    public function provideDataForGetTransfer()
    {
        $now = new \Datetime();
        $beginDate =$now->modify('-1 month')->format('Y-m-d'); 
        $endDate = date('Y-m-d'); 
        return array(
            'invalid admin referent to adherent operation'=>array('gl_grenoble','episol',Response::HTTP_FORBIDDEN),
            'valid super admin referent to adherent'=>array('admin_network','gjanssens',Response::HTTP_OK),
            'valid pro to self'=>array('nico_faus_prod','nico_faus_prod',Response::HTTP_OK),
            'valid person to self'=>array('gjanssens','gjanssens',Response::HTTP_OK),
        );

    }

    /**
     *
     *@dataProvider provideDataForAccountsOverview
     */
    public function testGetAccountsOverview($current,$httpStatusCode)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $crawler = $this->client->request('GET','/mobile/accounts');

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            foreach($responseData as $account){
                $this->assertSerializedEntityContent($account,'account');
            }
        }
    }

    public function provideDataForAccountsOverview()
    {
        return array(
            'pro'=>array('nico_faus_prod',Response::HTTP_OK),
            'person'=>array('gjanssens',Response::HTTP_OK),
            'admin'=>array('gl_grenoble',Response::HTTP_OK),
            'super admin'=>array('admin_network',Response::HTTP_OK),
        );

    }

    /**
     *
     *@dataProvider provideDataForNewUser
     */
    public function testCreateNewUser($email,$hasUploadedFile,$httpStatusCode)
    {
        $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
        $originalName = 'john-doe-id.png';                                 
        $absolutePath = $absoluteWebDir.$originalName;

        if($hasUploadedFile){
            $copyPath =$absoluteWebDir.rand(1000,10000).'.png'; 
            //copy 
            if(! copy($absolutePath,$copyPath )){
                echo "Failed to copy";
                return;
             }

            $file = new UploadedFile($copyPath,'test_register.png',null,null,null, true);
        }else{
            $file = array();
        }

        $crawler = $this->client->request(
            'POST',
            '/mobile/users/registration',
            array(
                'fos_user_registration_form'=>
                array(
                    'email'=>$email,
                    'name'=>"New User",
                    'address'=>array(
                        'street1'=>'10 rue du test',
                        'street2'=>'',
                        'zipCity'=>'38000 Grenoble'
                    ),
                    'description'=>'test',
                    'identityDocument'=>array(
                        'file'=>$file
                    )
                )
            ),
            [],
            ['Content-Type' => 'multipart/formdata']
        );


        $response = $this->client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            $this->assertSerializedEntityContent($responseData,'user');
            $this->assertNull($responseData['id']);
        }else{
            if($hasUploadedFile){
                unlink($copyPath);
             }
        }
    }

    public function provideDataForNewUser()
    {
        return array(
            'email already in use'=>array('labonnepioche@test.fr',true,Response::HTTP_BAD_REQUEST),
            'invalid email : no @'=>array('test.com',true,Response::HTTP_BAD_REQUEST),
            'invalid email : not enough characters'=>array('test@t.c',true,Response::HTTP_BAD_REQUEST),
            'no document file'=>array('newuser@test.fr',false,Response::HTTP_BAD_REQUEST),
            'valid registration'=>array('newuser@test.fr',true,Response::HTTP_CREATED),
        );
    }

    /**
     *
     *@dataProvider provideDataForEditProfile
     */
    public function testRemoteEditProfile($current, $email,$hasUploadedFile,$httpStatusCode)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
        $originalName = 'john-doe-id.png';                                 
        $absolutePath = $absoluteWebDir.$originalName;

        if($hasUploadedFile){
            $file = new UploadedFile($absolutePath,$originalName,null,null,null, true);
        }else{
            $file = '';
        }

        $crawler = $this->client->request(
            'POST',
            '/mobile/users/profile',
            array(
                'fos_user_profile_form'=>
                array(
                    'email'=>$email,
                    'name'=>"New User",
                    'address'=>array(
                        'street1'=>'10 rue du test',
                        'street2'=>'',
                        'zipCity'=>'38000 Grenoble'
                    ),
                    'description'=>'test',
                    //ONLY ADMIN
                    //'identityDocument'=>array(
                    //    'file'=>$file
                    //)

                )
            ),
            [],
            ['Content-Type' => 'multipart/formdata']
        );

        $response = $this->client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            $this->assertSerializedEntityContent($responseData,'user');
            $this->assertNotNull($responseData['id']);
        }

    }

    public function provideDataForEditProfile()
    {
        return array(
            'email already in use'=>array('comblant_michel','labonnepioche@test.fr',true,Response::HTTP_BAD_REQUEST),
            'invalid email : no @'=>array('comblant_michel','test.com',true,Response::HTTP_BAD_REQUEST),
            'invalid email : not enough characters'=>array('comblant_michel','test@t.c',true,Response::HTTP_BAD_REQUEST),
            'valid, no document file'=>array('comblant_michel','newuser@test.fr',false,Response::HTTP_OK),
            'valid registration'=>array('comblant_michel','newuser@test.fr',true,Response::HTTP_OK),
        );
    }

}
