<?php
// src/Cairn/UserBundle/Validator/UserPasswordValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Cairn\UserBundle\Service\Counter;                                          
use Cairn\UserBundle\Service\AccessPlatform;                                    
use Cairn\UserBundle\Service\Security;                                         
use Doctrine\ORM\EntityManager;


class UserPasswordValidator extends ConstraintValidator
{
    protected $encoderFactory;    
    protected $counter;
    protected $accessPlatform;
    protected $em;    
    protected $security;    

    public function __construct(EncoderFactory $encoderFactory, Counter $counter, AccessPlatform $accessPlatform, EntityManager $em,
        Security $security)
    {
        $this->encoderFactory = $encoderFactory;
        $this->counter = $counter;
        $this->accessPlatform = $accessPlatform;
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * Validates the provided user password
     *
     * If the provided password is correct, the user attribute "passwordTries" is reinitialized to 0. Otherwise it is incremented.
     */
    public function validate($password, Constraint $constraint)
    {
        $currentUser = $this->security->getCurrentUser();
        $encoder = $this->encoderFactory->getEncoder($currentUser);                   
        $salt = $currentUser->getSalt();                                              

        if(! $encoder->isPasswordValid($currentUser->getPassword(), $password, $salt)){
            $this->em->refresh($currentUser);

            //plainPassword is not refreshed because it does not belong to persisted attributes
            $currentUser->setPlainPassword('');
            $this->counter->incrementTries($currentUser,'password');              

            $remainingTries = 3 - $currentUser->getPasswordTries();
            $this->context->buildViolation('Mot de passe invalide. Attention, il vous reste ' .$remainingTries. ' tentative(s)')
                ->atPath('current_password')                                      
                ->addViolation();

            if($currentUser->getPasswordTries() > 2){                                
                $this->accessPlatform->disable(array($currentUser),'password_tries_exceeded');   
            }    

            $this->em->flush($currentUser); 
  
        }else{
            $this->counter->reinitializeTries($currentUser,'password');
        }


    }
}
