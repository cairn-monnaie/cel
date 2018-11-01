<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Cairn\UserBundle\Entity\Transaction;

/**
 * Recurring Transaction
 *
 */
class RecurringTransaction extends Transaction
{

    /**
     * @var \DateTime
     *
     */
    private $firstOccurrenceDate;

    /**
     * @var \DateTime
     *
     */
    private $lastOccurrenceDate;

    /**
     * @var string
     *
     */
    private $periodicity;

    public function __construct()
    {
        $this->firstOccurrenceDate = new \Datetime('today');
        $this->lastOccurrenceDate = date_modify(new \Datetime($this->getFirstOccurrenceDate()->format('Y-m-d')), '+ 1 months');
        $this->periodicity = '1';
    }

    /**
     *
     *@Assert\Callback() 
     */
    public function isRecurringTransactionValid(ExecutionContextInterface $context)
    {
        $today = new \Datetime('today');
        if($today->diff($this->getFirstOccurrenceDate())->invert == 1){
            $context->buildViolation('La date de première échéance ne peut être antérieure à la date du jour')
                ->atPath('firstOccurrenceDate')
                ->addViolation();
        }

        $interval = $this->getFirstOccurrenceDate()->diff($this->getLastOccurrenceDate());
        $monthsDiff = $interval->m;

        if($interval->invert == 1){
            $context->buildViolation('La date de dernière échéance ne peut être antérieure à celle de la première.')
                ->atPath('lastOccurrenceDate')
                ->addViolation();
        }

        $availablePeriodicities = array('1','2','3','6','12');
        if(!in_array($this->getPeriodicity(),$availablePeriodicities)){
            $context->buildViolation('Périodicité invalide.')
                ->atPath('periodicity')
                ->addViolation();
        } 

        if($interval->invert == 0){                                            
            $nbOccurrences = intdiv($monthsDiff, intval($this->getPeriodicity())) + 1;
            if($nbOccurrences <= 1){                                           
                $context->buildViolation('La période entre les 2 dates n\'est pas assez importante : ' .$nbOccurrences. ' occurrence calculée.'    )->atPath('lastOccurrenceDate')->addViolation();
                    
            }                                                                  
        }

    }

    /**
     * Set first occurrence date
     *
     * @param \DateTime $date
     *
     * @return RecurringTransaction
     */
    public function setFirstOccurrenceDate($date)
    {
        $this->firstOccurrenceDate = $date;

        return $this;
    }

    /**
     * Get first occurrence date
     *
     * @return \DateTime
     */
    public function getFirstOccurrenceDate()
    {
        return $this->firstOccurrenceDate;
    }

    /**
     * Set last occurrence date
     *
     * @param \DateTime $date
     *
     * @return RecurringTransaction
     */
    public function setLastOccurrenceDate($date)
    {
        $this->lastOccurrenceDate = $date;

        return $this;
    }

    /**
     * Get last occurrence date
     *
     * @return \DateTime
     */
    public function getLastOccurrenceDate()
    {
        return $this->lastOccurrenceDate;
    }

    /**
     * Set periodicity
     *
     * @param string $periodicity
     *
     * @return RecurringTransaction
     */
    public function setPeriodicity($periodicity)
    {
        $this->periodicity = $periodicity;

        return $this;
    }


    /**
     *Get periodicity
     *
     *@return string
     */ 
    public function getPeriodicity()
    {
        return $this->periodicity;
    }   

}

