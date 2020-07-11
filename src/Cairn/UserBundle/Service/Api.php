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

use Cairn\UserBundle\Service\Messages;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Form\FormInterface;

use Cairn\UserBundle\Service\Security;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * This class contains all useful services to build an API
 *
 */
class Api
{
    protected $requestStack;

    protected $security;

    protected $templating;

    protected $router;

    public function __construct(RequestStack $requestStack, Security $security, TwigEngine $templating, Router $router)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->templating = $templating;
        $this->router = $router;
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

    public function getApiResponse($apiJsonData,$statusCode)
    {
        $response = new Response($apiJsonData);            
        $response->setStatusCode($statusCode);      
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Accept', 'application/json');

        return $response;
    }

    public function getFormResponse(string $renderPath,array $renderParams = [], FormInterface $form,$messages = [])
    {
        $isRemoteCall = $this->isRemoteCall();
        $formErrors = $form->getErrors(true);

        $messages = $this->setMessages($isRemoteCall,$messages);

        if($isRemoteCall){
            $errors = [];
            foreach ($formErrors as $error) {
                $errors[] = array('field'=>$error->getOrigin()->getName(),'error'=>['key'=>$error->getMessage(),'args'=>$error->getMessageParameters()]);
            }

            $apiData = ['errors'=>$errors ,'messages'=> $messages ];
            return $this->getApiResponse($this->serialize($apiData),Response::HTTP_OK);
        }else{
            return new Response($this->templating->render($renderPath, $renderParams));
        }
    }

    public function getRenderResponse(string $renderPath,array $renderParams = [], $data, $statusCode, $messages = [])
    {
        if(($statusCode < 200) || ($statusCode >= 300)){
            throw new \Exception('Status code '.$statusCode.' is not an OK status',500);
        }

        $isRemoteCall = $this->isRemoteCall();
        $messages = $this->setMessages($isRemoteCall,$messages);

        if($isRemoteCall){
            $apiData = '{ "data": '.$this->serialize($data).',"messages":'.$this->serialize($messages).'}';
            return $this->getApiResponse($apiData,$statusCode);
        }else{
            return new Response($this->templating->render($renderPath, $renderParams));
        }
    }

    public function getRedirectionResponse(string $redirectKey,array $redirectParams = [], $data, $statusCode, $messages = [])
    {
        if(($statusCode < 200) || ($statusCode >= 300)){
            throw new \Exception('Status code '.$statusCode.' is not an OK status',500);
        }

        $isRemoteCall = $this->isRemoteCall();
        $messages = $this->setMessages($isRemoteCall,$messages);

        if($isRemoteCall){
            $apiData = '{ "data": '.$this->serialize($data).',"messages":'.$this->serialize($messages).'}';
            return $this->getApiResponse($apiData,$statusCode);
        }else{
            $redirectUrl = (filter_var($redirectKey, FILTER_VALIDATE_URL) === false) ? $this->router->generate($redirectKey,$redirectParams) : $redirectKey;
            return new RedirectResponse($redirectUrl);
        }
    }

    public function getErrorsResponse($errors, $messages, $statusCode,$redirectKey='cairn_user_welcome')
    {
        $isRemoteCall = $this->isRemoteCall();

        $formattedMessages = $this->setMessages($isRemoteCall,$messages);
        $formattedErrors = $this->setMessages($isRemoteCall,$errors);

        if($isRemoteCall){
            $apiData = ['errors'=>$formattedErrors, 'messages'=>$formattedMessages];
            return $this->getApiResponse($this->serialize($apiData),$statusCode);
        }else{
            $redirectUrl = (filter_var($redirectKey, FILTER_VALIDATE_URL) === false) ? $this->router->generate($redirectKey,[]) : $redirectKey;
            return new RedirectResponse($redirectUrl);
        }
    }

    private function setMessages($isRemoteCall,$messages)
    {
        $request = $this->requestStack->getCurrentRequest();

        $formattedMessages = Messages::getMessages($messages);
        if($isRemoteCall){
            return $formattedMessages;
        }else{
            $session = $request->getSession();
            foreach($formattedMessages as $message){
                $session->getFlashBag()->add($message['type'],$message['message']);
            }
        }
    }

    #public function getOkResponse($responseData,$statusCode,$responseKey = NULL)
    #{
    #    if(($statusCode < 200) || ($statusCode >= 300)){
    #        throw new \Exception('Status code '.$statusCode.' is not an OK status',500);
    #    }

    #    return $this->getApiResponse($this->serialize($responseData),$statusCode);
    #}

    #public function getErrorResponse($messages, $statusCode)
    #{
    #    if($statusCode < 400){
    #        throw new \Exception('Status code '.$statusCode.' is not an error status',500);
    #    }
    #    $errors = [];                                              
    #    foreach ($messages as $message) {               
    #        $errors[] = array('error'=>$message); 
    #    }                                                          

    #    return $this->getApiResponse(json_encode($errors),$statusCode);
    #}

    #public function getFormErrorResponse(FormInterface $form,$statusCode)
    #{
    #    $isRemoteCall = $this->isRemoteCall();
    #    $formErrors = $form->getErrors(true);

    #    if($isRemoteCall){
    #        $errors = [];                      
    #        foreach ($formErrors as $error) {               
    #            $errors[] = array('key'=>$error->getOrigin()->getName(),'error'=>$error->getMessage()); 
    #        }                                                          

    #        return $this->getApiResponse(json_encode($errors),Response::HTTP_OK);
    #    }
    #}

    function objectCallback($child)
    {
        $currentUser = $this->security->getCurrentUser();

        if($child instanceOf User){
            return array('name'=>$child->getName(),
                'address'=>$child->getAddress(),
                'image'=>$child->getImage(),
                'email'=>$child->getEmail(),
                'description'=>$child->getDescription(),
                'id'=>$child->getID(),
                'roles'=>$child->getRoles()
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
        $defaultIgnoredAttributes = [];
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
            $defaultIgnoredAttributes = ['fromAccount','toAccount','mandate','creditorContent'];  
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
            $defaultIgnoredAttributes = array('keyword','targetData','timeToLive','priority','collapsible','notificationData');
        }

        if($object instanceOf Phone){
            $defaultIgnoredAttributes = array('user','dailyAmountThreshold','dailyNumberPaymentsThreshold');
            $normalizer->setCallbacks(array(
                'smsData'=> function ($child) {return $this->objectCallback($child);}
            ));
        }

        $ignoredAttributes = array_merge($defaultIgnoredAttributes, $extraIgnoredAttributes,$serializationAttributes);
        $normalizer->setIgnoredAttributes($ignoredAttributes);

        return $ignoredAttributes;
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
            foreach($object as $key=>$item){
                $extraIgnoredAttributes = $this->setCallbacksAndAttributes($normalizer[1], $item, $extraIgnoredAttributes);
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

