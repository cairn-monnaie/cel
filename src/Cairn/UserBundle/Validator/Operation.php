<?php
// src/Cairn/UserBundle/Validator/Operation.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Operation extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_operation_validator';
    }       

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
