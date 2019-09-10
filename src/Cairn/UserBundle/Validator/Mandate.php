<?php
// src/Cairn/UserBundle/Validator/Mandate.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Mandate extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_mandate_validator';
    }       

}
