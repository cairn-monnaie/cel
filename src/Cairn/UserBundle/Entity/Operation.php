<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Operation
 *
 * @ORM\Table(name="operation")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\OperationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Operation
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentID", type="string", length=25, unique=true, nullable=true)
     */
    private $paymentID;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submissionDate", type="datetime")
     */
    private $submissionDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="executionDate", type="datetime")
     */
    private $executionDate;

   /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string")
     */
    private $reason;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="fromAccountNumber", type="string", length=10)
     */
    private $fromAccountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="toAccountNumber", type="string", length=10)
     */
    private $toAccountNumber;

    /**
     * @var \Cairn\UserBundle\Entity\User
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\User", cascade={"persist"})
     *@ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var array
     */
    private $fromAccount;

    /**
     * @var array
     */
    private $toAccount;

    public static $TYPE_TRANSACTION_EXECUTED = 0;
#    public static $TYPE_TRANSACTION_RECURRING = 1;
    public static $TYPE_TRANSACTION_SCHEDULED = 2;
    public static $TYPE_CONVERSION = 3;
    public static $TYPE_RECONVERSION = 4;
    public static $TYPE_DEPOSIT = 5;
    public static $TYPE_WITHDRAWAL = 6;
    public static $TYPE_SCHEDULED_FAILED = 7;


    /**
     *
     *@Assert\Callback() 
     */
    public function isOperationValid(ExecutionContextInterface $context)
    {
        $today = new \Datetime('today');
        if($today->diff($this->getExecutionDate())->invert == 1){
            $context->buildViolation('La date d\'exécution ne peut être antérieure à la date du jour')
                ->atPath('executionDate')
                ->addViolation();
        }

        if( strlen($this->getReason()) > 35){
            $context->buildViolation('Motif trop long : 35 caractères maximum')
                ->atPath('reason')
                ->addViolation();
        }
    }

    public function __construct()
    {
        $today = new \Datetime();
        $this->setSubmissionDate($today);
        $this->setExecutionDate($today);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Operation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set paymentID
     *
     * @param string $paymentID
     *
     * @return Operation
     */
    public function setPaymentID($paymentID)
    {
        $this->paymentID = $paymentID;

        return $this;
    }

    /**
     * Get paymentID
     *
     * @return string
     */
    public function getPaymentID()
    {
        return $this->paymentID;
    }

    /**
     * Set submissionDate
     *
     * @param \DateTime $submissionDate
     *
     * @return Operation
     */
    public function setSubmissionDate($submissionDate)
    {
        $this->submissionDate = $submissionDate;

        return $this;
    }

    /**
     * Get submissionDate
     *
     * @return \DateTime
     */
    public function getSubmissionDate()
    {
        return $this->submissionDate;
    }

    /**
     * Set executionDate
     *
     * @param \DateTime $executionDate
     *
     * @return Operation
     */
    public function setExecutionDate($executionDate)
    {
        $this->executionDate = $executionDate;

        return $this;
    }

    /**
     * Get executionDate
     *
     * @return \DateTime
     */
    public function getExecutionDate()
    {
        return $this->executionDate;
    }

    /**
     * Set updatedAt
     *
     * @ORM\PostLoad
     *
     * @param \DateTime $updatedAt
     * @return Operation
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \Datetime();

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Operation
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
     * Set reason
     *
     * @param string $reason
     *
     * @return Operation
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Operation
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set fromAccountNumber
     *
     * @param string $fromAccountNumber
     *
     * @return Operation
     */
    public function setFromAccountNumber($fromAccountNumber)
    {
        $this->fromAccountNumber = $fromAccountNumber;

        return $this;
    }

    /**
     * Get fromAccountNumber
     *
     * @return string
     */
    public function getFromAccountNumber()
    {
        return $this->fromAccountNumber;
    }

    /**
     * Set toAccountNumber
     *
     * @param string $toAccountNumber
     *
     * @return Operation
     */
    public function setToAccountNumber($toAccountNumber)
    {
        $this->toAccountNumber = $toAccountNumber;

        return $this;
    }

    /**
     * Get toAccountNumber
     *
     * @return string
     */
    public function getToAccountNumber()
    {
        return $this->toAccountNumber;
    }

    /**
     * Set user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return User
     */
    public function setUser(\Cairn\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Cairn\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set fromAccount
     *
     * @param array $fromAccount
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
     * @return array
     */
    public function getFromAccount()
    {
        return $this->fromAccount;
    }

    /**
     * Set toAccount
     *
     * @param array $toAccount
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
     * @return array
     */
    public function getToAccount()
    {
        return $this->toAccount;
    }

}

