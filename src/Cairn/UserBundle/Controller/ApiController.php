<?php 

// src/Cairn/UserBundle/Controller/ApiController.php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\OnlinePayment;
use Cairn\UserBundle\Entity\User;


/**
 * This class contains actions related to other applications as webhooks and specific API functions 
 */
class ApiController extends Controller
{

    public function phonesAction(Request $request)
    {
        $user = $this->getUser();
        $phones = $user->getPhones(); 
        $phones = is_array($phones) ? $phones : $phones->getValues();

        return $this->get('cairn_user.api')->getOkResponse($phones,Response::HTTP_OK);
    }

    public function setFirstLoginAction(Request $request)
    {
        $this->getUser()->setFirstLogin(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->get('cairn_user.api')->getOkResponse($this->getUser(),Response::HTTP_OK);
    }

    public function usersAction(Request $request)
    {
        if($request->isMethod('POST')){

            $jsonRequest = json_decode(htmlspecialchars($request->getContent(),ENT_NOQUOTES), true);

            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository(User::class);

            $ub = $userRepo->createQueryBuilder('u')
                ->setMaxResults($jsonRequest['limit'])
                ->setFirstResult($jsonRequest['offset']);

            if($jsonRequest['orderBy']['key']){
                $ub->orderBy('u.'.trim($jsonRequest['orderBy']['key']),$jsonRequest['orderBy']['order']);
            }else{
                $ub->orderBy('u.name','ASC');
            }

            $matchEmail = false;
            $matchICC = false;
            if($jsonRequest['name']){

                $matchEmail = preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#',$jsonRequest['name']);
                $matchICC = preg_match('#^\d{9}$#',$jsonRequest['name']);

                $ub->andWhere(
                    $ub->expr()->orX(
                        "u.name LIKE '%".$jsonRequest['name']."%'"
                        ,
                        "u.username LIKE '%".$jsonRequest['name']."%'"
                        ,
                        "u.email LIKE '%".$jsonRequest['name']."%'"
                        ,
                        "u.mainICC = :name"
                    )
                )
                ->setParameter('name',$jsonRequest['name'])
                ;
            }

            if(isset($jsonRequest['keywords']) && $jsonRequest['keywords']){
                $userRepo->whereKeywords($ub,$jsonRequest['keywords']);
            }

            if(isset($jsonRequest['payment_context']) && ($jsonRequest['payment_context'] == true) ){
                $userRepo->whereConfirmed($ub);
            }

            $userRepo->whereAdherent($ub);

            $currentUser =  $this->get('cairn_user.security')->getCurrentUser();

            if(! $currentUser){ //logout request
                $userRepo->whereRole($ub,'ROLE_PRO');
            }else{
                if(! $currentUser->isAdmin()){
                    
                    if($matchEmail || $matchICC){//mail exact ou n° de compte exact
                        $userRepo->whereRoles($ub,array_values($jsonRequest['roles']));
                    }else{
                        $userRepo->whereRole($ub,'ROLE_PRO');
                    } 
                    
                }else{// let admin choose according to POST sent
                    if(empty(array_values($jsonRequest['roles']))){
                        $userRepo->whereAdherent($ub);
                    }else{
                        $userRepo->whereRoles($ub,array_values($jsonRequest['roles']));
                    }
                }
            }

            $boundingValues = array_values($jsonRequest['bounding_box']);
            if( (! in_array('', $boundingValues)) && !empty($boundingValues) ){
                $ub->join('u.address','a')
                    ->andWhere('a.longitude > :minLon')
                    ->andWhere('a.longitude < :maxLon')
                    ->andWhere('a.latitude > :minLat')
                    ->andWhere('a.latitude < :maxLat')
                    ->setParameter('minLon',$jsonRequest['bounding_box']['minLon'])
                    ->setParameter('maxLon',$jsonRequest['bounding_box']['maxLon'])
                    ->setParameter('minLat',$jsonRequest['bounding_box']['minLat'])
                    ->setParameter('maxLat',$jsonRequest['bounding_box']['maxLat'])
                    ;
            }
                
            $users = $ub->getQuery()->getResult();

            if( ($matchEmail || $matchICC) && (count($users) == 1) && $users[0]->hasRole('ROLE_PERSON')){
                $res = [
                    'name' => $users[0]->getName(),
                    'account_number' => $users[0]->getMainICC()
                ];

                return $this->get('cairn_user.api')->getOkResponse($res,Response::HTTP_OK);
            }
            return $this->get('cairn_user.api')->getOkResponse($users,Response::HTTP_OK);
        }else{
            throw new NotFoundHttpException('POST Method required !');
        }
    }


    public function createOnlinePaymentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $securityService = $this->get('cairn_user.security');
        $apiService = $this->get('cairn_user.api');

        //if no user found linked to the domain name

        $creditorUser = $this->getUser();
        if(! $creditorUser ){
            return $apiService->getErrorResponse(array('User account not found') ,Response::HTTP_FORBIDDEN);
        }

        if(! ($request->headers->get('Content-Type') == 'application/json')){
            return $apiService->getErrorResponse(array('Invalid JSON') ,Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        //no possible code injection
        $postParameters = json_decode( htmlspecialchars($request->getContent(),ENT_NOQUOTES),true );

        $postAccountNumber = $postParameters['account_number'];


        if($creditorUser->getMainICC() != $postAccountNumber ){
            return $apiService->getErrorResponse(array('User not found with provided account number') ,Response::HTTP_NOT_FOUND);
        }

        if(! $creditorUser->hasRole('ROLE_PRO')){
            return $apiService->getErrorResponse(array('Access denied') ,Response::HTTP_FORBIDDEN);
        }

        if(! $creditorUser->getApiClient()){
            return $apiService->getErrorResponse(array('User has no data to perform online payment') ,Response::HTTP_PRECONDITION_FAILED);
        }

        if(! $creditorUser->getApiClient()->getWebhook()){
            return $apiService->getErrorResponse(array('No webhook defined to perform online payment') ,Response::HTTP_PRECONDITION_FAILED);
        }

        $oPRepo = $em->getRepository('CairnUserBundle:OnlinePayment');

        $onlinePayment = $oPRepo->findOneByInvoiceID($postParameters['invoice_id']);

        if($onlinePayment){
            $suffix = $onlinePayment->getUrlValidationSuffix();
        }else{
            $onlinePayment = new OnlinePayment();
            $suffix = preg_replace('#[^a-zA-Z0-9]#','@',$securityService->generateToken());
            $onlinePayment->setUrlValidationSuffix($suffix);
            $onlinePayment->setInvoiceID($postParameters['invoice_id']);
        }

        //validate POST content
        if( (! is_numeric($postParameters['amount']))   ){
            return $apiService->getErrorResponse(array('No numeric amount') ,Response::HTTP_BAD_REQUEST);
        }

        $numericalAmount = floatval($postParameters['amount']);
        $numericalAmount = round($numericalAmount,2); 

        if( $numericalAmount < 0.01  ){
            return $apiService->getErrorResponse(array('Amount too low') ,Response::HTTP_BAD_REQUEST);
        }

        if(! preg_match('#^(http|https):\/\/#',$postParameters['return_url_success'])){
            return $apiService->getErrorResponse(array('Invalid return_url_success format value') ,Response::HTTP_BAD_REQUEST);
        }

        if(! preg_match('#^(http|https):\/\/#',$postParameters['return_url_failure'])){
            return $apiService->getErrorResponse(array('Invalid return_url_failure format value') ,Response::HTTP_BAD_REQUEST);
        }

        if( strlen($postParameters['reason']) > 35){                                  
            return $apiService->getErrorResponse(array('Reason too long : 35 characters allowed') ,Response::HTTP_BAD_REQUEST);
        } 

        //finally register new onlinePayment data
        $onlinePayment->setUrlSuccess($postParameters['return_url_success']);
        $onlinePayment->setUrlFailure($postParameters['return_url_failure']);
        $onlinePayment->setAmount($numericalAmount);
        $onlinePayment->setAccountNumber($postParameters['account_number']);
        $onlinePayment->setReason($postParameters['reason']);

        $em->persist($onlinePayment);
        $em->flush();

        $payload = array(
            'invoice_id' => $postParameters['invoice_id'],
            'redirect_url' => $this->generateUrl('cairn_user_online_payment_execute',array('suffix'=>$suffix),UrlGeneratorInterface::ABSOLUTE_URL)
        );

        return $apiService->getOkResponse($payload,Response::HTTP_CREATED);
    }

}
