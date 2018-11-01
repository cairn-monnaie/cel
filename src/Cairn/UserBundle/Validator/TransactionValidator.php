<?php
// src/Cairn/UserBundle/Validator/TransactionValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Service\AccountInfo;
use Cairn\UserBundle\Repository\UserRepository;

class TransactionValidator extends ConstraintValidator
{

    protected $userRepo;
    protected $accountInfo;

    public function __construct(UserRepository $userRepo, AccountInfo $accountInfo)
    {
        $this->userRepo = $userRepo;
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

        $fromICC = $transaction->getFromAccount()['id'];
        $emailTo = $transaction->getToAccount()['email'];
        $toICC = $transaction->getToAccount()['id']; 

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
                if($e->errorCode == 'ENTITY_NOT_FOUND'){
                    $this->context->buildViolation('Le compte débiteur n\'existe pas ou ne vous appartient pas')
                        ->atPath('fromAccount')                                        
                        ->addViolation();                                              
                }else{
                    throw $e;
                }
            }
        }

        if(! ($toICC || $emailTo)){ 
            $this->context->buildViolation('Sélectionnez au moins l\'email ou l\'ICC.')
                ->atPath('toAccount')                                          
                ->addViolation();                                              
        }else{ //email or ICC provided
            if($toICC){
                try{
                    $account = $this->accountInfo->getAccountByID($toICC);

                }catch(\Exception $e){
                    if($e->errorCode == 'ENTITY_NOT_FOUND'){
                        $this->context->buildViolation('Le compte créditeur n\'existe pas')
                            ->atPath('toAccount')                                        
                            ->addViolation();                                              
                    }
                }
            }else{ //email provided
                $creditorUser = $this->userRepo->findOneBy(array('email'=>$emailTo));
                if(!$creditorUser){//invalid email
                    $this->context->buildViolation('Aucun membre avec email '.$emailTo)
                        ->atPath('toAccount')                                          
                        ->addViolation();                                              
                }else{//valid email
                    $account = $this->accountInfo->getDefaultAccount($creditorUser->getID());
                    $toICC = $account->id;
                }
            }


            if($fromICC == $toICC){
                $this->context->buildViolation('Les comptes débiteur et créditeur sont identiques')
                    ->atPath('toAccount')                                          
                    ->addViolation();                                              
            }

        } 


    }
}
