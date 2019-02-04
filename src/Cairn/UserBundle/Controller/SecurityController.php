<?php 

// src/Cairn/UserBundle/Controller/ApiController.php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cairn\UserCyclosBundle\Entity\LoginManager;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SecurityController extends Controller
{
    public function getTokensAction(Request $request)
    {
        if($request->isMethod('POST')){

            $params = $request->request->all();

            $grantRequest = new Request(array(
                'client_id'  => $params['client_id'],
                'client_secret' => $params['client_secret'],
                'grant_type' => $params['grant_type'],
                'username' => $params['username'],
                'password' => $params['password']
            ));

            $oauth_token_data = $this->get('fos_oauth_server.server')->grantAccessToken($grantRequest);
            $array_oauth = json_decode($oauth_token_data->getContent(), true);

            // ********* get Cyclos Token ****************
            $networkInfo = $this->get('cairn_user_cyclos_network_info');
            $networkName = $this->getParameter('cyclos_currency_cairn');
            $loginManager = new LoginManager();

            $credentials = array('username'=>$params['username'],'password'=> $params['password']);     
            $networkInfo->switchToNetwork($networkName,'login',$credentials);      

            //set cyclos session timeout
            $dto = new \stdClass();                                                
            $dto->amount = $this->getParameter('session_timeout');               
            $dto->field = 'SECONDS';   

            //effectively log in and get session token
            $loginResult = $loginManager->login($dto);                             
//            $array_oauth['cyclos_token'] =  $loginResult->sessionToken;
//
//            $networkInfo->switchToNetwork($networkName,'session_token',$token);

            $response =  new Response(json_encode($array_oauth));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
            throw new NotFoundHttpException('POST Method required !');
        }
    }
}
