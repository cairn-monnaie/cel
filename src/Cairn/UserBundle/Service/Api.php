<?php                                                                          
// src/Cairn/UserBundle/Service/Api.php                             

namespace Cairn\UserBundle\Service;                                      

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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

    /**
     * Serialize an object $object excluding attributes provided in $ignoredAttributes
     *
     *@param object $object any possible entity/object/array to serialize
     *@param array $ignoredAttributes set of attributes to not include in the serialization  
     *
     */
    public function serialize($object, $ignoredAttributes)
    {
        $normalizer = new ObjectNormalizer();
//        $normalizer->setCircularReferenceHandler(function ($child) {
//                return $child->getName();
//        });
        $normalizer->setIgnoredAttributes($ignoredAttributes);
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
       
        return $serializer->serialize($object, 'json');
    }
}

