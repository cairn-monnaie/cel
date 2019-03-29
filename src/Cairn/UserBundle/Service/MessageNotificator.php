<?php
// src/Cairn/UserBundle/Service/MessageNotificator.php

namespace Cairn\UserBundle\Service;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Security\Core\User\UserInterface;
use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserBundle\Repository\SmsRepository;
use Cairn\UserBundle\Entity\Sms;

/**
 * This class contains services related to the notifications/mailing.
 *
 */
class MessageNotificator
{
    /**
     *@var UserRepository $userRepo
     */
    protected $userRepo;

    /**
     *@var SmsRepository $smsRepo
     */
    protected $smsRepo;

     /**
     * Service dealing with emails
     *@var \Swift_Mailer $mailer
     */
   protected $mailer;

   protected $templating;

    /**
     *email address defined in global parameter 
     *@var string $technicalServices
     */
    protected $technicalServices;

    /**
     * email address defined in global parameter
     *@var string $noreply
     */
    protected $noreply;

    protected $env;

    protected $smsApiToken;

    protected $smsProviderUrl;

    public function __construct(UserRepository $userRepo, SmsRepository $smsRepo,\Swift_Mailer $mailer, TwigEngine $templating,string $technicalServices,string $noreply,string $env,string $smsApiToken, string $smsProviderUrl)
    {
        $this->userRepo = $userRepo;
        $this->smsRepo = $smsRepo;

        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->technicalServices = $technicalServices;
        $this->noreply = $noreply;
        $this->env = $env;
        $this->smsApiToken = $smsApiToken;
        $this->smsProviderUrl = $smsProviderUrl;
    }


    protected function listOfIds($minID,$maxID)
    {
        $res = '';
        for($i = $minID; $i <= $maxID; $i++){
            $res .= $i.',';
        }

        return $res;
    }

    protected function getMessageData($campaignName)
    {
        $apiToken = '&api_token='.$this->smsApiToken;
        $full = '&full=0';
        $filter = '';//&filters%5Bname%5D=Validation';//Validation';
        $url = $this->smsProviderUrl.'/campaign/list/'.$this->listOfIds(34,36).'?'.$apiToken.$filter.$full;
		$ch = \curl_init($url);
        
        // Set the CURL options
        $options = array(
                CURLOPT_RETURNTRANSFER => true,
        );

        \curl_setopt_array ($ch, $options);

		// Execute the request
		$json = \curl_exec($ch);
		$code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$results = \json_decode($json);

        unset($results->result_code);
        unset($results->result_message);
        unset($results->result_output);
        unset($results->result_success);
        unset($results->success);

//        var_dump($code);
//        var_dump($results);

        $default_res = array('campaignID'=>NULL, 'messageID'=>NULL);

        foreach($results as $result){
            try{
                if( strpos($result->name, $campaignName) !== false){
                    $default_res = array('campaignID'=>$result->id, 'messageID'=>$result->messages[0]->id);

                }
            }catch(\Exception $e){
                var_dump($e);
            }
        }

        return $default_res;
    }

    protected function getMessageContent($parameters, $templateMessage)
    {
        $message = $templateMessage;
        foreach($parameters as $key=>$value){
            $message = str_replace('%'.$key.'%',$value, $message);
        }

        return $message;
    }

    protected function generateGetFields($parameters)
    {
        $res = '';

        foreach($parameters as $key => $value){
            if(is_array($value)){
                $value = implode($value);
            }
           $res .= "&".$key."=".$value; 
        }

        return $res;
    }

    /**
     * 
     *
     * All contacts in our list of possible contacts have global fields than must be edited while editing the contact himself.
     * In the third-party application registering contacts, these global fields have the format %FIELD%. Therefore, when used in URL 
     * parameters, they have a specific format
     */
    protected function generateContactFields($parameters)
    {
        $res = '';

        foreach($parameters as $key => $value){
           $res .= "&field%5B%25".$key."%25%2C0%5D=".$value; 
        }

        return $res;
    }

    public function sendSMS($phoneNumber, $content, Sms $smsToAnswer = NULL)
    {

        //if user has not been warned about spam activity yet, we send him a text to do so. Otherwise, nothing sent
        if($smsToAnswer && $this->isSpam($smsToAnswer)){
            $smsToAnswer->setState(Sms::STATE_SPAM); 

            $nbSpamSms = $this->getNumberOfTodaySms($phoneNumber, Sms::STATE_SPAM);

            if($nbSpamSms >=1 ){
                return NULL;
            }else{
                $content = 'Votre activité du jour a été identifiée comme du SPAM. Les opérations SMS vous sont interdites pour aujourd\'hui. Merci de votre compréhension';
            }
        }

        $action = ($this->env == 'prod') ? 'send' : 'test';
        $action = '&action='.$action;

        $apiToken = 'api_token='.$this->smsApiToken;

        //get campaign ID
        $campaignName = 'e-Cairn SMS';
        $messageData =  $this->getMessageData($campaignName);

        $campaignID = '&campaign_id='.$messageData['campaignID'];
        $messageID = '&message_id='.$messageData['messageID'];

        if(! $campaignID){
            $subject = 'Service SMS indisponible';
            $body = 'Erreur : Campagne SMS non trouvée.'."\n".'Le SMS de contenu '.$content.' n\'a pu être envoyé au numéro '.$phoneNumber;
            $from = $this->getNoReplyEmail();
            $to = $this->getMaintenanceEmail();

            $this->notifyByEmail($subject, $from, $to, $body);
            return;
        }


        //edit contact who will receive SMS to set parameters
        $url = $this->smsProviderUrl.'/contact/edit/10?'.$apiToken;
		$ch = \curl_init($url);
        
        $postfields_base = "p%5B{{list_id}}%5D=3&lang=fr&country=FR&continue_if_in_list=1&update_if_exist=1";


        $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_HTTPHEADER => array('Content-type: application/x-www-form-urlencoded'),
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $postfields_base.$this->generateContactFields( array('SMS_CONTENT'=> $content)),
        );

