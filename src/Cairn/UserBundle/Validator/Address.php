<?php
// src/Cairn/UserBundle/Validator/Address.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Address extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_address_validator';
    }       

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
