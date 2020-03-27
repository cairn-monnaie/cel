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
        $this->assertEquals($httpResponse, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if(! $response->isSuccessful()){
            $this->assertContains($expectedMessage,$responseData[0]['error']);
        }

    }

    public function provideDataForApiSecure()
    {
        return array(
            'valid data + valid Auth'=>array('noire_aliss@test.fr',true,true,Response::HTTP_CREATED,'added'),
            'valid data + wrong key format'=>array('noire_aliss@test.fr',false,false,Response::HTTP_UNAUTHORIZED,'Format'),
            'valid data + wrong key value'=>array('noire_aliss@test.fr',true,false,Response::HTTP_UNAUTHORIZED,'Wrong')
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

        if($response->isSuccessful()){
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
                    'bounding_box'=>array('minLon'=>'','maxLon'=>'','minLat'=>'','maxLat'=>'')
            );


        return array(
            'offset too high' =>array('gjanssens',true, array_replace($baseSubmit,array('offset'=>250)),Response::HTTP_OK,0),
            'invalid offset' =>array('gjanssens',true,array_replace($baseSubmit,array('offset'=>-5)),Response::HTTP_INTERNAL_SERVER_ERROR,0),
            'invalid limit' =>array('gjanssens',true,array_replace($baseSubmit,array('limit'=>-5)),Response::HTTP_INTERNAL_SERVER_ERROR,0),
            'order by creationDate' =>array('gjanssens',true,array_replace_recursive($baseSubmit,array('orderBy'=>array('key'=>'creationDate'))),
                                                                                    Response::HTTP_OK,$limit),
            'base request with login' => array('gjanssens',true,$baseSubmit,Response::HTTP_OK,$limit),
            'precise name' =>array('gjanssens',true,array_replace($baseSubmit,array('name'=>'maltobar')),Response::HTTP_OK,1),
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
            'base request as an admin, persons only' => array('admin_network',true,array_replace($baseSubmit, array('roles'=> ['0'=>'ROLE_PERSON'])),
                                                                                        Response::HTTP_OK,$limitPersons),
            'base request as a person, persons only' => array('gjanssens',true,array_replace($baseSubmit, array('roles'=> ['0'=>'ROLE_PERSON'])),
                                                                                        Response::HTTP_OK,$limit),
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
            'invalid : target not beneficiary of user'=> array('labonnepioche','ferme_bressot',Response::HTTP_BAD_REQUEST),
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

        $uri = '/mobile/phones/'.$targetUser;

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

        if($response->isSuccessful()){

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

            'pro request : used by pro'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
                    'newPhone'=>array('phoneNumber'=>'+33612345678'), 'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                    )),

            'person request : used by person'=>array_replace_recursive($baseData, array(
                    'newPhone'=>array('phoneNumber'=>'+33612345678'),  'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
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
                    'httpValidationStatusCode'=>Response::HTTP_BAD_REQUEST, 'code'=>'2222'
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

        if($response->isSuccessful()){
            if(!$isNewPhoneNumber){
                $this->assertSerializedEntityContent($responseData['phone'],'phone');
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


            'too many requests'=>array_replace($baseData, array('current'=>'crabe_arnold','target'=>'crabe_arnold', 'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST)),

            'current number'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar','isPhoneNumberEdit'=>false,
                                                              'newPhone'=>array('phoneNumber'=>'+33611223344')
                                                    )),

            'invalid number'=>array_replace_recursive($baseData, array('current'=>'maltobar','target'=>'maltobar',
                                                            'newPhone'=>array('phoneNumber'=>'+33911223344'), 'httpPhoneStatusCode'=>Response::HTTP_BAD_REQUEST
                                                    )),

            'admin enables sms'=>array_replace_recursive($baseAdminData, array('target'=>'la_mandragore','isPhoneNumberEdit'=>false,
                                                'newPhone'=>array('phoneNumber'=>'+33744444444')
            )),

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

    /**
     *
     *@dataProvider provideDataForRemotePayment
     */
    public function testRemotePayment($debitor, $formSubmit, $httpStatusCode,$confirmCode)
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

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
            $this->assertSerializedEntityContent($responseData['operation'],'operation');

            $this->assertNull($responseData['operation']['paymentID']);

            $this->mobileLogin($debitor,'@@bbccdd');

            $uri ='/mobile/transaction/confirm/'.$responseData['operation']['id'] ;
            $form = array("confirmationCode"=> $confirmCode,'save'=>"");

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

            if($confirmCode == '1111'){
                $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

                 $responseData = json_decode($response->getContent(),true);

                 $this->assertNotNull($responseData['paymentID']);
                 $this->assertSerializedEntityContent($responseData,'operation');
                 
            }else{
                 $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            }
        }
    }

    public function provideDataForRemotePayment()
    {
        $now = new \Datetime();
        $nowFormat = date('Y-m-d');
        $later = $now->modify('+2 days')->format('Y-m-d');
        $before = $now->modify('-10 days')->format('Y-m-d');
        $inconsistent = $now->modify('+4 years')->format('Y-m-d');

        $timestampNow = 1000*time($nowFormat);
        $timestampAfter = 1000*time($later);

        $validLogin = 'benoit_perso';
        $baseSubmit = array(
                    'toAccount'=>'labonnepioche@test.fr',
                    'amount'=>25,
                    'reason'=>'Test reason',
                    'description'=>'Test description',
                    'executionDate'=> $timestampNow,
            );

        return array(
            'invalid amount too low'=>array($validLogin,array_replace($baseSubmit, array('amount'=>0.0001)),Response::HTTP_BAD_REQUEST,'1111'),
            'invalid negative amount'=>array($validLogin,array_replace($baseSubmit, array('amount'=>-5)),Response::HTTP_BAD_REQUEST,'1111'),
            'invalid insufficient balance'=>array($validLogin,array_replace($baseSubmit, array('amount'=>1000000000)),Response::HTTP_BAD_REQUEST,'1111'),
            'invalid : identical creditor & debitor'=>array($validLogin,array_replace($baseSubmit, 
                                                        array('toAccount'=>$validLogin.'@test.fr')),Response::HTTP_UNAUTHORIZED,'1111'),
            'invalid : no creditor data'=>array($validLogin,array_replace($baseSubmit, array('toAccount'=>'')),Response::HTTP_INTERNAL_SERVER_ERROR,'1111'),
            //'invalid : no phone number associated'=>array('gjanssens',$baseSubmit,Response::HTTP_UNAUTHORIZED,'1111'),
            'valid now'=>array($validLogin,$baseSubmit,Response::HTTP_CREATED,'1111'),
            'invalid confirm code'=>array($validLogin,$baseSubmit,Response::HTTP_CREATED,'2222'),
            'invalid execution date format'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$later)),Response::HTTP_BAD_REQUEST,'1111'),
            'valid after'=>array($validLogin,array_replace($baseSubmit, 
                                    array('executionDate'=>$timestampAfter)), Response::HTTP_CREATED,'1111'),
            'invalid before'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$before)),Response::HTTP_BAD_REQUEST,'1111'),
            'invalid inconsistent'=>array($validLogin,array_replace($baseSubmit, array('executionDate'=>$inconsistent)),Response::HTTP_BAD_REQUEST,'1111'),
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

        if($response->isSuccessful()){
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

        if($response->isSuccessful()){
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
            'description'=>'test'
        ];

        return array(
            'logged in as adherent'=>array('gjanssens',$formSubmit,'person',true,Response::HTTP_UNAUTHORIZED),
            'logged in as pro'=>array('nico_faus_prod',$formSubmit,'person',true,Response::HTTP_UNAUTHORIZED),
            'logged in as admin'=>array('admin_network',$formSubmit,'person',true,Response::HTTP_CREATED),
            'create pro'=>array('',$formSubmit,'pro',true,Response::HTTP_CREATED),
            'invalid address'=>['',array_replace_recursive($formSubmit, ['address'=>['street1'=>'7']]),'pro',true,Response::HTTP_BAD_REQUEST],
            'email already in use'=>array('',array_replace($formSubmit,['email'=>'labonnepioche@test.fr']),'pro',true,Response::HTTP_BAD_REQUEST),
            'invalid email: no @'=>array('',array_replace($formSubmit,['email'=>'test.com']),'person',true,Response::HTTP_BAD_REQUEST),
            'invalid email : not enough characters'=>array('',array_replace($formSubmit,['email'=>'test@t.c']),'person',true,Response::HTTP_BAD_REQUEST),
            'no document file'=>array('',$formSubmit,'pro',false,Response::HTTP_BAD_REQUEST),
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
            $files['cairn_user_profile_edit'][] = ['image' => ['file'=>$file2]];
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
        //var_dump($response);

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(),true);

        if($response->isSuccessful()){
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
        $pro = 'maltobar';

        return array(
            'adherent for himself'=>array($person,$person,$this->getNewSubmit(false),false,false,Response::HTTP_OK),
            'adherent for someone else'=>array($person,'gjanssens',$this->getNewSubmit(false),false,false,Response::HTTP_FORBIDDEN),
            'admin is not referent'=>array('admin_network',$person,$this->getNewSubmit(true),false,false,Response::HTTP_FORBIDDEN),
            'admin is person referent, no id doc upload'=>array('admin_network','noire_aliss',$this->getNewSubmit(true),false,false,Response::HTTP_OK),
            'admin is person referent, id doc uploaded'=>array('admin_network','noire_aliss',$this->getNewSubmit(true),true,false,Response::HTTP_OK),
            'logged in as admin for pro'=>array('admin_network',$pro,$this->getNewSubmit(true),true,true,Response::HTTP_OK),
            'pro for himself with logo'=>array($pro,$pro,$this->getNewSubmit(false),false,true,Response::HTTP_OK),
            'invalid pro for himself with id doc'=>array($pro,$pro,$this->getNewSubmit(false),true,true,Response::HTTP_BAD_REQUEST),
            'invalid address'=>[$pro,$pro,array_replace_recursive($this->getNewSubmit(false), ['address'=>['street1'=>'7']]),false,true,Response::HTTP_BAD_REQUEST],
            'email already in use'=>array($pro,$pro,array_replace($this->getNewSubmit(false),['email'=>'labonnepioche@test.fr']),false,true,Response::HTTP_BAD_REQUEST),
            'invalid email: no @'=>array($pro,$pro,array_replace($this->getNewSubmit(false),['email'=>'test.com']),false,true,Response::HTTP_BAD_REQUEST),
            'invalid email : not enough characters'=>array($pro,$pro,array_replace($this->getNewSubmit(false),['email'=>'test@t.c']),
                                                                    false,true,Response::HTTP_BAD_REQUEST),
            'person tries to add logo'=>array($person,$person,$this->getNewSubmit(false),false,true,Response::HTTP_BAD_REQUEST),
            'person tries to add id doc'=>array($person,$person,$this->getNewSubmit(false),true,false,Response::HTTP_BAD_REQUEST),
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
            'description'=>'test'
        ];

        if($isAdmin){
            $formSubmit['username'] = $username;
            $formSubmit['name'] = $name;
        }
        return $formSubmit;
    }
}
