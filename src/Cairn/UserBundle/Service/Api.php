<?php                                                                          
// src/Cairn/UserBundle/Service/Api.php                             

namespace Cairn\UserBundle\Service;                                      

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\NotificationData;
use Cairn\UserBundle\Entity\Phone;
use Cairn\UserBundle\Entity\BaseNotification;

use Cairn\UserBundle\Entity\File as CairnFile;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

use Cairn\UserBundle\Service\Security;

/**
 * This class contains all useful services to build an API
 *
 */
class Api
{
    protected $requestStack;

    protected $security;

    public function __construct(RequestStack $requestStack, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    /**
     * Make a request API
     *
     *@param string $baseUrl api base URL
     *@param array $params request parameters
     *@param string $resource uri matching a resource
     *@return stdClass $results result of the api request 
     */
    public function get($baseUrl, $resource, $params = NULL, $format = NULL)
    {
        $url = $baseUrl.$resource.$format;

        if($params){
            $url = $url."?".http_build_query($params);
        }

		$ch = \curl_init($url);
        
        // Set the CURL options
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
        );

        \curl_setopt_array ($ch, $options);

		// Execute the request
		$json = \curl_exec($ch);
		$code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $results = \json_decode($json,true);

        curl_close($ch);

        return array('code'=> $code, 'results' => $results);

    }

    public function isApiCall()
    {
        $request = $this->requestStack->getCurrentRequest();                     
                                                                                          
//        $isCorrectAuth = (strpos($request->headers->get('authorization'),'Bearer') !== false);
        $isCorrectUrl = (strpos($request->getRequestURI(),'/api') !== false);
        $isCorrectRoute = (strpos($request->get('_route'),'cairn_user_api') !== false);

        return ($isCorrectUrl && $isCorrectRoute);
    }

    public function isRemoteCall()
    {
        $request = $this->requestStack->getCurrentRequest();                     

        return ($request->getRequestFormat() != 'html');
    }

    public function isMobileCall()
    {
        $request = $this->requestStack->getCurrentRequest();                     

        return ( (($request->getRequestFormat() != 'html') && (strpos($request->getRequestURI(),'/mobile') !== false)) || 
            (in_array($request->get('_route'),array('cairn_zipcities_mobile','cairn_accounts_mobile_ajax' ,'cairn_user_api_get_tokens')))  );
    }

    public function is_assoc($array) {
        foreach (array_keys($array) as $k => $v) {
            if ($k !== $v)
                return true;
        }
        return false;
    }

     public function fromArrayToStringDeterministicOrder($arr)
     {
         if( is_scalar($arr)){
             if (is_bool($arr)) {
                 return $arr ? 'true' : 'false';
             }
             return strval($arr);
         }

         $res = '';
         if( is_array($arr) ){
             if(! $this->is_assoc($arr)){
                 $toSort = [];
                 foreach($arr as $item){
                     $toSort[] = $this->fromArrayToStringDeterministicOrder($item);
                 }
             }else{
                 $toSort = [];
                 foreach($arr as $key=>$item){
                     $toSort[] = $key.':'.$this->fromArrayToStringDeterministicOrder($item);

                 }
             }
             sort($toSort,SORT_STRING);
             $res .= implode($toSort);
         }

         return $res;
     }

    public function getOkResponse($responseData,$statusCode)
    {
        if(($statusCode < 200) || ($statusCode >= 300)){
            throw new \Exception('Status code '.$statusCode.' is not an OK status',500);
        }

        $response = new Response($this->serialize($responseData));            
        $response->setStatusCode($statusCode);      
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Accept', 'application/json');

        return $response;
    }

    public function getErrorResponse($messages, $statusCode)
    {
        if($statusCode < 400){
            throw new \Exception('Status code '.$statusCode.' is not an error status',500);
        }
        $errors = [];                                              
        foreach ($messages as $message) {               
            $errors[] = array('error'=>$message); 
        }                                                          
        $response = new Response(json_encode($errors));            
        $response->setStatusCode($statusCode);      
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Accept', 'application/json');

        return $response;
    }

    public function getFormErrorResponse(FormInterface $form)
    {
        $errors = [];                                              
        foreach ($form->getErrors(true) as $error) {               
            $errors[] = array('key'=>$error->getOrigin()->getName(),'error'=>$error->getMessage()); 
        }                                                          
        $response = new Response(json_encode($errors));            
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);      
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    function objectCallback($child)
    {
        $currentUser = $this->security->getCurrentUser();

        if($child instanceOf User){
            return array('name'=>$child->getName(),
                         'address'=>$child->getAddress(),
                         'image'=>$child->getImage(),
                         'email'=>$child->getEmail(),
                         'description'=>$child->getDescription(),
                         'id'=>$child->getID()
                     );
        }

        if($child instanceOf CairnFile){
            return array('url'=>$child->getUrl(),
                         'alt'=>$child->getAlt(),
                         'webPath'=>$child->getWebPath()
                     );
        }

        if($child instanceOf SmsData){
            return array('user'=>$this->objectCallback($child->getUser()),
                         'id'=>$child->getID()
                     );
        }

        if($child instanceOf NotificationData){
            return array('user'=>$this->objectCallback($child->getUser()),
                         'id'=>$child->getID()
                     );
        }

        if($child instanceOf BaseNotification){
            return json_decode($this->serialize($child),true);
        }

        if($child instanceOf Phone){
            $phoneInfos = array('id'=>$child->getID(),'identifier'=>$child->getIdentifier());

            if(($currentUser && $currentUser->isAdmin()) || ($currentUser === $child->getUser()) ){
                $phoneInfos['phoneNumber'] = $child->getPhoneNumber();
            }
            return $phoneInfos;
        }
        
    }

