<?php
// src/Cairn/UserBundle/Validator/SmsData.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SmsData extends Constraint
{
    public function validatedBy()
    {
        return 'cairn_smsdata_validator';
    }       

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
