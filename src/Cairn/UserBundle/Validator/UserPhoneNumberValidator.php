<?php
// src/Cairn/UserBundle/Validator/UserPhoneNumberValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserBundle\Service\Security;                                         
use Cairn\UserBundle\Repository\UserRepository;

use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\User;

class UserPhoneNumberValidator extends ConstraintValidator
{
    protected $userRepo;
    protected $security;    

    public function __construct(UserRepository $userRepo,Security $security)
    {
        $this->userRepo = $userRepo;
        $this->security = $security;
    }

    /**
     * Validates the provided user sms data.
     *
     */
    public function validate($phoneNumber, Constraint $constraint)
    {
        $currentUser = $this->security->getCurrentUser();

        $phoneNumber = SmsData::cleanPhoneNumber($phoneNumber);

        preg_match('#^\+33(6|7)\d{8}$#',$phoneNumber,$matches_number);

        if(! $matches_number){
            $this->context->buildViolation("Format du numéro de téléphone invalide")
                ->atPath('phoneNumber')
                ->addViolation();
            return;
        }


        $usersWithPhoneNumber = $this->userRepo->findUsersByPhoneNumber($phoneNumber);

        if(count($usersWithPhoneNumber) > 1){
            $this->context->buildViolation("Ce numéro de téléphone est déjà utilisé.")
                ->atPath('phoneNumber')
                ->addViolation();
            return;
        }elseif(count($usersWithPhoneNumber) == 1){
            $userWithPhoneNumber = $usersWithPhoneNumber[0];
            $bothPros = $currentUser->hasRole('ROLE_PRO') && $userWithPhoneNumber->hasRole('ROLE_PRO');
            $bothPersons = $currentUser->hasRole('ROLE_PERSON') && $userWithPhoneNumber->hasRole('ROLE_PERSON');

            $status = $currentUser->hasRole('ROLE_PRO') ? 'professionnel' : 'particulier';
            if($bothPros || $bothPersons){
                $this->context->buildViolation("Ce numéro de téléphone est déjà utilisé à titre ".$status)
                    ->atPath('phoneNumber')
                    ->addViolation();
                return;
            }

        }

//        //check length for example
//        if(strlen($smsIdentifier) < 5){
//            $this->context->buildViolation('Identifiant SMS trop court ! 5 caractères minimum')
//                ->atPath('identifier')
//                ->addViolation();
//        }
//        if(strlen($smsIdentifier) > 15){
//            $this->context->buildViolation('Identifiant SMS trop long ! 15 caractères maximum')
//                ->atPath('identifier')
//                ->addViolation();
//        }
//
//        if($currentUser->hasRole('ROLE_PRO')){ //SMS identifier contains uppercase letters & figures
//            if(preg_match('#[^A-Z0-9]#',$smsIdentifier)){
//                $this->context->buildViolation('L\'identifiant SMS PRO contient des lettres majuscules et chiffres uniquement')
//                    ->atPath('identifier')
//                    ->addViolation();
//            }
//            if(preg_match('#^[^A-Z]#',$smsIdentifier)){
//                $this->context->buildViolation('L\'identifiant SMS PRO doit commencer par une lettre')
//                    ->atPath('identifier')
//                    ->addViolation();
//            }
//
//        }else{ //ROLE_PERSON : SMS identifier contains lowercase letters & figures
//            if(preg_match('#[^a-z0-9]#',$smsIdentifier)){
//                $this->context->buildViolation('L\'identifiant SMS Particulier contient des lettres minuscules et chiffres uniquement')
//                    ->atPath('identifier')
//                    ->addViolation();
//            }
//        }


    }

}
