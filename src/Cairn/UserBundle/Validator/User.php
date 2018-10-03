<?php
// src/Cairn/UserBundle/Validator/User.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class User extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_user_validator';
    }       

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
