<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class ApiControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     *
     *
     *@dataProvider provideDataForSmsData
     */
    public function testGetPhonesAction($username, $isEmpty, $httpResponse){

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
        var_dump($ICC);

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
            //'valid removal'=> array('nico_faus_prod','labonnepioche',Response::HTTP_OK),
            //'valid removal 2'=> array('le_marque_page','labonnepioche',Response::HTTP_OK),
            //'invalid : beneficiary does not exist'=> array('nico_faus_prod','comblant_michel',Response::HTTP_BAD_REQUEST),
            'invalid : target not beneficiary of user'=> array('labonnepioche','ferme_bressot',Response::HTTP_BAD_REQUEST),
        );
    }

}
