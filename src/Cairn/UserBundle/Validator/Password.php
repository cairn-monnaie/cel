<?php
// src/Cairn/UserBundle/Validator/Password.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Password extends Constraint
{
    public $message = "Mot de passe trop simple.";

    public function validatedBy()
    {
        return 'cairn_user_password';
    }       
}
