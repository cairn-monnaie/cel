<?php
// src/Cairn/UserBundle/Validator/OperationValidator.php

namespace Cairn\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Service\AccountInfo;
use Cairn\UserCyclosBundle\Service\UserInfo;
use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserBundle\Entity\Operation;

class OperationValidator extends ConstraintValidator
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

    private function validateActiveAccount($account,$path)
    {

        $ICC = $account['accountNumber'];
        if(!$ICC){                                    
            $this->context->buildViolation('Le compte n\'a pas été sélectionné')
                ->atPath($path)                                        
                ->addViolation();                                              
        }else{ //ICC provided
            try{
                $account = $this->accountInfo->getAccountByNumber($ICC);
                if(!$account){
                    $this->context->buildViolation('Compte introuvable')
                        ->atPath($path)                                        
                        ->addViolation();                                              
                }
            }catch(\Exception $e){
                if($e->errorCode == 'ENTITY_NOT_FOUND' || $e->errorCode == 'NULL_POINTER'){
                    $this->context->buildViolation('Compte introuvable')
                        ->atPath($path)                                        
                        ->addViolation();                                              
                }else{
                    throw $e;
                }
            }
        }
    }

    private function validatePassiveAccount($account,$path){
        $email = $account['email'];
        $ICC = $account['accountNumber']; 

        if(! ($ICC || $email)){ 
            $this->context->buildViolation('Sélectionnez au moins l\'email ou l\'ICC.')
                ->atPath($path)                                          
                ->addViolation();                                              
        }else{ //email and ICC provided

            $user = NULL;
            if($ICC){
                $userVO = $this->userInfo->getUserVOByKeyword($ICC);

                if(!$userVO){
                    $this->context->buildViolation('ICC introuvable')
                        ->atPath($path)                                          
                        ->addViolation();                                              
                }else{
//                    if($fromICC == $toICC){
//                        $this->context->buildViolation('Les comptes débiteur et créditeur sont identiques')
//                            ->atPath($path)                                          
//                            ->addViolation();                                              
//                    }

                    $user = $this->userRepo->findOneBy(array('username'=>$userVO->username));
                }
            }
            if($email){
                $user = $this->userRepo->findOneBy(array('email'=>$email));

                if(!$user){//invalid email
                    $this->context->buildViolation('Aucun membre avec email '.$email)
                        ->atPath($path)                                          
                        ->addViolation();                                              
                }
            }

            if($user && $user->getRemovalRequest()){
                $this->context->buildViolation($user->getName().' est en phase de suppression. Vous ne pouvez donc pas effectuer cette opération')
                    ->atPath($path)                                          
                    ->addViolation();                                              
            }
            if($ICC && $email){
                if($userVO && $user){
                    if(! ($user->getUsername() == $userVO->username)){//email not provided so we use username instead
                        $this->context->buildViolation('email et ICC ne correspondent pas')
                            ->atPath($path)                                          
                            ->addViolation();                                              
                    }


                }
            }
        }

    }

    private function validateBalance($account,$amount)
    {
        if(!$account->unlimited){
            if($account->status->availableBalance < $amount){
                $this->context->buildViolation('Montant trop élevé : modifiez-le ou rechargez votre compte.')
                    ->atPath('amount')
                    ->addViolation();
            }
        }
    }

    /**
     * Validates the provided operation information
     *@todo : case if fromICC == toICC
     */
    public function validate($operation, Constraint $constraint)
    {
        if($operation->getAmount() < 0.01){
            $this->context->buildViolation('Montant trop faible : doit être supérieur à 0.01')
                ->atPath('amount')
                ->addViolation();
        }

        $array_debitor = array(Operation::TYPE_TRANSACTION_EXECUTED,Operation::TYPE_TRANSACTION_SCHEDULED);

        if(in_array($operation->getType(),$array_debitor)){

            //the account to debit on cyclos-side is "fromAccount". Therefore, we must ensure that the debitor account exists and
            //then, that the balance is sufficient to make the payment
            $this->validateActiveAccount($operation->getFromAccount(),'fromAccount');
            if(count($this->context->getViolations()) == 0){
                $account = $this->accountInfo->getAccountByNumber($operation->getFromAccount()['accountNumber']);
                $this->validateBalance($account,$operation->getAmount());
            }
            $this->validatePassiveAccount($operation->getToAccount(),'toAccount');

        }else{
            //we ensure that creditor account exists
            $this->validateActiveAccount($operation->getToAccount(),'toAccount');
            $this->validatePassiveAccount($operation->getFromAccount(),'fromAccount');
            if(count($this->context->getViolations()) == 0){
                $account = $this->accountInfo->getAccountByNumber($operation->getFromAccount()['accountNumber']);
                $this->validateBalance($account,$operation->getAmount());
            }

        }
    } 


}
