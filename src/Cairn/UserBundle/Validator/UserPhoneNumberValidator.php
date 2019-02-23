<?php
// src/Cairn/UserBundle/Validator/UserPhoneNumberValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserBundle\Service\Security;                                         
use Cairn\UserBundle\Repository\SmsDataRepository;

class UserPhoneNumberValidator extends ConstraintValidator
{

    protected $userRepo;
    protected $security;    

    public function __construct(SmsDataRepository $smsDataRepo,Security $security)
    {
        $this->smsDataRepo = $smsDataRepo;
        $this->security = $security;

    }

    /**
     * Validates the provided user password
     *
     * If the provided password is correct, the user attribute "passwordTries" is reinitialized to 0. Otherwise it is incremented.
     */
    public function validate($newPhoneNumber, Constraint $constraint)
    {
        $user = $this->security->getCurrentUser();

        //TODO : validate with regex
        $usersWithPhoneNumber = $this->smsDataRepo->findBy(array('phoneNumber'=> $newPhoneNumber));
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

}
