<?php
// src/Cairn/UserBundle/Validator/UserPhoneNumber.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserPhoneNumber extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_user_phone_number_validator';
    }       

}