        \curl_setopt_array ($ch, $options);

		$json = \curl_exec($ch);
		$code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$result = \json_decode($json);

//        var_dump($code);
//        var_dump($result);

        $phoneNumber = '+33669734539';
        $mobile = '&mobile='.$phoneNumber;
        $type='&type=text';
        
        $url = $this->smsProviderUrl.'/campaign/sms/sendSms?'.$apiToken.$campaignID.$messageID.$action.$mobile.$type;
		$ch = \curl_init($url);
        
        // Set the CURL options
        $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
        );

        \curl_setopt_array ($ch, $options);

		// Execute the request
//    	$json = \curl_exec($ch);
//		$code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
//		$result = \json_decode($json);
//
//        $err = curl_error($ch);
//
//        curl_close($ch);
//        var_dump($err);
//        var_dump($json);
        //TODO : define what a good result is. For now, we say code = 200
//        if($code != 200){
//            $subject = 'Service SMS indisponible';
//            $body = 'Erreur lors de l envoi SMS.'."\n".'Le SMS de contenu '.$content.' n\'a pu être envoyé au numéro '.$phoneNumber;
//            $from = $this->getNoReplyEmail();
//            $to = $this->getMaintenanceEmail();
//
//            $this->notifyByEmail($subject, $from, $to, $body);
//            return;
//        }
        $email = 'whoknows@test.com';
        $this->notifyByEmail('SMS',$this->getNoReplyEmail(), $email, $content);
        $sms = new Sms($phoneNumber,$content,Sms::STATE_SENT);

        return $sms;
    }

    public function getNoReplyEmail()
    {
        return $this->noreply;
    }

    public function getMaintenanceEmail()
    {
        return $this->technicalServices;
    }

    /**
     *Send emails to all users with roles $roles
     *
     *@param array $roles
     *@param string $subject
     *@param string $from email adress of the sender
     *@param text $body HTML text
     */
    public function notifyRolesByEmail($roles, $subject,$from,$body)
    {
        $users = $this->userRepo->myFindByRole($roles);
        foreach($users as $user){
            $to = $user->getEmail();
            $this->notifyByEmail($subject,$from,$to,$body);
        }
    }

    /**
     *Send email
     *
     *@param string $subject
     *@param string $from email adress of the sender
     *@param string $to email adress of the receiver
     *@param text $body HTML text content
     */
    public function notifyByEmail($subject,$from,$to,$body)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body,'text/html');
        $this->mailer->send($message);
    }

    protected function getNumberOfTodaySms($phoneNumber,$state,$content = NULL)
    {
        $sb = $this->smsRepo->createQueryBuilder('s'); 
        $this->smsRepo
            ->whereCurrentDay($sb)
            ->wherePhoneNumbers($sb,$phoneNumber)
            ->whereState($sb, $state);
        if($content){
            $this->smsRepo->whereContentContains($sb,$content);
        }

        $nbSms = $sb->select('count(s.id)')->getQuery()->getSingleScalarResult();

        return $nbSms;
    }

    public function isSpam(Sms $sms)
    {
        //number of spam sms today
        $nbSpamSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_SPAM);
        if($nbSpamSms >= 1){return true;}

        //number of account balance requests a day
        if( strpos($sms->getContent(),'SOLDE') !== false){
            $nbSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_PROCESSED, 'SOLDE');
            if($nbSms >= 1){return true;}
        }

        //number of SMS identifier requests a day
        if( strpos($sms->getContent(),'LOGIN') !== false){
            $nbIdentifierSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_PROCESSED, 'LOGIN');
            if($nbIdentifierSms >= 1){return true;}
        }

        //number of invalid SMS requests a day
        if($sms->getState() == Sms::STATE_INVALID){
            $nbInvalidSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_INVALID);
            if($nbInvalidSms >= 4){return true;}
        }

        //number of unauthorized SMS requests a day
        if($sms->getState() == Sms::STATE_UNAUTHORIZED){
            $nbUnauthorizedSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_UNAUTHORIZED);
            if($nbUnauthorizedSms >= 1){return true;}
        }

        //number of error SMS requests a day
        if($sms->getState() == Sms::STATE_ERROR){
            $nbErrorSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_ERROR);
            if($nbErrorSms >= 1){return true;}
        }

        //number of expired SMS requests a day
        if($sms->getState() == Sms::STATE_EXPIRED){
            $nbExpiredSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_EXPIRED);
            if($nbExpiredSms >= 4){return true;}
        }

        //number of canceled SMS requests a day
        if($sms->getState() == Sms::STATE_CANCELED){
            $nbCanceledSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_CANCELED);
            if($nbCanceledSms >= 3){return true;}
        }

        return false;
    }

}

