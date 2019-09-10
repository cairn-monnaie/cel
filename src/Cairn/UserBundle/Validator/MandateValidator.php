<?php
// src/Cairn/UserBundle/Validator/MandateValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Doctrine\ORM\EntityManager;


class MandateValidator extends ConstraintValidator
{
    public function __construct()
    {
    
    }

    /**
     * Validates the provided mandate
     *
     */
    public function validate($mandate, Constraint $constraint)
    {
    }
}
