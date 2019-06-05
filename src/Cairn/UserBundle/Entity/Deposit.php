<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deposit
 *
 * @ORM\Table(name="deposit")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\DepositRepository")
 */
class Deposit
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
     * @var \Cairn\UserBundle\Entity\User
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\User", cascade={"persist"})
     */
    private $creditor;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="requestedAt", type="datetime")
     */
    private $requestedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="executedAt", type="datetime",nullable=true)
     */
    private $executedAt;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;


    const STATE_PROCESSED = 0;
    const STATE_SCHEDULED= 1;
    const STATE_FAILED = 2;

    public function __construct(User $creditor)
    {
        $this->setCreditor($creditor);
        $this->setStatus(self::STATE_SCHEDULED);
        $this->setRequestedAt(new \Datetime());
    }

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
     * Set creditor
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return Operation
     */
    public function setCreditor(\Cairn\UserBundle\Entity\User $user = NULL)
    {
        $this->creditor = $user;

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
     * Set status.
     *
     * @param int $status
     *
     * @return Deposit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set requestedAt.
     *
     * @param \DateTime $requestedAt
     *
     * @return Deposit
     */
    public function setRequestedAt($requestedAt)
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    /**
     * Get requestedAt.
     *
     * @return \DateTime
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * Set executedAt.
     *
     * @param \DateTime $executedAt
     *
     * @return Deposit
     */
    public function setExecutedAt($executedAt)
    {
        $this->executedAt = $executedAt;

        return $this;
    }

    /**
     * Get executedAt.
     *
     * @return \DateTime
     */
    public function getExecutedAt()
    {
        return $this->executedAt;
    }

    /**
     * Set amount.
     *
     * @param float $amount
     *
     * @return Deposit
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
}
