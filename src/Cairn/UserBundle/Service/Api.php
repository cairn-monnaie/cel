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

use Symfony\Component\HttpFoundation\RequestStack;

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

    public function isApiCall()
    {
        $request = $this->requestStack->getCurrentRequest();                     
                                                                                          
//        $isCorrectAuth = (strpos($request->headers->get('authorization'),'Bearer') !== false);
        $isCorrectUrl = (strpos($request->getRequestURI(),'/api') !== false);
        $isCorrectRoute = (strpos($request->get('_route'),'cairn_user_api') !== false);

        return ($isCorrectUrl && $isCorrectRoute);
    }

    function objectCallback($child)
    {
        if($child instanceOf User){
            return array('name'=>$child->getName(),
                         'id'=>$child->getID()
                     );
        }
    }

    public function setCallbacksAndAttributes($normalizer, $object, $extraIgnoredAttributes)
    {
        $defaultIgnoredAttributes = array();
        if($object instanceOf User){
            $defaultIgnoredAttributes = array('localGroupReferent','singleReferent','referents','beneficiaries','card');
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
            $defaultIgnoredAttributes = array();
            $normalizer->setCallbacks(array(
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
        $normalizer = new ObjectNormalizer();
        $this->setCallbacksAndAttributes($normalizer, $object, $extraIgnoredAttributes);
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
       
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

