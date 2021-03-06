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

    /**
     * Validates the data from the account doing the current action
     *
     * @param stdClass $account Object containing cyclos account data like account number and email
     * @param string $path Form field to display error at
     *
     */
    private function validateActiveAccount($account,$path)
    {
        if(is_scalar($account)){
            $this->context->buildViolation('account.not_found')
                ->setInvalidValue($account)
                ->atPath($path)                                        
                ->addViolation();                                              

            return;
        }

        $ICC = $account->number;
        if(!$ICC){                                    
            $this->context->buildViolation('account.not_found')
                ->atPath($path)                                        
                ->addViolation();                                              
        }else{ //ICC provided
            try{
                $account = $this->accountInfo->getAccountByNumber($ICC);
                if(!$account){
                    $this->context->buildViolation('account.not_found')
                        ->atPath($path)                                        
                        ->addViolation();                                              
                }
            }catch(\Exception $e){
                if($e->errorCode == 'ENTITY_NOT_FOUND' || $e->errorCode == 'NULL_POINTER'){
                    $this->context->buildViolation('account.not_found')
                        ->atPath($path)                                        
                        ->addViolation();                                              
                }else{
                    throw $e;
                }
            }
        }
    }


    /**
     * Validates the data from the account subject to current action, but not executing it
     *
     * @param stdClass $account Object containing cyclos account data like account number and email
     * @param string $path Form field to display error at
     *
     */
    private function validatePassiveAccount($account,$path)
    {
        if(is_scalar($account)){
            $this->context->buildViolation('account.not_found')
                ->setInvalidValue($account)
                ->atPath($path)                                        
                ->addViolation();                                              

            return;
        }

        $ICC = $account->number;

        if(! $ICC ){ 
            $this->context->buildViolation('account.not_found')
                ->atPath($path)                                          
                ->addViolation();                                              
        }else{

            $user = NULL;
            if($ICC){
                $userVO = $this->userInfo->getUserVOByKeyword($ICC);

                if(!$userVO){
                    $this->context->buildViolation('account.not_found')
                        ->atPath($path)                                          
                        ->addViolation();                                              
                    return;
                }
                $user = $this->userRepo->findOneBy(array('username'=>$userVO->username));
            }

            if($user && $user->getRemovalRequest()){
                $this->context->buildViolation('account.disabled')
                    ->setInvalidValue($user->getEmail())
                    ->atPath($path)                                          
                    ->addViolation();                                              
            }
        }
    }

    /**
     * Validates that account $account has available balance to perform operation of amount $amount
     *
     * @param const int $operationType Type of the current operation
     * @param stdClass $account Debitor account
     * @param float $amount Payment amount
     */
    private function validateBalance($operationType, $account,$amount)
    {
        if(!$account->unlimited){
            if($account->status->availableBalance < $amount){
                if($operationType == Operation::TYPE_SMS_PAYMENT){
                    $message = 'Solde insuffisant : Votre solde actuel est de '.$account->status->availableBalance;
                }else{
                    $message = 'op.amount_too_high';
                }
                $this->context->buildViolation($message)
                    ->setInvalidValue($amount)
                    ->atPath('amount')
                    ->addViolation();
            }
        }
    }

    /**
     * Validates the current Operation data
     *
     * The validation process depends on the operation type
     */
    public function validate($operation, Constraint $constraint)
    {
        //************ Common validation, independent of operation type ************//
        if($operation->getAmount() < 0.01){
            $message = 'Montant indiqué trop faible';
            if($operation->isSmsPayment()){
                $this->context->addViolation($message);
            }else{
                $this->context->buildViolation('op.amount_too_low')
                    ->setInvalidValue($operation->getAmount())
                    ->atPath('amount')
                    ->addViolation();
            }
        }

        $today = new \Datetime('today');

        $clone = clone $today;
        $inconsistentLimitDate = $clone->modify('+1 year');

        if($today->diff($operation->getExecutionDate())->invert == 1){
            $this->context->buildViolation('op.date_before_today')
                ->setInvalidValue($operation->getExecutionDate()->format('d-m-Y'))
                ->atPath('executionDate')
                ->addViolation();
        }

        if($operation->getExecutionDate()->diff($inconsistentLimitDate)->invert == 1){
            $this->context->buildViolation('Cette date est incohérente ! Plus d\'un an avant l\éxecution de cette opération...')
                ->setCode('inconsistent_data')
                ->setInvalidValue($operation->getExecutionDate()->format('d-m-Y'))
                ->atPath('executionDate')
                ->addViolation();
        }

        if( strlen($operation->getReason()) > 35){
            //TODO : set invalid value
            $this->context->buildViolation('Motif trop long : 35 caractères maximum')
                ->setCode('too_many_chars')
                ->setInvalidValue('Le motif')
                ->atPath('reason')
                ->addViolation();
        }


        //************ Specific validation, anything but SMS payment ************//
        if(! $operation->isSmsPayment()){
            $array_transaction_types = array(Operation::TYPE_MOBILE_APP,Operation::TYPE_TRANSACTION_EXECUTED,Operation::TYPE_TRANSACTION_SCHEDULED);

            if(in_array($operation->getType(),Operation::getDebitOperationTypes()) || in_array($operation->getType(), $array_transaction_types)){

                //the account to debit on cyclos-side is "fromAccount". Therefore, we must ensure that the debitor account exists and
                //then, that the balance is sufficient to make the payment
                $this->validateActiveAccount($operation->getFromAccount(),'fromAccount');
                if(count($this->context->getViolations()) == 0){
                    $account = $this->accountInfo->getAccountByNumber($operation->getFromAccount()->number);
                    $this->validateBalance($operation->getType(), $account,$operation->getAmount());
                }


                if(in_array($operation->getType(),$array_transaction_types)){
                    $this->validatePassiveAccount($operation->getToAccount(),'toAccount');
                }
            }else{
                //we ensure that creditor account exists
                $this->validateActiveAccount($operation->getToAccount(),'toAccount');
                $this->validatePassiveAccount($operation->getFromAccount(),'fromAccount');
                if(count($this->context->getViolations()) == 0){
                    $account = $this->accountInfo->getAccountByNumber($operation->getFromAccount()['number']);
                    $this->validateBalance($operation->getType(), $account,$operation->getAmount());
                }

            }
            //************ Specific validation, SMS payment ************//
        }else{ 
            $debitorUser = $operation->getDebitor();
            $creditorUser = $operation->getCreditor();

            if(! $debitorUser->isEnabled()){
                $message = 'Votre compte [e]-Cairn est inactif';
                $this->context->addViolation($message);
            }

            if($debitorUser === $creditorUser){
                $this->context->addViolation('Comptes débiteur et créditeur identiques');
            }

            if(count($this->context->getViolations()) == 0){
                $account = $this->accountInfo->getDefaultAccount($debitorUser->getCyclosID());
                $this->validateBalance($operation->getType(), $account,$operation->getAmount());
            }

        }
    } 


}
