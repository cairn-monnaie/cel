<?php
// src/Cairn/UserBundle/Validator/Phone.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Phone extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_phone_validator';
    }       

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
