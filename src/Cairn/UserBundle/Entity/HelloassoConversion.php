<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HelloassoConversion
 *
 * @ORM\Table(name="helloasso_conversion")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\HelloassoConversionRepository")
 */
class HelloassoConversion
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
     * @ORM\Column(name="paymentID", type="string", length=255, unique=true)
     */
    private $paymentID;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="creditorName", type="string", length=255)
     */
    private $creditorName;


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
     * Set paymentID.
     *
     * @param string $paymentID
     *
     * @return HelloassoConversion
     */
    public function setPaymentID($paymentID)
    {
        $this->paymentID = $paymentID;

        return $this;
    }

    /**
     * Get paymentID.
     *
     * @return string
     */
    public function getPaymentID()
    {
        return $this->paymentID;
    }

    /**
     * Set amount.
     *
     * @param float $amount
     *
     * @return HelloassoConversion
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
     * Set email.
     *
     * @param string $email
     *
     * @return HelloassoConversion
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return HelloassoConversion
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set creditorName.
     *
     * @param string $creditorName
     *
     * @return HelloassoConversion
     */
    public function setCreditorName($creditorName)
    {
        $this->creditorName = $creditorName;

        return $this;
    }

    /**
     * Get creditorName.
     *
     * @return string
     */
    public function getCreditorName()
    {
        return $this->creditorName;
    }
}