    public function setCallbacksAndAttributes($normalizer, $object, $extraIgnoredAttributes)
    {
        $defaultIgnoredAttributes = array();
        $serializationAttributes = ["__initializer__", "__cloner__", "__isInitialized__"];

        if($object instanceOf User){
            $defaultIgnoredAttributes = array('creationDate','superAdmin','removalRequest','identityDocument','admin','cyclosID','nbPhoneNumberRequests','passwordRequestedAt','cardAssociationTries','phoneNumberActivationTries','cardKeyTries','passwordTries','confirmationToken','cyclosToken','salt','firstname','plainPassword','password','phoneNumbers','notificationData','smsData','apiClient','localGroupReferent','singleReferent','referents','beneficiaries','card','webPushSubscriptions','usernameCanonical','emailCanonical','accountNonExpired','accountNonLocked','credentialsNonExpired','groups','groupNames');
            $normalizer->setCallbacks(array(
                        'image'=> function ($child) {return $this->objectCallback($child);},
                        'phones'=> function ($child) {
                            $phones = [];
                            foreach($child as $item){
                                $phones[] = $this->objectCallback($item);
                            }
                            return $phones;
                        },
            ));
        }
        if($object instanceOf Beneficiary){
            $defaultIgnoredAttributes = array('sources');
            $normalizer->setCallbacks(array('user'=> function ($child) {return $this->objectCallback($child);}
            ));
        }
        if($object instanceOf Operation){
            $defaultIgnoredAttributes = array('creditorContent','fromAccount','toAccount','mandate');
            $normalizer->setCallbacks(array(
                        'creditor'=> function ($child) {return $this->objectCallback($child);},
                        'debitor'=>  function ($child) {return $this->objectCallback($child);}
           ));
        }

        if($object instanceOf SmsData){
            $defaultIgnoredAttributes = array('smsClient');
            $normalizer->setCallbacks(array(
                        'user'=> function ($child) {return $this->objectCallback($child);},
           ));
        }
        if($object instanceOf NotificationData){
            $defaultIgnoredAttributes = array('deviceTokens','androidDeviceTokens','iosDeviceTokens','webPushSubscriptions','pinCode');
            $normalizer->setCallbacks(array(
                        'baseNotifications'=> function ($child) {
                            $notifs = [];
                            foreach($child as $item){
                                $notifs[] = $this->objectCallback($item);
                            }
                            return $notifs;
                        },
                        'user'=> function ($child) {return $this->objectCallback($child);},
           ));
        }
        if($object instanceOf BaseNotification){
            $defaultIgnoredAttributes = array('targetData','timeToLive','priority','collapsible','notificationData');
        }

        if($object instanceOf Phone){
            $defaultIgnoredAttributes = array('user','dailyAmountThreshold','dailyNumberPaymentsThreshold');
            $normalizer->setCallbacks(array(
                        'smsData'=> function ($child) {return $this->objectCallback($child);}
           ));
        }

        $ignoredAttributes = array_merge($defaultIgnoredAttributes, $extraIgnoredAttributes,$serializationAttributes);
        $normalizer->setIgnoredAttributes($ignoredAttributes);
    }


    /**
     * Serialize an object $object excluding attributes provided in $ignoredAttributes
     *
     *@param object $object any possible entity/object/array to serialize
     *@param array $ignoredAttributes set of attributes to not include in the serialization  
     *
     */
    public function serialize($object, $extraIgnoredAttributes=array())
    {
        $normalizer = array(new DateTimeNormalizer(),new ObjectNormalizer());

        if( is_array($object)){
            foreach($object as $item){
                $this->setCallbacksAndAttributes($normalizer[1], $item, $extraIgnoredAttributes);
            }
        }else{
            $this->setCallbacksAndAttributes($normalizer[1], $object, $extraIgnoredAttributes);
        }

        $encoder = new JsonEncoder();
        $serializer = new Serializer($normalizer, array($encoder));
       
        return $serializer->serialize($object, 'json');
    }

    public function deserialize($json_object, $class)
    {
        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));

        return $serializer->deserialize($json_object, $class, 'json');
    }
}

