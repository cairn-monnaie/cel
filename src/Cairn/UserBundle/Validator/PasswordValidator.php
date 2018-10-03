<?php
// src/OC/PlatformBundle/Validator/AntifloodValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Service\ChannelInfo;
use Cairn\UserCyclosBundle\Service\GroupInfo;
use Cairn\UserCyclosBundle\Service\NetworkInfo;

class PasswordValidator extends ConstraintValidator
{
    protected $networkName;    
    protected $groupName;    
    protected $channelInfo;
    protected $groupInfo;
    protected $networkInfo;
    protected $cyclosUserManager;

    public function __construct($networkName, $groupName, ChannelInfo $channelInfo, GroupInfo $groupInfo, NetworkInfo $networkInfo)
    {
        $this->networkName = $networkName;
        $this->groupName = $groupName;
        $this->channelInfo = $channelInfo;
        $this->groupInfo = $groupInfo;
        $this->networkInfo = $networkInfo;
        $this->cyclosUserManager = new UserManager();
    }

    /**
     * Validates the provided password using Cyclos inner validation algorithm
     *
     * This function creates a user on Cyclos side with random values and the provided password $value. This way, if Cyclos returns a 
     * validation error, it means that its inner validation algorithm detected it as too obvious or too repetitive. If so, a violation
     * error is added.
     * WARNING : for this validation process to be useful, one needs to allow the inner algorithm in Cyclos configuration(password types) 
     *
     */
    public function validate($value, Constraint $constraint)
    {
        $this->networkInfo->switchToNetwork($this->networkName);

        $groupName = $this->groupName;
        $groupVO = $this->groupInfo->getGroupVO($groupName);

        //if the webServices channel is not added, it will be impossible to update/remove the cyclos user entity from third application
        $webServicesChannelVO = $this->channelInfo->getChannelVO('webServices');

        $userDTO = new \stdClass();                                            
        $userDTO->name = 'Random Name';                                        
        $userDTO->username = 'random_username';                                
        $userDTO->internalName = 'randomusername';                             
        $userDTO->login = 'random_username';                                   
        $userDTO->email = 'random@cairn-monnaie.com';                          

        $password = new \stdClass();                                           
        $password->assign = true;                                              
        $password->type = 'login';//in Cyclos : System -> User config -> password types -> click on login Password
        $password->value = $value;                  
        $password->confirmationValue = $value;//control already done in Symfony
        $userDTO->passwords = $password;                                       

        try{                                                                   
            $newUserCyclosID = $this->cyclosUserManager->addUser($userDTO,$groupVO,$webServicesChannelVO);
            $params = new \stdClass();                                             
            $params->status = 'REMOVED';
            $params->user = $newUserCyclosID;
            $this->cyclosUserManager->changeStatusUser($params);
        }catch(\Exception $e){                                                 
//            var_dump($e);
//            $e->tamere;
//            if($e->errorCode == 'VALIDATION'){                                 
                $this->context->buildViolation($constraint->message)
                    ->atPath('plainPassword')
                    ->addViolation();
//            }else{
//                throw $e;
//            }
        }

    }
}
