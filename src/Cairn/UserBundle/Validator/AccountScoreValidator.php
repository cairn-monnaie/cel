<?php
// src/Cairn/UserBundle/Validator/AccountScoreValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class AccountScoreValidator extends ConstraintValidator
{

    /**
     * Validates the provided account score information
     *
     */
    public function validate($accountScore, Constraint $constraint)
    {

        if(! $accountScore->getUser()->hasRole('ROLE_PRO')){
            $this->context->addViolation('Utilisateur doit être un professionnel');
            return;
        }

        $schedule = $accountScore->getSchedule();
        foreach($schedule as $day => $times){
            $count_time_occurrences = array_count_values($times);
            foreach($count_time_occurrences as $time_occurrences){
                if($time_occurrences > 1){
                    $this->context->buildViolation("Configuration invalide pour ".$day.' : heures identiques')
                        ->atPath('schedule')
                        ->addViolation();
                }
            }
        }

        if(! preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#',strtolower($accountScore->getEmail()) )){
            $this->context->buildViolation("Email invalide. Un email ne contient ni majuscule ni accent.Le symbole @ est suivi d\'au moins 2 chiffres/lettres, et le point de 2 ou 4 lettres.")
                ->atPath('email')
                ->addViolation();
        }

    }

}

