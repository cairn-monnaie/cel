<?php
// src/Cairn/UserBundle/Service/MessageNotificator.php

namespace Cairn\UserBundle\Service;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Security\Core\User\UserInterface;
use Cairn\UserBundle\Entity\Sms;
use Cairn\UserBundle\Entity\BaseNotification;
use Cairn\UserBundle\Entity\PaymentNotification;
use Cairn\UserBundle\Entity\RegistrationNotification;

use Cairn\UserBundle\Entity\User;                       

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\Exception\InvalidArgumentException;


use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

/**
 * This class contains services related to the notifications/mailing.
 *
 */
class MessageNotificator
{
    /**
     *@var EntityManager $em
     */
    protected $em;

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

    protected $consts;

    public function __construct(EntityManager $em,\Swift_Mailer $mailer, TwigEngine $templating,string $technicalServices,string $noreply,string $env, array $consts)
    {
        $this->em = $em;

        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->technicalServices = $technicalServices;
        $this->noreply = $noreply;
        $this->env = $env;
        $this->consts = $consts;
    }

    public function sendRegisterNotifications(User $user)
    {
        //if(! $user->hasRole('ROLE_PRO')){
        //    return;
        //}

        $payload = RegistrationNotification::getPushData($user);
        $nfKeyword = BaseNotification::KEYWORD_REGISTER;

        $webPushData = array(
            'title'=> 'Nouveau pro !',
            'payload'=>array(
                'body' => 'Nouveau pro dans le réseau pouloulou',
                'tag' => $payload['tag'], 
                'data'=>$payload
            )
        );

        $appPushData = $payload;

        $targets = $this->em->getRepository('CairnUserBundle:RegistrationNotification')->findTargetsAround($user->getAddress()->getLatitude(),$user->getAddress()->getLongitude());

        $this->sendAppPushNotifications(
            $targets['device_tokens'],$appPushData,$nfKeyword,BaseNotification::TTL_REGISTER,BaseNotification::PRIORITY_VERY_LOW
        );
        $this->sendWebPushNotifications(
            $targets['web_endpoints'],$webPushData,$nfKeyword,BaseNotification::TTL_REGISTER,BaseNotification::PRIORITY_HIGH
        );

    }

    public function sendPaymentNotifications($operation, $phoneNumber = NULL)
    {
        $nfKeyword = BaseNotification::KEYWORD_PAYMENT;
        $nfData = $operation->getCreditor()->getNotificationData();

        if( (! $nfData) && $phoneNumber){ 
            $this->sendSMS($phoneNumber,$operation->getCreditorContent());
            return ; 
        }

        $paymentNotification = $this->em->getRepository('CairnUserBundle:PaymentNotification')->findPaymentNotification($nfData,[$operation->getType()],$operation->getAmount());
        if( $paymentNotification){
            $targets = $paymentNotification->getTargetData($operation->getType(),$phoneNumber);
            $payload = PaymentNotification::getPushData($operation);

            $webPushData = array(
                'title'=> 'Vous avez reçu un paiement !',
                'payload'=>array(
                    'body' => $operation->getCreditorContent(),
                    'tag' => $payload['tag'], 
                    'data'=>$payload
                )
            );

            $appPushData = $payload;

            if($targets['email']){
                $body = $this->templating->render('CairnUserBundle:Emails:payment_notification.html.twig',
                    array('operation'=>$operation));

                $this->notifyByEmail('Vous avez reçu des cairns',
                    $this->getNoReplyEmail(),$operation->getCreditor()->getEmail(),$body);
            }

            if($targets['phone']){
                $this->sendSMS($targets['phone'],$operation->getCreditorContent());
            }

            $this->sendAppPushNotifications(
                $targets['deviceTokens'],$appPushData,$nfKeyword,BaseNotification::TTL_PAYMENT,BaseNotification::PRIORITY_HIGH
            );
            $this->sendWebPushNotifications(
                $targets['webSubscriptions'],$webPushData,$nfKeyword,BaseNotification::TTL_PAYMENT,BaseNotification::PRIORITY_HIGH
            );

        }
    }

    /**
     *
     *@see https://firebase.google.com/docs/cloud-messaging/http-server-ref?hl=fr#send-downstream
     */
    public function sendAppPushNotifications(array $tokens = [], $data, $keyword, $ttl, $priority)
    {
        if( empty($tokens) ){ return; }
        $pushConsts = $this->consts['mobilepush'];
        $androidConsts = $pushConsts['android'];

        // Message to be sent
        $push = array(
            'data'=> $data,
            'collapse_key'=> $keyword,
            'android'=>array(
                'ttl'=> $ttl,
                'priority'=> $priority,
            )
        );

        if(count($tokens) == 1){
            $push['to'] = $tokens[0];
        }else{
            $push['registration_ids'] = $tokens;
        }

        $headers = array(
            'Authorization: key=' . $androidConsts['api_key'],
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the URL, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $androidConsts['api_url']);
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, ($this->env != 'test'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $push));

