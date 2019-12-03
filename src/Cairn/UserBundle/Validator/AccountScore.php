<?php
// src/Cairn/UserBundle/Validator/AccountScore.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AccountScore extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_accountscore_validator';
    }       

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
