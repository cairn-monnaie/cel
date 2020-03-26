<?php
// src/Cairn/UserBundle/Validator/PhoneValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserBundle\Service\Security;                                         
use Cairn\UserBundle\Repository\UserRepository;

use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\Phone;
use Cairn\UserBundle\Entity\User;

class PhoneValidator extends ConstraintValidator
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
    public function validate($phone, Constraint $constraint)
    {
        // CHECK FOR NUMBER VALIDATION AS BEFORE
        $phoneNumber = $phone->getPhoneNumber();

        $currentUser = $this->security->getCurrentUser();

        $phoneNumber = Phone::cleanPhoneNumber($phoneNumber);


        if(! preg_match('#^(\+33|0|0033)[6-8]\d{8}$#',$phoneNumber,$matches_number)){
            $this->context->buildViolation("Format du numéro de téléphone invalide")
                ->atPath('phoneNumber')
                ->addViolation();
            return;
        }

        $listPhoneNumbers = $currentUser->getPhoneNumbers();

        /**
         * treatment is different if entity is edited or if brand new sms data. For this reason, it is fundamental to get the
         * current route
         *1) If a smsData entity is edited, the new phone number replaces the previous one in the $listPhoneNumbers array. It means that,
         *to consider this new phone number as a duplicate, it must appear two times in $listPhoneNumbers
         *2) If it is a brand new smsData entity, the new phone number won't belong to $listPhoneNumbers. Then, it must appear only once
         * to be considered as a duplicate
         */

        //whether it is an edit or add, if the phonenumber appears more than 2, wrong
        $array_occurrences_phone = array_count_values($listPhoneNumbers);

        if( array_key_exists($phoneNumber,$array_occurrences_phone)){
            if($array_occurrences_phone[$phoneNumber]>= 2){
                $this->context->buildViolation("Ce numéro vous appartient déjà.")
                    ->atPath('phoneNumber')
                    ->addViolation();
                return;
            }else{// 1 occurence
                if(! $phone->getID()){
                    $this->context->buildViolation("Ce numéro vous appartient déjà.")
                        ->atPath('phoneNumber')
                        ->addViolation();
                    return;
                }
            }
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
            if( ($bothPros || $bothPersons) && ($userWithPhoneNumber !== $currentUser)){
                $this->context->buildViolation("Ce numéro de téléphone est déjà utilisé à titre ".$status)
                    ->atPath('phoneNumber')
                    ->addViolation();
                return;
            }
        }

        // CHECK THAT ID SMS NOT ALREADY IN USE
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
