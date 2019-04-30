<?php
// src/Cairn/UserBundle/Validator/SmsDataValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserBundle\Service\Security;                                         
use Cairn\UserBundle\Repository\UserRepository;

use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\User;

class SmsDataValidator extends ConstraintValidator
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
    public function validate($smsData, Constraint $constraint)
    {
        $currentUser = $this->security->getCurrentUser();
        $user = $smsData->getUser();

        $smsIdentifier = $smsData->getIdentifier();

        //TODO : validate with regex
        $phoneNumber = $smsData->getPhoneNumber();
        $phoneNumber = preg_replace('/[^0-9+]/', '',$phoneNumber);

        preg_match('#^\+33(6|7)\d{8}$#',$phoneNumber,$matches_number);

        if(! $matches_number){
            $this->context->buildViolation("Format du numéro de téléphone invalide")
                ->atPath('phoneNumber')
                ->addViolation();
            return;
        }

        $usersWithPhoneNumber = $this->userRepo->findUsersByPhoneNumber($phoneNumber);

        $isNewPhoneNumber = true;

        //if current user is adherent and is not in the list of users owning the phone number, it means that it is an edit
        if($currentUser->isAdmin()){
            $isNewPhoneNumber = false;
        }else{
            foreach($usersWithPhoneNumber as $userWithPhoneNumber){
                if($userWithPhoneNumber === $currentUser){
                    $isNewPhoneNumber = false;
                }
            }
        }

        if($isNewPhoneNumber){ //relevant only if request is a new phone number
            if(count($usersWithPhoneNumber) > 1){
                $this->context->buildViolation("Ce numéro de téléphone est déjà utilisé.")
                    ->atPath('phoneNumber')
                    ->addViolation();
            }elseif(count($usersWithPhoneNumber) == 1){
                $userWithPhoneNumber = $usersWithPhoneNumber[0];
                $bothPros = $user->hasRole('ROLE_PRO') && $userWithPhoneNumber->hasRole('ROLE_PRO');
                $bothPersons = $user->hasRole('ROLE_PERSON') && $userWithPhoneNumber->hasRole('ROLE_PERSON');

                if($bothPros || $bothPersons){
                    $this->context->buildViolation("Ce numéro de téléphone est déjà utilisé")
                        ->atPath('phoneNumber')
                        ->addViolation();
                }

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
