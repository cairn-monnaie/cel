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
     *Test the API key security for POST beneficiaries URL
     *
     *@dataProvider provideDataForApiSecure
     */
    public function testApiSecureAction($beneficiaryValue,$isValidFormat,$isValidKey, $httpResponse,$expectedMessage)
    {
        $this->mobileLogin('gjanssens','@@bbccdd');

        $uri = '/mobile/beneficiaries';
        $formSubmit = array('cairn_user'=> $beneficiaryValue);

        $authKey = ($isValidFormat) ? $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$formSubmit) : 'ABCDE';
        $finalKey = ($isValidKey) ? $authKey : $authKey.'0';

        $crawler = $this->client->request(
            'POST',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $finalKey
            ],
            json_encode($formSubmit)
        );


        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(),true);

        $this->assertEquals($httpResponse, $response->getStatusCode());

        
        if(! $this->isSuccessfulResponse($response)){
            $this->assertTrue($this->errorContains($responseData['errors'],$expectedMessage));
        }

    }

    public function provideDataForApiSecure()
    {
        return array(
            'valid data + valid Auth'=>array('labonnepioche@test.fr',true,true,Response::HTTP_CREATED,'added'),
            'valid data + wrong key format'=>array('noire_aliss@test.fr',false,false,Response::HTTP_UNAUTHORIZED,'api_signature_format'),
            'valid data + wrong key value'=>array('noire_aliss@test.fr',true,false,Response::HTTP_UNAUTHORIZED,'wrong_auth_header')
        );
    }


    /**
     *
     *@dataProvider provideDataForSmsData
     */
    public function testGetPhonesAction($username, $isEmpty, $httpResponse)
    {
        $this->mobileLogin($username,'@@bbccdd');

        $uri = '/mobile/phones';
        $crawler = $this->client->request(
            'GET',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'GET',$uri)
            ]
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            $responseData = $responseData['data'];
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
    public function testGetUsersAction($login,$doesLogin,$formSubmit, $httpResponse, $nbUsers)
    {
        if($doesLogin){
            $this->mobileLogin($login,'@@bbccdd');
            $uri = '/mobile/users';
        }else{
            $uri = '/mapUsers';
        }


        $crawler = $this->client->request(
            'POST',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$formSubmit)
            ],
            json_encode($formSubmit)
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];

            $this->assertEquals($nbUsers,count($responseData));
            foreach($responseData as $user){
                $this->assertSerializedEntityContent($user,'user');
            }
        }
    }

    public function provideDataForUsers()
    {
        $limit = 20;
        $limitPersons = 14;
        $baseSubmit = array(
            'limit'=>$limit,
            'offset'=>0,
            'orderBy'=> array('key'=>'name','order'=>'ASC') ,
            'name'=>'',
            'roles'=>array('0'=>'ROLE_PRO','1'=>'ROLE_PERSON'),
            'bounding_box'=>array('minLon'=>'','maxLon'=>'','minLat'=>'','maxLat'=>''),
            'payment_context'=>true
        );


        return array(
            'offset too high' =>array('gjanssens',true, array_replace($baseSubmit,array('offset'=>250)),Response::HTTP_OK,0),
            'invalid offset' =>array('gjanssens',true,array_replace($baseSubmit,array('offset'=>-5)),Response::HTTP_OK,20),
            'invalid limit' =>array('gjanssens',true,array_replace($baseSubmit,array('limit'=>-5)),Response::HTTP_OK,5),
            'order by creationDate' =>array('gjanssens',true,array_replace_recursive($baseSubmit,array('orderBy'=>array('key'=>'creationDate'))),
            Response::HTTP_OK,$limit),
            'base request with login' => array('gjanssens',true,$baseSubmit,Response::HTTP_OK,$limit),
            'precise pro name' =>array('gjanssens',true,array_replace($baseSubmit,array('name'=>'maltobar')),Response::HTTP_OK,1),
            'precise person name' =>array('gjanssens',true,array_replace($baseSubmit,array('name'=>'alberto_malik@test.fr')),Response::HTTP_OK,1),
            'unprecise name' =>array('gjanssens',true,array_replace($baseSubmit,array('name'=>'test')),Response::HTTP_OK,$limit),
            'empty bounding box' =>array('gjanssens',true,array_replace($baseSubmit,array('bounding_box'=>[])),Response::HTTP_OK,$limit),
            'inconsistent bounding box data' =>array('gjanssens',true,
            array_replace_recursive($baseSubmit,array('bounding_box'=>['minLon'=>'1','maxLon'=>'2','minLat'=>'1','maxLat'=>'2'])),
            Response::HTTP_OK,0),
            'valid bounding box data' =>array('gjanssens',true,
            array_replace_recursive($baseSubmit,array('bounding_box'=>['minLon'=>'4','maxLon'=>'6','minLat'=>'44','maxLat'=>'46'])),
            Response::HTTP_OK,
            $limit),
            'base request without login, pros & persons' => array('',false,$baseSubmit,Response::HTTP_OK,$limit),
            'base request without login, persons only' => array('',false,array_replace($baseSubmit, array('roles'=>['0'=>'ROLE_PERSON'])),Response::HTTP_OK,$limit),

            'base request as an admin, pro & persons' => array('admin_network',true,$baseSubmit,Response::HTTP_OK,$limit),
            'base request as an admin, persons only,payment_context' => array('admin_network',true,array_replace($baseSubmit, 
                                                                array('roles'=> ['0'=>'ROLE_PERSON'])),Response::HTTP_OK,$limitPersons),
            'base request as an admin, persons only,no payment_context' => array('admin_network',true,array_replace($baseSubmit, 
                                                                array('roles'=> ['0'=>'ROLE_PERSON'],'payment_context'=>false)),Response::HTTP_OK,0),
            'base request as a person, persons only' => array('gjanssens',true,array_replace($baseSubmit, 
                                                                array('roles'=> ['0'=>'ROLE_PERSON'])),Response::HTTP_OK,$limit),
            'base request as a pro, persons only' => array('maltobar',true,array_replace($baseSubmit, array('roles'=> ['0'=>'ROLE_PERSON'])),Response::HTTP_OK,$limit),


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

        $uri = '/mobile/users/'.$targetUser->getID();
        $crawler = $this->client->request(
            'GET',
            $uri,
            [],
            [],
            [
                'Content-Type' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'GET',$uri)
            ]
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];

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

        $uri = '/mobile/beneficiaries';
        $crawler = $this->client->request(
            'GET',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'GET',$uri)
            ]
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];

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

        $uri = '/mobile/beneficiaries';
        $formSubmit = array('cairn_user'=>$beneficiaryValue);

        $key = $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$formSubmit);

        $crawler = $this->client->request(
            'POST',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $key
            ],
            json_encode($formSubmit)
        );


        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];
            $this->assertSerializedEntityContent($responseData,'beneficiary');
        }
    }

    public function provideDataForAddBeneficiary()
    {
        return array(
            'self beneficiary'=> array('vie_integrative','vie_integrative@test.fr',Response::HTTP_OK),
            'user not found'=> array('vie_integrative','malt@cairn-monnaie.fr',Response::HTTP_OK),
            'ICC not found'=>array('vie_integrative', '123456789',Response::HTTP_OK),
            'pro adds pro'=>array('vie_integrative','alter_mag@test.fr',Response::HTTP_CREATED),
            'already benef'=>array('nico_faus_prod','labonnepioche@test.fr',Response::HTTP_OK),
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

        $body = array('save'=> "");

        $uri = '/mobile/beneficiaries/'.$ICC;
        $crawler = $this->client->request(
            'DELETE',
            $uri,
            [],
            [],
            [ 
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'DELETE',$uri,$body)
            ],
            json_encode($body)

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
            'invalid : target not beneficiary of user'=> array('labonnepioche','ferme_bressot',Response::HTTP_OK),
        );
    }

    /**
     *
     *@dataProvider provideDataForRemoteAddPhone
     */
    public function testRemoteAddPhone($current,$target, $newPhoneSubmit, $httpPhoneStatusCode,$code,$httpValidationStatusCode)
    {
        $this->mobileLogin($current,'@@bbccdd');

        $formSubmit = array('phoneNumber'=> $newPhoneSubmit['phoneNumber'], 'paymentEnabled'=> 'true');

        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($target);
        $uri = '/mobile/phones/add/'.$targetUser->getID();

        $crawler = $this->client->request(
            'POST',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$formSubmit)
            ],
            json_encode($formSubmit)
        );


        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpPhoneStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){

            $formSubmit = ['activationCode'=> $code];
            $crawler = $this->client->request(
                'POST',
                $uri,
                [],
                [],
                [
                    'CONTENT-TYPE' => 'application/json',
                    'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$formSubmit)
                ],
                json_encode($formSubmit)
            );

            $response = $this->client->getResponse();

            $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
            $this->assertJson($response->getContent());
            $this->assertEquals($httpValidationStatusCode, $response->getStatusCode());

            $responseData = json_decode($response->getContent(),true);

            if($this->isSuccessfulResponse($response)){
                $responseData = $responseData['data'];

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
        $baseData = array('current'=>'stuart_andrew','target'=>'stuart_andrew',
            'newPhone'=>array('phoneNumber'=>'+33699999999','paymentEnabled'=>true),
            'httpPhoneStatusCode'=>Response::HTTP_OK,
            'code'=>'1111',
            'httpValidationStatusCode'=>Response::HTTP_CREATED,
        );

        $baseAdminData = array('current'=>$admin,'target'=>'hirundo_archi',
            'newPhone'=>array('phoneNumber'=>'+33699999999','paymentEnabled'=>true,'identifier'=>'IDSMS'),
            'httpPhoneStatusCode'=>Response::HTTP_OK,
            'code'=>'1111',
            'httpValidationStatusCode'=>Response::HTTP_CREATED,
        );


        $validDataMsg = 'Un code vous a été envoyé';
        $validCodeMsg = 'enregistré';
        $usedMsg = 'déjà utilisé';
        return array(
            'admin not referent' => array_replace($baseAdminData, array('target'=>'stuart_andrew','httpPhoneStatusCode'=>Response::HTTP_FORBIDDEN)),

            'admin is referent' => array_replace($baseAdminData, array('target'=>'hirundo_archi')),

            'user not referent' => array_replace($baseData, array('current'=>'mazmax', 'isExpectedForm'=>false,'httpPhoneStatusCode'=>Response::HTTP_FORBIDDEN)),

            'too many requests'=>array_replace($baseData, array('current'=>'crabe_arnold','target'=>'crabe_arnold','httpPhoneStatusCode'=>Response::HTTP_OK)),

            'current number'=>array_replace_recursive($baseData, array(
                'newPhone'=>array('phoneNumber'=>'+33743434343'),'httpPhoneStatusCode'=>Response::HTTP_OK)
            ),

            'current number, disable sms'=>array_replace_recursive($baseData, array(
                'newPhone'=>array('phoneNumber'=>'+33743434343'),'httpPhoneStatusCode'=>Response::HTTP_OK)
            ),

            'used by pro & person'=>array_replace_recursive($baseData, array(
                'newPhone'=>array('phoneNumber'=>'+33612345678'),'httpPhoneStatusCode'=>Response::HTTP_OK
            )),

            'pro request : used by pro'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
            'newPhone'=>array('phoneNumber'=>'+33612345678'), 'httpPhoneStatusCode'=>Response::HTTP_OK
        )),

            'person request : used by person'=>array_replace_recursive($baseData, array(
                'newPhone'=>array('phoneNumber'=>'+33612345678'),  'httpPhoneStatusCode'=>Response::HTTP_OK
            )),

            'pro request : used by person'=>array_replace_recursive($baseData,array('current'=>'maltobar','target'=>'maltobar',
            'newPhone'=>array('phoneNumber'=>'+33644332211')
        )),

            'person request : used by pro'=>array_replace_recursive($baseData, array('current'=>'benoit_perso','target'=>'benoit_perso',
            'newPhone'=>array('phoneNumber'=>'+33611223344')
        )),

            'last remaining try : valid code'=>array_replace($baseData, array('current'=>'hirundo_archi','target'=>'hirundo_archi',
        )),

            'last remaining try : wrong code'=>array_replace($baseData, array('current'=>'hirundo_archi','target'=>'hirundo_archi',
            'httpValidationStatusCode'=>Response::HTTP_OK, 'code'=>'2222'
        )),

            'user with no phone number'=>array_replace($baseData, array('current'=>'noire_aliss','target'=>'noire_aliss'
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

        $uri = '/mobile/phones/'.$phones[0]->getID();

        $crawler = $this->client->request(
            'POST',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$newPhoneSubmit)
            ],
            json_encode($newPhoneSubmit)
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpPhoneStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];

            if(!$isNewPhoneNumber){
                $this->assertSerializedEntityContent($responseData,'phone');
                return;
            }

            $body = ['activationCode'=> $code];
            $crawler = $this->client->request(
                'POST',
                $responseData['validation_url'],
                [],
                [],
                [
                    'CONTENT-TYPE' => 'application/json',
                    'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$responseData['validation_url'],$body)
                ],
                json_encode($body)
            );

            $response = $this->client->getResponse();

            $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
            $this->assertJson($response->getContent());
            $this->assertEquals($httpValidationStatusCode, $response->getStatusCode());

            $responseData = json_decode($response->getContent(),true);

            if($this->isSuccessfulResponse($response)){
                $responseData = $responseData['data'];

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
            'newPhone'=>array('phoneNumber'=>'+33699999999','paymentEnabled'=>true,'identifier'=>'IDSMS'),
            'isPhoneNumberEdit'=>true,
            'httpPhoneStatusCode'=>Response::HTTP_OK,
            'code'=>'1111',
            'httpValidationStatusCode'=>Response::HTTP_CREATED,
        );

        return array(
            'not referent'=>array_replace($baseData, array('current'=>$admin,'target'=>'stuart_andrew', 'httpPhoneStatusCode'=>Response::HTTP_FORBIDDEN)),

            'admin as ref changes a pro number' => array_replace($baseAdminData, array('target'=>'la_mandragore')),

            'user not referent' => array_replace($baseData, array('current'=>'mazmax', 'httpPhoneStatusCode'=>Response::HTTP_FORBIDDEN)),


            'too many requests'=>array_replace($baseData, array('current'=>'crabe_arnold','target'=>'crabe_arnold', 'httpPhoneStatusCode'=>Response::HTTP_OK)),

            'current number'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar','isPhoneNumberEdit'=>false,
                'newPhone'=>array('phoneNumber'=>'+33611223344'))),

            'invalid number'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
                'newPhone'=>array('phoneNumber'=>'+33911223344'), 'httpPhoneStatusCode'=>Response::HTTP_OK)),

            'admin enables sms'=>array_replace_recursive($baseAdminData, array('target'=>'la_mandragore','isPhoneNumberEdit'=>false,
            'newPhone'=>array('phoneNumber'=>'+33744444444'))),

            'new number'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar')),

            'used by pro & person'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
            'newPhone'=>array('phoneNumber'=>'+33612345678'),'httpPhoneStatusCode'=>Response::HTTP_OK)),

            'pro request : used by pro'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
            'newPhone'=>array('phoneNumber'=>'+33612345678'),'httpPhoneStatusCode'=>Response::HTTP_OK)),

            'person request : used by person'=>array_replace_recursive($baseData, array('current'=>'benoit_perso','target'=>'benoit_perso',
            'newPhone'=>array('phoneNumber'=>'+33612345678'),'httpPhoneStatusCode'=>Response::HTTP_OK)),

            'pro request : used by person'=>array_replace_recursive($baseData,array('current'=>'maltobar','target'=>'maltobar',
            'newPhone'=>array('phoneNumber'=>'+33644332211'))),

            'person request : used by pro'=>array_replace_recursive($baseData, array('current'=>'benoit_perso','target'=>'benoit_perso',
            'newPhone'=>array('phoneNumber'=>'+33611223344'))),

            'last remaining try : valid code'=>array_replace($baseData, array('current'=>'hirundo_archi','target'=>'hirundo_archi')),

            'last remaining try : wrong code'=>array_replace($baseData, array('current'=>'hirundo_archi','target'=>'hirundo_archi',
            'code'=>'2222','httpValidationStatusCode'=>Response::HTTP_OK)),

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
        $uri = '/mobile/phones/'.$targetUser->getPhones()[0]->getID();

        $crawler = $this->client->request(
            'DELETE',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'DELETE',$uri)
            ],
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
            'invalid referent admin'=>array('admin_network','stuart_andrew',Response::HTTP_FORBIDDEN),
            'invalid referent'=>array('gjanssens','nico_faus_prod',Response::HTTP_FORBIDDEN),
        );
    }

    private function atomicRemotePaymentCreation($debitor,$formSubmit,$httpStatusCode)
    {
        $this->mobileLogin($debitor,'@@bbccdd');

        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($debitor);

        $uri ='/mobile/payment/request' ;
        $form =  array_merge(array('fromAccount'=>$debitorUser->getMainICC()), $formSubmit);

        $crawler = $this->client->request(
            'POST',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$form)
            ],
            json_encode($form)
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        return $response;
    }

    private function atomicRemotePaymentValidation($debitor,$responseData)
    {
        $this->mobileLogin($debitor,'@@bbccdd');

        $uri ='/mobile/transaction/confirm/'.$responseData['operation']['id'] ;
        //$form = array("confirmationCode"=> '1111','save'=>"");
        $form = array('save'=>"");

        $crawler = $this->client->request(
            'POST',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$form)
            ],
            json_encode($form)
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true)['data'];

        $this->assertNotNull($responseData['paymentID']);
        $this->assertSerializedEntityContent($responseData,'operation');
    }

    /**
     *
     *@dataProvider provideDataForRemotePayment
     */
    public function testRemotePayment($debitor, $formSubmit,$needsValidation,$isSuspicious, $httpStatusCode)
    {
        $response = $this->atomicRemotePaymentCreation($debitor,$formSubmit,$httpStatusCode);
        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];
            $this->assertSerializedEntityContent($responseData['operation'],'operation');
            $this->assertNull($responseData['operation']['paymentID']);

            if($needsValidation){
                $this->assertTrue($responseData['secure_validation']);
            }else{
                $this->assertFalse($responseData['secure_validation']);
            }
            $this->atomicRemotePaymentValidation($debitor,$responseData);

        }else{
            if($isSuspicious){
                $this->assertTrue($this->errorContains($responseData['messages'],'threshold'));
            }
        }
    }

    public function provideDataForRemotePayment()
    {
        $now = new \Datetime();
        $nowFormat = date('Y-m-d');
        $later = $now->modify('+2 days')->format('Y-m-d');
        $inconsistent = $now->modify('+4 years')->format('Y-m-d');

        $timestampNow = 1000*time();
        $timestampAfter = 1000*strtotime($later);
        $timestampBefore = 1000*strtotime('1990-07-01');
        $timestampInconsistent = 1000*strtotime($inconsistent);

        $uniqueAmount = $this->container->getParameter('mobile_daily_thresholds')['amount']['unique'];
        $maxAmount = $this->container->getParameter('mobile_daily_thresholds')['amount']['block'];

        $validLogin = 'benoit_perso';
        $baseSubmit = array(
            'toAccount'=>'labonnepioche@test.fr',
            'amount'=>$uniqueAmount - 1,
            'reason'=>'Test reason',
            'description'=>'Test description',
            'executionDate'=> $timestampNow,
        );


        return array(
            'invalid amount too low'=>array($validLogin,array_replace($baseSubmit, array('amount'=>0.0001)),false,false,Response::HTTP_OK),
            'invalid negative amount'=>array($validLogin,array_replace($baseSubmit, array('amount'=>-5)),false,false,Response::HTTP_OK),
            'invalid insufficient balance'=>array($validLogin,array_replace($baseSubmit, array('amount'=>1000000000)),false,false,Response::HTTP_OK),
            'invalid : identical creditor & debitor'=>array($validLogin,array_replace($baseSubmit, 
                array('toAccount'=>$validLogin.'@test.fr')),false,false,Response::HTTP_FORBIDDEN),
            'invalid : no creditor data'=>array($validLogin,array_replace($baseSubmit, array('toAccount'=>'')),false,false,Response::HTTP_OK),
            //'invalid : no phone number associated'=>array('gjanssens',$baseSubmit,Response::HTTP_UNAUTHORIZED),
            'valid now'=>array($validLogin,$baseSubmit,false,false,Response::HTTP_CREATED),
            'valid now : user has no card'=>array('episol',$baseSubmit,false,false,Response::HTTP_CREATED),

            'valid now + validation amount'=>array($validLogin,array_replace($baseSubmit, 
                array('amount'=>$uniqueAmount + 1)),true,false, Response::HTTP_CREATED),
            'invalid suspicious amount'=>array($validLogin,array_replace($baseSubmit, 
                array('amount'=>$maxAmount + 1)),false,true, Response::HTTP_OK),
            'invalid execution date format'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$later)),false,false,Response::HTTP_BAD_REQUEST),
            //should not work because mobile app payment is instantaneous, no scheduled payment
            //'valid after'=>array($validLogin,array_replace($baseSubmit, 
            //    array('executionDate'=>$timestampAfter)),false,false, Response::HTTP_CREATED),
            'invalid date format'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$nowFormat)),false,false,Response::HTTP_BAD_REQUEST),
            'invalid before'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$timestampBefore)),false,false,Response::HTTP_OK),
            'invalid inconsistent'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$timestampInconsistent)),false,false,Response::HTTP_OK),
        );

    }

    /**
     *
     *@dataProvider provideDataForThresholds
     */
    public function testRemoteThresholds($debitor,$formSubmit,$nbOpsBeforeValidation,$nbOpsBeforeBlock)
    {
        //ALL ops that do not need validation
        for($i = 0; $i < $nbOpsBeforeValidation; $i++){
            $response = $this->atomicRemotePaymentCreation($debitor,$formSubmit,Response::HTTP_CREATED);
            $responseData = json_decode($response->getContent(),true)['data'];
            $this->assertSerializedEntityContent($responseData['operation'],'operation');
            $this->assertNull($responseData['operation']['paymentID']);
            $this->assertFalse($responseData['secure_validation']);
            $this->atomicRemotePaymentValidation($debitor,$responseData);
        }

        //FROM NOW ON, validation required
        for($j = $nbOpsBeforeValidation; $j < $nbOpsBeforeBlock; $j++){
            $response = $this->atomicRemotePaymentCreation($debitor,$formSubmit,Response::HTTP_CREATED);
            $responseData = json_decode($response->getContent(),true)['data'];
            $this->assertSerializedEntityContent($responseData['operation'],'operation');
            $this->assertNull($responseData['operation']['paymentID']);
            $this->assertTrue($responseData['secure_validation']);
            $this->atomicRemotePaymentValidation($debitor,$responseData);
        }

        $response = $this->atomicRemotePaymentCreation($debitor,$formSubmit,Response::HTTP_OK);
    }

    public function provideDataForThresholds()
    {
        $now = new \Datetime();
        $nowFormat = date('Y-m-d');
        $later = $now->modify('+2 days')->format('Y-m-d');
        $before = $now->modify('-10 days')->format('Y-m-d');
        $inconsistent = $now->modify('+4 years')->format('Y-m-d');

        $timestampNow = 1000*time($nowFormat);
        $timestampAfter = 1000*time($later);

        $uniqueAmount = $this->container->getParameter('mobile_daily_thresholds')['amount']['unique'];
        $maxAmount = $this->container->getParameter('mobile_daily_thresholds')['amount']['block'];

        $stepQty = $this->container->getParameter('mobile_daily_thresholds')['qty']['step'];
        $maxQty = $this->container->getParameter('mobile_daily_thresholds')['qty']['block'];

        $validLogin = 'maltobar';
        $baseSubmit = array(
            'toAccount'=>'labonnepioche@test.fr',
            'amount'=> 1,
            'reason'=>'Test reason',
            'description'=>'Test description',
            'executionDate'=> $timestampNow,
        );

        return array(
            'limit by quantity of payments'=>array($validLogin,$baseSubmit,3,9),
            'limit by cumulated amount 1'=> array($validLogin,array_replace($baseSubmit,array('amount'=>100)),0,4),
            'limit by cumulated amount 2'=>array($validLogin,array_replace($baseSubmit,array('amount'=>60)),0,8),
            'limit by cumulated amount 3'=>array($validLogin,array_replace($baseSubmit,array('amount'=>10)),3,9),
            'limit by cumulated amount 4'=>array($validLogin,array_replace($baseSubmit,array('amount'=>5)),3,9),
            'limit by max amount'=> array($validLogin,array_replace($baseSubmit,array('amount'=>1500)),0,0)
        );
    }


    /**
     *
     *@dataProvider provideDataForAccountOperations
     */
    public function testGetAccountOperations($current,$target,$formSubmit,$httpStatusCode,$nbUsers)
    {
        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($target);

        //connect to Cyclos to get account ID of the target
        $credentials = array('username'=>$target,'password'=>'@@bbccdd');
        $network = $this->container->getParameter('cyclos_currency_cairn');
        $group = $this->container->getParameter('cyclos_group_network_admins');
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($network,'login',$credentials);

        $accounts = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($targetUser->getCyclosID());

        $this->mobileLogin($current,'@@bbccdd');

        $uri = '/mobile/account/operations/'.$accounts[0]->id;

        $crawler = $this->client->request(
            'POST',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$formSubmit)
            ],
            json_encode($formSubmit)
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];

            $this->assertEquals($nbUsers,count($responseData));

            foreach($responseData as $operation){
                $this->assertSerializedEntityContent($operation,'operation');
                $this->assertNotNull($operation['paymentID']);
            }
        }
    }

    public function provideDataForAccountOperations()
    {
        $now = new \Datetime();
        $beginDate =$now->modify('-4 months')->format('Y-m-d'); 
        $endDate = date('Y-m-d'); 

        $baseSubmit = array(
            "limit"=>20,
            "offset"=>0,
            "begin"=> $beginDate,
            "end"=> $endDate,
            "minAmount"=> "",
            "maxAmount"=> "",
            "keywords"=> "",
            "types"=> [],
            "orderBy"=> "ASC"
        );

        //array_replace($baseSubmit,array())
        return array(
            'invalid adherent to adherent'=>array('gjanssens','labonnepioche',$baseSubmit,Response::HTTP_NOT_FOUND,0),
            'invalid admin referent to adherent'=>array('gl_grenoble','episol',$baseSubmit,Response::HTTP_FORBIDDEN,0),
            'valid super admin referent to adherent'=>array('admin_network','gjanssens',$baseSubmit,Response::HTTP_OK,1),
            'valid super admin to self'=>array('admin_network','admin_network',$baseSubmit,Response::HTTP_OK,0),
            'valid pro to self'=>array('nico_faus_prod','nico_faus_prod',$baseSubmit,Response::HTTP_OK,1),
            'valid person to self'=>array('gjanssens','gjanssens',$baseSubmit,Response::HTTP_OK,1),
            'valid deposits'=>array('gjanssens','gjanssens',array_replace_recursive($baseSubmit,array('types'=>array('DEPOSIT'))),Response::HTTP_OK,1),
            'invalid offset'=>array('gjanssens','gjanssens',array_replace_recursive($baseSubmit,array('offset'=>150)),Response::HTTP_OK,0),
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

        $uri = '/mobile/operations/'.$operations[0]->getPaymentID();
        $crawler = $this->client->request(
            'GET',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'GET',$uri)
            ]
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];

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

        $uri = '/mobile/accounts.json';
        $crawler = $this->client->request(
            'GET',
            $uri,
            [],
            [],
            [
                'CONTENT-TYPE' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'GET',$uri)
            ]
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];

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
     *@dataProvider provideDataForCreateUser
     */
    public function testRemoteCreateUser($login,$formSubmit,$type,$hasUploadedFile,$httpStatusCode)
    {
        if($login){
            $this->mobileLogin($login,'@@bbccdd');
        }
        $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
        $originalName = 'john-doe-id.png';                                 
        $absolutePath = $absoluteWebDir.$originalName;

        if($hasUploadedFile){
            $copyPath1 =$absoluteWebDir.rand(1000,10000).'.png'; 
            //copy 
            if(! copy($absolutePath,$copyPath1 )){
                echo "Failed to copy";
                return;
            }
            $file1 = new UploadedFile($copyPath1, $originalName, 'image/png',123);
            $files = [
                'fos_user_registration_form'=> [
                    'identityDocument'=> ['file'=>$file1]
                ]
            ];
        }else{
            $files = [];
        }


        $uri = '/mobile/users/registration?type='.$type;

        $crawler = $this->client->request(
            'POST',
            $uri,
            ['fos_user_registration_form' => $formSubmit],
            $files,
            [
                'Content-Type' => 'multipart/formdata',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri)
            ]
        );

        $response = $this->client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];
            $this->assertSerializedEntityContent($responseData,'user');
            $this->assertNull($responseData['id']);

            if($type == 'pro'){
                $this->assertContains('ROLE_PRO',array_values($responseData['roles']));
                $this->assertNotContains('ROLE_PERSON',array_values($responseData['roles']));
            }else{
                $this->assertContains('ROLE_PERSON',array_values($responseData['roles']));
                $this->assertNotContains('ROLE_PRO',array_values($responseData['roles']));
            }
        }else{

            if($hasUploadedFile){
                unlink($copyPath1);
            }

        }
    }

    public function provideDataForCreateUser()
    {
        $formSubmit =[
            'email'=>'newuser@test.fr',
            'name'=>"New User",
            'address'=>array(
                'street1'=>'7 rue Très Cloitres',
                'street2'=>'',
                'zipCity'=>'38000 Grenoble'
            ),
            'excerpt'=>'test excerpt'
        ];

        return array(
            'logged in as adherent'=>array('gjanssens',$formSubmit,'person',true,Response::HTTP_FORBIDDEN),
            'logged in as pro'=>array('nico_faus_prod',$formSubmit,'person',true,Response::HTTP_FORBIDDEN),
            'logged in as admin'=>array('admin_network',$formSubmit,'person',true,Response::HTTP_CREATED),
            'create pro'=>array('',$formSubmit,'pro',true,Response::HTTP_CREATED),
            'invalid address'=>['',array_replace_recursive($formSubmit, ['address'=>['street1'=>'7']]),'pro',true,Response::HTTP_OK],
            'email already in use'=>array('',array_replace($formSubmit,['email'=>'labonnepioche@test.fr']),'pro',true,Response::HTTP_OK),
            'invalid email: no @'=>array('',array_replace($formSubmit,['email'=>'test.com']),'person',true,Response::HTTP_OK),
            'invalid email : not enough characters'=>array('',array_replace($formSubmit,['email'=>'test@t.c']),'person',true,Response::HTTP_OK),
            'no document file'=>array('',$formSubmit,'pro',false,Response::HTTP_OK),
            'create person'=>array('',$formSubmit,'person',true,Response::HTTP_CREATED),
        );
    }


    /**
     *
     *@dataProvider provideDataForEditProfile
     */
    public function testRemoteEditProfile($login,$target,$formSubmit,$hasUploadedIdDoc,$hasUploadedLogo,$httpStatusCode)
    {
        $this->mobileLogin($login,'@@bbccdd');

        $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
        $originalName = 'john-doe-id.png';                                 
        $absolutePath = $absoluteWebDir.$originalName;

        if($hasUploadedIdDoc){
            $copyPath1 =$absoluteWebDir.rand(1000,10000).'.png'; 
            //copy 
            if(! copy($absolutePath,$copyPath1 )){
                echo "Failed to copy";
                return;
            }
            $file1 = new UploadedFile($copyPath1, $originalName, 'image/png',123);
            $files = [
                'cairn_user_profile_edit'=> [
                    'identityDocument'=> ['file'=>$file1]
                ]
            ];
        }else{
            $files = [
                'cairn_user_profile_edit'=> []
            ];
        }

        if($hasUploadedLogo){
            $copyPath2 =$absoluteWebDir.rand(1000,10000).'.png'; 
            //copy 
            if(! copy($absolutePath,$copyPath2 )){
                echo "Failed to copy";
                return;
            }
            $file2 = new UploadedFile($copyPath2, $originalName, 'image/png',123);
            $files['cairn_user_profile_edit']['image'] =  ['file'=>$file2];

        }

        $targetUser = $this->em->getRepository('CairnUserBundle:User')->findOneByUsername($target);

        $uri = '/mobile/users/profile/'.$targetUser->getID();

        $crawler = $this->client->request(
            'POST',
            $uri,
            ['cairn_user_profile_edit' => $formSubmit],
            $files,
            [
                'Content-Type' => 'multipart/formdata',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri)
            ]
        );

        $response = $this->client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $responseData = $responseData['data'];
            $this->assertSerializedEntityContent($responseData,'user');
            $this->assertNotNull($responseData['id']);
        }else{
            if($hasUploadedIdDoc){
                unlink($copyPath1);
            }
            if($hasUploadedLogo){
                unlink($copyPath2);
            }

        }
    }

    public function provideDataForEditProfile()
    {
        $person = 'stuart_andrew';
        $pro = 'mon_vrac';

        return array(
            'adherent for himself'=>array($person,$person,$this->getNewSubmit(false),false,false,Response::HTTP_CREATED),
            'adherent for someone else'=>array($person,'gjanssens',$this->getNewSubmit(false),false,false,Response::HTTP_FORBIDDEN),
            'admin is not referent'=>array('admin_network',$person,$this->getNewSubmit(true),false,false,Response::HTTP_FORBIDDEN),
            'admin is person referent, no id doc upload'=>array('admin_network','noire_aliss',$this->getNewSubmit(true),false,false,Response::HTTP_CREATED),
            'admin is person referent, id doc uploaded'=>array('admin_network','noire_aliss',$this->getNewSubmit(true),true,false,Response::HTTP_CREATED),
            'logged in as admin for pro'=>array('admin_network',$pro,$this->getNewSubmit(true),false,true,Response::HTTP_CREATED),
            'pro for himself with logo'=>array($pro,$pro,$this->getNewSubmit(false),false,false,Response::HTTP_CREATED),
            'invalid pro for himself with id doc'=>array($pro,$pro,$this->getNewSubmit(false),true,true,Response::HTTP_OK),
            'invalid address'=>[$pro,$pro,array_replace_recursive($this->getNewSubmit(false), ['address'=>['street1'=>'7']]),false,true,Response::HTTP_OK],
            'email already in use'=>array($pro,$pro,array_replace($this->getNewSubmit(false),['email'=>'labonnepioche@test.fr']),false,true,Response::HTTP_OK),
            'invalid email: no @'=>array($pro,$pro,array_replace($this->getNewSubmit(false),['email'=>'test.com']),false,true,Response::HTTP_OK),
            'invalid email : not enough characters'=>array($pro,$pro,array_replace($this->getNewSubmit(false),['email'=>'test@t.c']),
            false,true,Response::HTTP_OK),
            'person tries to add logo'=>array($person,$person,$this->getNewSubmit(false),false,true,Response::HTTP_OK),
            'person tries to add id doc'=>array($person,$person,$this->getNewSubmit(false),true,false,Response::HTTP_OK),
        );
    }

    private function getNewSubmit($isAdmin){
        $id = rand(1,99999);
        $username = 'newprofileuser'.$id;
        $name = "New User".$id;

        $formSubmit =[
            'email'=>$username.'@test.fr',
            'address'=>array(
                'street1'=>'7 rue Très Cloitres',
                'street2'=>'',
                'zipCity'=>'38000 Grenoble'
            ),
            'excerpt'=>'test excerpt'
        ];

        if($isAdmin){
            $formSubmit['username'] = $username;
            $formSubmit['name'] = $name;
        }
        return $formSubmit;
    }

    /**
     *
     *@dataProvider provideDataForChangePassword
     */
    public function testRemoteChangePassword($login,$currentPwd,$newPwd,$confirmPwd,$httpStatusCode)
    {
        $this->mobileLogin($login,'@@bbccdd');

        $uri = '/mobile/users/change-password';

        $formSubmit = [ 
            'current_password'=> $currentPwd,
            'plainPassword'=> [
                'first' => $newPwd,
                'second' => $confirmPwd
            ]
        ];

        $crawler = $this->client->request(
            'POST',
            $uri,
            $formSubmit,
            [],
            [
                'Content-Type' => 'application/json',
                'HTTP_Authorization' => $this->generateApiAuthorizationHeader(time(date('Y-m-d')),'POST',$uri,$formSubmit)
            ],
            json_encode($formSubmit)
        );

        $response = $this->client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($this->isSuccessfulResponse($response)){
            $this->mobileLogin($login,$newPwd);
        }
    }

    public function provideDataForChangePassword()
    {
        $currentPwd = '@@bbccdd';
        $newBasePwd = 'bcdefgh';

        return array(
            'invalid current password'=> array('denis_ketels','@bcdef','@'.$newBasePwd,'@'.$newBasePwd,Response::HTTP_OK),
            'new != confirm'=> array('denis_ketels',$currentPwd,'@'.$newBasePwd,'<'.$newBasePwd,Response::HTTP_OK),
            'invalid : too short new'=> array('denis_ketels',$currentPwd,'@bcde','@bcde',Response::HTTP_OK),
            'invalid: no special chars'=> array('denis_ketels',$currentPwd,'a'.$newBasePwd,'a'.$newBasePwd,Response::HTTP_OK),
            'pseudo included in pwd'=> array('denis_ketels',$currentPwd,'@denis_ketels@','@denis_ketels@',Response::HTTP_OK),
            'invalid é' =>  array('denis_ketels',$currentPwd,'é'.$newBasePwd,'é'.$newBasePwd,Response::HTTP_OK),
            'invalid é' =>  array('denis_ketels',$currentPwd,'ä'.$newBasePwd,'ä'.$newBasePwd,Response::HTTP_OK),
            'invalid ù' =>  array('denis_ketels',$currentPwd,'ù'.$newBasePwd,'ù'.$newBasePwd,Response::HTTP_OK),
            'invalid §' =>  array('denis_ketels',$currentPwd,'§'.$newBasePwd,'§'.$newBasePwd,Response::HTTP_OK),

            'first login change pwd'=> array('Claire_Dode',$currentPwd,$currentPwd,$currentPwd,Response::HTTP_CREATED),
            'new = current'=> array('denis_ketels',$currentPwd,$currentPwd,$currentPwd,Response::HTTP_CREATED),
            'valid classic'=> array('denis_ketels',$currentPwd,$currentPwd,$currentPwd,Response::HTTP_CREATED),
            'valid >' =>  array('denis_ketels',$currentPwd,'>'.$newBasePwd,'>'.$newBasePwd,Response::HTTP_CREATED),
            'valid <' =>  array('denis_ketels',$currentPwd,'<'.$newBasePwd,'<'.$newBasePwd,Response::HTTP_CREATED),
            'valid `' =>  array('denis_ketels',$currentPwd,'`'.$newBasePwd,'`'.$newBasePwd,Response::HTTP_CREATED),
            'valid @' =>  array('denis_ketels',$currentPwd,'@'.$newBasePwd,'@'.$newBasePwd,Response::HTTP_CREATED),
            'valid !' =>  array('denis_ketels',$currentPwd,'!'.$newBasePwd,'!'.$newBasePwd,Response::HTTP_CREATED),
            'valid "' =>  array('denis_ketels',$currentPwd,'"'.$newBasePwd,'"'.$newBasePwd,Response::HTTP_CREATED),
            'valid #' =>  array('denis_ketels',$currentPwd,'#'.$newBasePwd,'#'.$newBasePwd,Response::HTTP_CREATED),
            'valid $' =>  array('denis_ketels',$currentPwd,'$'.$newBasePwd,'$'.$newBasePwd,Response::HTTP_CREATED),
            'valid %' =>  array('denis_ketels',$currentPwd,'%'.$newBasePwd,'%'.$newBasePwd,Response::HTTP_CREATED),
            'valid &' =>  array('denis_ketels',$currentPwd,'&'.$newBasePwd,'&'.$newBasePwd,Response::HTTP_CREATED),
            'valid \''=>  array('denis_ketels',$currentPwd,'\''.$newBasePwd,'\''.$newBasePwd,Response::HTTP_CREATED),
            'valid ()'=>  array('denis_ketels',$currentPwd,'('.$newBasePwd.')','('.$newBasePwd.')',Response::HTTP_CREATED),
            'valid {}'=>  array('denis_ketels',$currentPwd,'{'.$newBasePwd.'}','{'.$newBasePwd.'}',Response::HTTP_CREATED),
            'valid []'=>  array('denis_ketels',$currentPwd,'['.$newBasePwd.']','['.$newBasePwd.']',Response::HTTP_CREATED),
            'valid *' =>  array('denis_ketels',$currentPwd,'*'.$newBasePwd,'*'.$newBasePwd,Response::HTTP_CREATED),
            'valid +' =>  array('denis_ketels',$currentPwd,'+'.$newBasePwd,'+'.$newBasePwd,Response::HTTP_CREATED),
            'valid ,' =>  array('denis_ketels',$currentPwd,','.$newBasePwd,','.$newBasePwd,Response::HTTP_CREATED),
            'valid -' =>  array('denis_ketels',$currentPwd,'-'.$newBasePwd,'-'.$newBasePwd,Response::HTTP_CREATED),
            'valid .' =>  array('denis_ketels',$currentPwd,'.'.$newBasePwd,'.'.$newBasePwd,Response::HTTP_CREATED),
            'valid /' =>  array('denis_ketels',$currentPwd,'/'.$newBasePwd,'/'.$newBasePwd,Response::HTTP_CREATED),
            'valid :' =>  array('denis_ketels',$currentPwd,':'.$newBasePwd,':'.$newBasePwd,Response::HTTP_CREATED),
            'valid ;' =>  array('denis_ketels',$currentPwd,';'.$newBasePwd,';'.$newBasePwd,Response::HTTP_CREATED),
            'valid =' =>  array('denis_ketels',$currentPwd,'='.$newBasePwd,'='.$newBasePwd,Response::HTTP_CREATED),
            'valid ?' =>  array('denis_ketels',$currentPwd,'?'.$newBasePwd,'?'.$newBasePwd,Response::HTTP_CREATED),
            'valid ^' =>  array('denis_ketels',$currentPwd,'^'.$newBasePwd,'^'.$newBasePwd,Response::HTTP_CREATED),
            'valid _' =>  array('denis_ketels',$currentPwd,'_'.$newBasePwd,'_'.$newBasePwd,Response::HTTP_CREATED),
            'valid ~' =>  array('denis_ketels',$currentPwd,'~'.$newBasePwd,'~'.$newBasePwd,Response::HTTP_CREATED),
        );
    }
}
