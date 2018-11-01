<?php
// src/Cairn/UserBundle/Validator/Transaction.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Transaction extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_transaction_validator';
    }       

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
