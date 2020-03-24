<?php
// src/Cairn/UserBundle/Validator/AddressValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserBundle\Service\Geolocalization;                                         

use Cairn\UserBundle\Entity\Address;

class AddressValidator extends ConstraintValidator
{

    protected $geolocalization;

    public function __construct(Geolocalization $geolocalization)
    {
        $this->geolocalization = $geolocalization;
    }

    /**
     * Validates the provided user address.
     *
     */
    public function validate($address, Constraint $constraint)
    {
        $coords = $this->geolocalization->getCoordinates($address);            

        if(!$coords['latitude']){                                                          
            $this->context->buildViolation("Adresse non localisée. Pensez à vérifier le code postal \n Référence la plus pertinente : ".$coords['closest']['label'])
                ->atPath('street1')
                ->addViolation();
            return;
        }else{                                                                 
            if(array_key_exists('closest',$coords)){
                $address->setStreet1($coords['closest']['name']);
            }
            
            $address->setLongitude($coords['longitude']);                      
            $address->setLatitude($coords['latitude']);                        
        }
    }

}
