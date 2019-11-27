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
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="fromAccountNumber", type="string", length=25)
     */
    private $fromAccountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="toAccountNumber", type="string", length=25)
     */
    private $toAccountNumber;

     /**
     * @var \Cairn\UserBundle\Entity\User
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\User", cascade={"persist"})
     *@ORM\JoinColumn(name="creditor_id", nullable=true,referencedColumnName="id", onDelete="SET NULL")
     */
    private $creditor;

    /**
     * @var string
     *
     *@ORM\Column(name="creditorName", type="string", length=50)
     */
    private $creditorName;

     /**
     * @var \Cairn\UserBundle\Entity\User
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\User", cascade={"persist"})
     *@ORM\JoinColumn(name="debitor_id", nullable=true,referencedColumnName="id", onDelete="SET NULL")
     */
    private $debitor;

    /**
     * @var string
     *
     *@ORM\Column(name="debitorName", type="string", length=50)
     */
    private $debitorName;

    /**
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\Mandate", inversedBy="operations" )
     *@ORM\JoinColumn(nullable=true)
     */
    private $mandate;

    /**
     * @var string
     *
     * @ORM\Column(name="recurringID", type="string", length=25, nullable=true)
     */
    private $recurringID;

    /**
     * @var array
     */
    private $fromAccount;

    /**
     * @var array
     */
    private $toAccount;

    //WARNING : CURRENT VALUES SHOULD NOT BE CHANGED ! THIS WOULD MAKE ANY FILTERING OPERATION FAIL
    const TYPE_TRANSACTION_EXECUTED = 0;
    const TYPE_TRANSACTION_RECURRING = 1;
    const TYPE_TRANSACTION_SCHEDULED = 2;
    const TYPE_CONVERSION_BDC = 3;
    const TYPE_CONVERSION_HELLOASSO = 4;
    const TYPE_DEPOSIT = 5;
    const TYPE_WITHDRAWAL = 6;
    const TYPE_SCHEDULED_FAILED = 7;
    const TYPE_SMS_PAYMENT = 8;
    const TYPE_MANDATE = 9;
    const TYPE_ONLINE_PAYMENT = 10;
    const TYPE_RECONVERSION = 11;
    const TYPE_MOBILE_APP = 12;
    const TYPE_DIRECT_DEBITING = 13;


    const ARRAY_EXECUTED_TYPES = array(self::TYPE_SMS_PAYMENT,self::TYPE_TRANSACTION_EXECUTED,self::TYPE_WITHDRAWAL,self::TYPE_DEPOSIT,self::TYPE_CONVERSION_BDC,self::TYPE_CONVERSION_HELLOASSO, self::TYPE_ONLINE_PAYMENT, self::TYPE_RECONVERSION);

    /*
     * All types which involve two adherents
     */
    const ARRAY_TRANSFER_TYPES = array(self::TYPE_SMS_PAYMENT,self::TYPE_TRANSACTION_EXECUTED, self::TYPE_TRANSACTION_SCHEDULED,self::TYPE_TRANSACTION_RECURRING, self::TYPE_ONLINE_PAYMENT);


    public function isSmsPayment()
    {
        return ($this->getType() == self::TYPE_SMS_PAYMENT) ;
    }

    public static function getTypeIndex($typeName)
    {
        switch ($typeName){
        case "TRANSACTION_EXECUTED":
            return self::TYPE_TRANSACTION_EXECUTED;
            break;
        case "TRANSACTION_RECURRING":
            return self::TYPE_TRANSACTION_RECURRING;
            break;
        case "TRANSACTION_SCHEDULED":
            return self::TYPE_TRANSACTION_SCHEDULED;
            break;
        case "CONVERSION_BDC":
            return  self::TYPE_CONVERSION_BDC;
            break;
        case "CONVERSION_HELLOASSO":
            return  self::TYPE_CONVERSION_HELLOASSO;
            break;
        case "DEPOSIT":
            return self::TYPE_DEPOSIT;
            break;
        case "WITHDRAWAL":
            return self::TYPE_WITHDRAWAL;
            break;
        case "SCHEDULED_FAILED":
            return self::TYPE_SCHEDULED_FAILED ;
            break;
        case "SMS_PAYMENT":
            return self::TYPE_SMS_PAYMENT;
            break;
        case "ONLINE_PAYMENT":
            return self::TYPE_ONLINE_PAYMENT;
            break;
        default:
            return NULL;
        }
    }

    public static function getTypeName($typeIndex)
    {
        switch ($typeIndex){
        case "0":
            return 'transaction';
            break;
        case "1":
            return 'recurring transaction';
            break;
        case "2":
            return 'scheduled transaction';
            break;
        case "3":
            return 'conversion en bureau de change';
            break;
        case "4":
            return 'conversion par virement Helloasso';
            break;
        case "5":
            return 'deposit';
            break;
        case "6":
            return 'withdrawal';
            break;
        case "7":
            return 'failed transaction';
            break;
        case "8":
            return 'sms payment';
            break;
        case "9":
            return 'payment order';
            break;
        case "10":
            return 'online payment';
            break;
        case "11":
            return 'reconversion';
            break;
        default:
            return NULL;
        }
    }

    public static function getB2CTypes()
    {
        return array(self::TYPE_SMS_PAYMENT,self::TYPE_TRANSACTION_EXECUTED);
    }

    public static function getDebitOperationTypes()
    {
        return array(self::TYPE_WITHDRAWAL,self::TYPE_RECONVERSION);
    }

    public static function getToOperationTypes()
    {
        return array(self::TYPE_DEPOSIT,self::TYPE_CONVERSION_BDC,self::TYPE_CONVERSION_HELLOASSO);
    }

    /**
     * Types which can lead to desynchronization because of other information system services
     *
     * There are several services (docker meaning) connected to each other. A broken connection between two services can lead to desynchronized data.
     * For instance, if a deposit is done on BDC application and the connection to CEL app is broken, the user account balance will change, but the operation won't appear on
     * user CEL dashboard
     *
     */
    public static function getPotentiallyDesynchronizedTypes()
    {
        return array(self::TYPE_WITHDRAWAL,self::TYPE_DEPOSIT,self::TYPE_CONVERSION_BDC);
    }

    /**
     * Propose TYPE_MANDATE in the list or not
     * 
     * If user is PRO, do propose reconversion field
     */
    public static function getExecutedTypes($withMandate = NULL, $asPro = false)
    {
        $types =  self::ARRAY_EXECUTED_TYPES;

        if($asPro){
            $types[] = self::TYPE_RECONVERSION;
        }

        if($withMandate){
            $types[] = self::TYPE_MANDATE;
        }
        return $types;
    }

    public static function getScheduledTypes()
    {
        return array(self::TYPE_TRANSACTION_SCHEDULED,self::TYPE_TRANSACTION_RECURRING );
    }

    
    public function __construct()
    {
        $today = new \Datetime();
        $this->setSubmissionDate($today);
        $this->setExecutionDate($today);
        $this->setType(self::TYPE_TRANSACTION_EXECUTED);
    }

    // same idea than a copy constructor
    public static function copyFrom(Operation $operation)
    {
        $copy = clone $operation;
        $copy->setPaymentID(NULL);
        $copy->setRecurringID(NULL);

        return $copy;        
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
     * Set recurringID
     *
     * @param string $recurringID
     *
     * @return Operation
     */
    public function setRecurringID($recurringID)
    {
        $this->recurringID = $recurringID;

        return $this;
    }

    /**
     * Get recurringID
     *
     * @return string
     */
    public function getRecurringID()
    {
        return $this->recurringID;
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
     * @param float $amount
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
     * @return float
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
     * Set creditor
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return Operation
     */
    public function setCreditor(\Cairn\UserBundle\Entity\User $user = NULL)
    {
        $this->creditor = $user;

        if($user){
            $this->creditorName = $user->getName();
        }

        return $this;
    }

    /**
     * Get creditor
     *
     * @return \Cairn\UserBundle\Entity\User
     */
    public function getCreditor()
    {
        return $this->creditor;
    }

    /**
     * Set creditor's name
     *
     * @param string
     *
     * @return Operation
     */
    public function setCreditorName($name)
    {
        $this->creditorName = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getCreditorName()
    {
        return $this->creditorName;
    }

    /**
     * Set debitor
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return Operation
     */
    public function setDebitor(\Cairn\UserBundle\Entity\User $user = NULL)
    {
        $this->debitor = $user;

        if($user){
            $this->debitorName = $user->getName();
        }

        return $this;
    }

    /**
     * Get debitor
     *
     * @return \Cairn\UserBundle\Entity\User
     */
    public function getDebitor()
    {
        return $this->debitor;
    }

    /**
     * Set debitor's name
     *
     * @param string
     *
     * @return Operation
     */
    public function setDebitorName($name)
    {
        $this->debitorName = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getDebitorName()
    {
        return $this->debitorName;
    }

    /**
     * Set mandate if any
     *
     *
     * @return Mandate
     */
    public function setMandate($mandate)
    {
        $this->mandate = $mandate;

        return $this;
    }

    /**
     * Get mandate
     *
     * @return Mandate
     */
    public function getMandate()
    {
        return $this->mandate;
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

