<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Transaction
 *
 */
class Transaction
{

    /**
     * @var string
     *
     */
    private $amount;

    /**
     * @var string
     *
     */
    private $description;

    /**
     * @var \stdClass
     *
     */
    private $fromAccount;

    /**
     * @var \stdClass
     *
     * @Assert\Valid(traverse = true)
     *      
     */
    private $toAccount;


    /**
     *
     *@Assert\Callback() 
     */
    public function isTransactionValid(ExecutionContextInterface $context)
    {
        if($this->getAmount() < 0.01){
            $context->buildViolation('Montant trop faible : doit être supérieur à 0.01 cairn')
                ->atPath('amount')
                ->addViolation();
        }

        if(!$this->getFromAccount()['id']){
            $context->buildViolation('Le compte débiteur n\'a pas été sélectionné')
                ->atPath('fromAccount')
                ->addViolation();
        }

        if(! ($this->getToAccount()['id'] || $this->getToAccount()['email'])){
            $context->buildViolation('Sélectionnez au moins l\'email ou l\'ICC.')
                ->atPath('toAccount')
                ->addViolation();
        }


    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return Transaction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set fromAccount
     *
     * @param \stdClass $fromAccount
     *
     * @return Transaction
     */
    public function setFromAccount($fromAccount)
    {
        $this->fromAccount = $fromAccount;

        return $this;
    }

    /**
     * Get fromAccount
     *
     * @return \stdClass
     */
    public function getFromAccount()
    {
        return $this->fromAccount;
    }

    /**
     * Set toAccount
     *
     * @param \stdClass $toAccount
     *
     * @return Transaction
     */
    public function setToAccount($toAccount)
    {
        $this->toAccount = $toAccount;

        return $this;
    }

    /**
     * Get toAccount
     *
     * @return \stdClass
     */
    public function getToAccount()
    {
        return $this->toAccount;
    }
}

