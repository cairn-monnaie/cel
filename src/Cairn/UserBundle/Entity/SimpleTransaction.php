<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Cairn\UserBundle\Entity\Transaction;
/**
 * Simple Transaction
 *
 */
class SimpleTransaction extends Transaction
{

    /**
     * @var \DateTime
     *
     */
    private $date;

    /**
     *
     *@Assert\Callback() 
     */
    public function isSimpleTransactionValid(ExecutionContextInterface $context)
    {
        $today = new \Datetime('today');
        if($today->diff($this->getDate())->invert == 1){
            $context->buildViolation('La date d\'exécution ne peut être antérieure à la date du jour')
                ->atPath('date')
                ->addViolation();
        }
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return SimpleTransaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

}

