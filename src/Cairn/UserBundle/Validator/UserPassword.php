<?php
// src/Cairn/UserBundle/Validator/UserPassword.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserPassword extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_user_password_validator';
    }       

}
