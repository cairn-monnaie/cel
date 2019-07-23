<?php                                                                          
// src/Cairn/UserBundle/Service/Api.php                             

namespace Cairn\UserBundle\Service;                                      

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\Phone;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

/**
 * This class contains all useful services to build an API
 *
 */
class Api
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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

    public function getErrorResponse(FormInterface $form)
    {
        $errors = [];                                              
        foreach ($form->getErrors(true) as $error) {               
            $errors[$error->getOrigin()->getName()] = $error->getMessage(); 
        }                                                          
        $response = new Response(json_encode($errors));            
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);      
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    function objectCallback($child)
    {
        if($child instanceOf User){
            return array('name'=>$child->getName(),
                         'id'=>$child->getID()
                     );
        }
        if($child instanceOf SmsData){
            return array('user'=>$this->objectCallback($child->getUser()),
                         'id'=>$child->getID()
                     );
        }
    }

    public function setCallbacksAndAttributes($normalizer, $object, $extraIgnoredAttributes)
    {
        $defaultIgnoredAttributes = array();

        if($object instanceOf User){
            $defaultIgnoredAttributes = array('password','phones','smsData','apiClient','localGroupReferent','singleReferent','referents','beneficiaries','card');
        }
        if($object instanceOf Beneficiary){
            $defaultIgnoredAttributes = array('sources');
            $normalizer->setCallbacks(array('user'=> function ($child) {return $this->objectCallback($child);}
            ));
        }
        if($object instanceOf Operation){

            $defaultIgnoredAttributes = array('fromAccount','toAccount');
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
        if($object instanceOf Phone){
            $defaultIgnoredAttributes = array();
            $normalizer->setCallbacks(array(
                        'smsData'=> function ($child) {return $this->objectCallback($child);},
                        'user'=> function ($child) {return $this->objectCallback($child);},
           ));
        }

        $ignoredAttributes = array_merge($defaultIgnoredAttributes, $extraIgnoredAttributes);
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