        // Execute post
        $jsonResponse = curl_exec($ch);
        file_put_contents('test1.txt',json_encode($jsonResponse));

        $response = json_decode($jsonResponse,true);
        $code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){//messages have been sent, but maybe with errors
            //get DEPRECATED RESULTS HERE
            $possibleErrors = array('InvalidRegistration','NotRegistered');
            $failedTokens = [];
            foreach($response['results'] as $index=>$result){
                if(isset($result['error']) && in_array($result['error'],$possibleErrors) ){
                    $failedTokens[] = $tokens[$index];
                }
            }
            
            $nfDataRepo = $this->em->getRepository('CairnUserBundle:NotificationData');

            //first version: simple foreach loop, not scalable...
            foreach($failedTokens as $failedToken){
                $nfDataList = $nfDataRepo->findByDeviceTokens($failedTokens);

                foreach($nfDataList as $nfData){
                    $nfData->removeDeviceToken($failedToken);
                }
            }

        } // else messages not sent
        
        //TODO : better version : less requests


        // Close connection
        curl_close($ch);
    }

    public function sendWebPushNotifications($subscriptions, $data, $keyword, $ttl, $priority)
    {
        if(! isset($data['title'])){
            throw new InvalidArgumentException('WebPush title field required !');
        }
        if(! isset($data['payload'])){
            throw new InvalidArgumentException('WebPush payload filed required !');
        }

        // ------------------- SEND NON APPLE PUSH -------------------//
        $auth = array(
            'GCM' => 'MY_GCM_API_KEY',// deprecated and optional, it's here only for compatibility reasons
            'VAPID'=>array(
                'subject' => 'https://moncompte.cairn-monnaie.com',
                'publicKey' => $this->consts['webpush']['public_key'],
                'privateKey' => $this->consts['webpush']['private_key']
            )
        );

        $defaultOptions = [
            'TTL' => $ttl, // 2h 
            'urgency' => $priority, // protocol defaults to "normal"
            //'topic' => 'new_event', // not defined by default,
            'batchSize' => 200, // defaults to 1000
        ];

        $webPush = new WebPush($auth);
//        $webPush->setReuseVAPIDHeaders(true);
        $webPush->setDefaultOptions($defaultOptions);

        foreach($subscriptions['mozilla'] as $subscription){
            $notification = array(
                'subscription'=> Subscription::create(
                    array(
                        'endpoint' => $subscription->getEndpoint(),
                        'keys'=>$subscription->getEncryptionKeys()
                    )
                ),
                'payload'=>json_encode($data)
            );

            $webPush->sendNotification($notification['subscription'],$notification['payload']);
        }

        $failedEndpoints = [];
        ////check sent results
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getEndPoint();

            if(! $report->isSuccess()){
                if($report->isSubscriptionExpired()){
                  $failedEndpoints[] = $endpoint;
                }
            }
        }

        
        $webPushSubsList = $this->em->getRepository('CairnUserBundle:WebPushSubscription')->findSubsByWebEndpoints($failedEndpoints,true);

        foreach($webPushSubsList as $sub)
        {
            $nfData = $sub->getNotificationData();
            $nfData->removeWebPushSubscription($sub);

            $collectionWebPushSubs = $nfData->getWebPushSubscriptions();

            if($collectionWebPushSubs->count() == 0){//IF no more web push subscription, consider web push disabled for all notifications
                foreach($nfData->getBaseNotifications() as $notification)
                {
                    $notification->setWebPushEnabled(false);
                }
            }
            
            $this->em->remove($sub);
        }
    }

    protected function listOfIds($minID,$maxID)
    {
        $res = '';
        for($i = $minID; $i <= $maxID; $i++){
            $res .= $i.',';
        }

        return $res;
    }

    /**
     * Returns array containing campaign ID and message ID from $campaignName
     *
     *@param string $campaignName
     *@return array
     */
    protected function getMessageData($campaignName)
    {
        $apiToken = '&api_token='.$this->consts['sms']['api_token'];
        $full = '&full=0';
        $filter = '';//&filters%5Bname%5D=Validation';//Validation';
        $url = $this->consts['sms']['provider_url'].'/campaign/list/'.$this->listOfIds(34,36).'?'.$apiToken.$filter.$full;
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

        //cleans response of unnecessary data
        unset($results->result_code);
        unset($results->result_message);
        unset($results->result_output);
        unset($results->result_success);
        unset($results->success);

//        var_dump($code);
        //var_dump($results);

        $default_res = array('campaignID'=>NULL, 'messageID'=>NULL);

        foreach($results as $result){
                if( strpos($result->name, $campaignName) !== false){
                    $default_res = array('campaignID'=>$result->id,'messageID'=>NULL);//$result->messages[0]->id);
                }
        }

        return $default_res;
    }

//    protected function getMessageContent($parameters, $templateMessage)
//    {
//        $message = $templateMessage;
//        foreach($parameters as $key=>$value){
//            $message = str_replace('%'.$key.'%',$value, $message);
//        }
//
//        return $message;
//    }

