<?php
// src/Cairn/UserBundle/Validator/TransactionValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Service\AccountInfo;
use Cairn\UserCyclosBundle\Service\UserInfo;
use Cairn\UserBundle\Repository\UserRepository;

class TransactionValidator extends ConstraintValidator
{

    protected $userRepo;
    protected $userInfo;
    protected $accountInfo;

    public function __construct(UserRepository $userRepo, UserInfo $userInfo, AccountInfo $accountInfo)
    {
        $this->userRepo = $userRepo;
        $this->userInfo = $userInfo;
        $this->accountInfo = $accountInfo;
    }

    /**
     * Validates the provided transaction information
     *
     */
    public function validate($transaction, Constraint $constraint)
    {
        if($transaction->getAmount() < 0.01){
            $this->context->buildViolation('Montant trop faible : doit être supérieur à 0.01')
                ->atPath('amount')
                ->addViolation();
        }

        $fromICC = $transaction->getFromAccount()['accountNumber'];
        $emailTo = $transaction->getToAccount()['email'];
        $toICC = $transaction->getToAccount()['accountNumber']; 

        //check debitor account
        if(!$fromICC){                                    
            $this->context->buildViolation('Le compte débiteur n\'a pas été sélectionné')
                ->atPath('fromAccount')                                        
                ->addViolation();                                              
        }else{ //fromICC provided
            try{
                $account = $this->accountInfo->getAccountByID($fromICC);
                if(!$account->unlimited){
                    if($account->status->availableBalance < $transaction->getAmount()){
                        $this->context->buildViolation('Montant trop élevé : modifiez-le ou rechargez votre compte.')
                            ->atPath('amount')
                            ->addViolation();
                    }
                }
            }catch(\Exception $e){
                if($e->errorCode == 'ENTITY_NOT_FOUND' || $e->errorCode == 'NULL_POINTER'){
                    $this->context->buildViolation('Le compte débiteur n\'existe pas ou ne vous appartient pas')
                        ->atPath('fromAccount')                                        
                        ->addViolation();                                              
                }else{
                    throw $e;
                }
            }
        }

        //check creditor account
        if(! ($toICC || $emailTo)){ 
            $this->context->buildViolation('Sélectionnez au moins l\'email ou l\'ICC.')
                ->atPath('toAccount')                                          
                ->addViolation();                                              
        }else{ //email and ICC provided
            if($toICC){
                $toUserVO = $this->userInfo->getUserVOByKeyword($toICC);

                if(!$toUserVO){
                    $this->context->buildViolation('ICC introuvable')
                        ->atPath('toAccount')                                          
                        ->addViolation();                                              
                }else{
                    if($fromICC == $toICC){
                        $this->context->buildViolation('Les comptes débiteur et créditeur sont identiques')
                            ->atPath('toAccount')                                          
                            ->addViolation();                                              
                    }
                }
            }
            if($emailTo){
                $creditorUser = $this->userRepo->findOneBy(array('email'=>$emailTo));

                if(!$creditorUser){//invalid email
                    $this->context->buildViolation('Aucun membre avec email '.$emailTo)
                        ->atPath('toAccount')                                          
                        ->addViolation();                                              
                }
            }
            if($toICC && $emailTo){
                if($toUserVO && $creditorUser){
                    if(! ($creditorUser->getUsername() == $toUserVO->username)){//email not provided so we use username instead
                        $this->context->buildViolation('email et ICC ne correspondent pas')
                            ->atPath('toAccount')                                          
                            ->addViolation();                                              
                    }


                }
            }
        }
    } 


}
