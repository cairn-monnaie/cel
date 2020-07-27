<?php
// src/Cairn/UserBundle/Validator/AddressValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserBundle\Service\Geolocalization;                                         

use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserBundle\Entity\Address;

class AddressValidator extends ConstraintValidator
{

    protected $geolocalization;

    protected $userRepo;

    public function __construct(Geolocalization $geolocalization,UserRepository $userRepo)
    {
        $this->geolocalization = $geolocalization;
        $this->userRepo = $userRepo;
    }

    /**
     * Validates the provided user address.
     *
     */
    public function validate($address, Constraint $constraint)
    {
        $user = $this->userRepo->findOneByAddress($address);

        $isPro = ($user) ? $user->hasRole('ROLE_PRO') : false;

        if((! preg_match('/^\d+/',$address->getStreet1())) && !$isPro){
            $this->context->buildViolation("Adresse imprécise. Veuillez commencer par un n° d'adresse")
                ->setInvalidValue($address->__toString())
                ->setCode('unprecise_address')
                ->atPath('street1')
                ->addViolation();
            return;
        }

        $coords = $this->geolocalization->getCoordinates($address);            

        if(!$coords['latitude']){                                                          
            $this->context->buildViolation("Adresse non localisée. Pensez à vérifier le code postal \n Référence la plus pertinente : ".$coords['closest']['label'])
                ->setInvalidValue($address->__toString())
                ->setCode('geolocalization_failed')
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