//    protected function generateGetFields($parameters)
//    {
//        $res = '';
//
//        foreach($parameters as $key => $value){
//            if(is_array($value)){
//                $value = implode($value);
//            }
//           $res .= "&".$key."=".$value; 
//        }
//
//        return $res;
//    }

    /**
     * 
     * All contacts in our list of possible contacts have global fields than must be edited while editing the contact himself.
     * In the third-party application registering contacts, these global fields have the format %FIELD%. Therefore, when used in URL 
     * parameters, they have a specific format
     *
     * @param array Parameters for specific request fields
     * @return array Correct format for our SMS provider 
     */
    protected function generateContactFields($parameters)
    {
        $res = '';

        foreach($parameters as $key => $value){
           $res .= "&field%5B%25".$key."%25%2C0%5D=".$value; 
        }

        return $res;
    }

    /**
     * If sent SMS is not considered as spam, we reply back with $param content
     *
     * @param string $phoneNumber phone number to reply back to 
     * @param string $content content of the sms to send to the phone number $phoneNumber
     * @param Sms $smsToAnswer SMS which requires a response
     * @return Sms entity representing the app reply to the user with phone number $phoneNumber
     */
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

        $apiToken = 'api_token='.$this->consts['sms']['api_token'];

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
        $url = $this->consts['sms']['provider_url'].'/contact/edit/10?'.$apiToken;
		$ch = \curl_init($url);
        
        $postfields_base = "p%5B{{list_id}}%5D=3&mobile=".$phoneNumber."&lang=fr&country=FR&continue_if_in_list=1&update_if_exist=1";

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

//        var_dump($result);
//        var_dump($code);
//        var_dump($result);

        $mobile = '&mobile='.$phoneNumber;
        $type='&type=text';
        
        if($this->env == 'prod'){
            $url = $this->consts['sms']['provider_url'].'/campaign/sms/sendSms?'.$apiToken.$campaignID.$messageID.$action.$mobile.$type;
            $ch = \curl_init($url);

            // Set the CURL options
            $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
            );

            \curl_setopt_array ($ch, $options);

            // Execute the request
            $json = \curl_exec($ch);
            $code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result = \json_decode($json);

            $err = curl_error($ch);

            curl_close($ch);
        }else{
            $email = 'whoknows@test.com';
            $this->notifyByEmail('SMS',$this->getNoReplyEmail(), $email, $content);
        }
      //  var_dump($err);
      //  var_dump($json);
      //TODO : define what a good result is. For now, we say code = 200
        //if($code != 200){
        //    $subject = 'Service SMS indisponible';
        //    $body = 'Erreur lors de l envoi SMS.'."\n".'Le SMS de contenu '.$content.' n\'a pu être envoyé au numéro '.$phoneNumber;
        //    $from = $this->getNoReplyEmail();
        //    $to = $this->getMaintenanceEmail();

        //    $this->notifyByEmail($subject, $from, $to, $body);
        //    return;
        //}
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
        $users = $this->em->getRepository('CairnUserBundle:User')->myFindByRole($roles);
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
    public function notifyByEmail($subject,$from,$to,$body, $attachment = NULL)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body,'text/html');

        if($attachment){
            $message->attach($attachment);
        }

        $this->mailer->send($message);
    }

    /**
     * Returns the number of sms received on current day from phone number $phoneNumber with state $state and content $content
     *
     *@param string $phoneNumber SMS sender
     *@param const int $state State of the SMS in our app (expired, canceled, processed, ...)
     *@param string $content  SMS content
     *@return int 
     */
    protected function getNumberOfTodaySms($phoneNumber,$state,$content = NULL)
    {
        $smsRepo = $this->em->getRepository('CairnUserBundle:Sms');
        $sb = $smsRepo->createQueryBuilder('s'); 
        $smsRepo
            ->whereCurrentDay($sb)
            ->wherePhoneNumbers($sb,$phoneNumber)
            ->whereState($sb, $state);
        if($content){
            $smsRepo->whereContentContains($sb,$content);
        }

        $nbSms = $sb->select('count(s.id)')->getQuery()->getSingleScalarResult();

        return $nbSms;
    }

    /**
     * Returns true if $sms is considered as SPAM, false otherwise
     *
     *@param Sms $sms Entity to consider as spam or not
     *@return boolean 
     */
    public function isSpam(Sms $sms)
    {
        //number of spam sms today
        $nbSpamSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_SPAM);
        if($nbSpamSms >= 1){return true;}

        //number of account balance requests a day
        if( strpos($sms->getContent(),'SOLDE') !== false){
            $nbSms = $this->getNumberOfTodaySms($sms->getPhoneNumber(), Sms::STATE_PROCESSED, 'SOLDE');
            if($nbSms >= 2){return true;}
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

    public function getTemplatingService()
    {
        return $this->templating;
    }

}

