<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OnlinePayment
 *
 * @ORM\Table(name="online_payment")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\OnlinePaymentRepository")
 */
class OnlinePayment
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
     * @var string
     *
     * @ORM\Column(name="urlSuccess", type="string", length=255)
     */
    private $urlSuccess;

    /**
     * @var string
     *
     * @ORM\Column(name="urlFailure", type="string", length=255)
     */
    private $urlFailure;

    /**
     * @var string
     *
     * @ORM\Column(name="accountNumber", type="string", length=15)
     */
    private $accountNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="invoiceID", type="string", length=255, unique=true)
     */
    private $invoiceID;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=255)
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="urlValidationSuffix", type="string", length=255, unique=true)
     */
    private $urlValidationSuffix;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set urlSuccess.
     *
     * @param string $urlSuccess
     *
     * @return OnlinePayment
     */
    public function setUrlSuccess($urlSuccess)
    {
        $this->urlSuccess = $urlSuccess;

        return $this;
    }

    /**
     * Get urlSuccess.
     *
     * @return string
     */
    public function getUrlSuccess()
    {
        return $this->urlSuccess;
    }

    /**
     * Set urlFailure.
     *
     * @param string $urlFailure
     *
     * @return OnlinePayment
     */
    public function setUrlFailure($urlFailure)
    {
        $this->urlFailure = $urlFailure;

        return $this;
    }

    /**
     * Get urlFailure.
     *
     * @return string
     */
    public function getUrlFailure()
    {
        return $this->urlFailure;
    }

    /**
     * Set accountNumber.
     *
     * @param string $accountNumber
     *
     * @return OnlinePayment
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get accountNumber.
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set amount.
     *
     * @param float $amount
     *
     * @return OnlinePayment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set invoiceID.
     *
     * @param string $invoiceID
     *
     * @return OnlinePayment
     */
    public function setInvoiceID($invoiceID)
    {
        $this->invoiceID = $invoiceID;

        return $this;
    }

    /**
     * Get invoiceID.
     *
     * @return string
     */
    public function getInvoiceID()
    {
        return $this->invoiceID;
    }

    /**
     * Set reason.
     *
     * @param string $reason
     *
     * @return OnlinePayment
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set urlValidationSuffix.
     *
     * @param string $urlValidationSuffix
     *
     * @return OnlinePayment
     */
    public function setUrlValidationSuffix($urlValidationSuffix)
    {
        $this->urlValidationSuffix = $urlValidationSuffix;

        return $this;
    }

    /**
     * Get urlValidationSuffix.
     *
     * @return string
     */
    public function getUrlValidationSuffix()
    {
        return $this->urlValidationSuffix;
    }
}
