<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Mandate
 *
 * @ORM\Table(name="mandate")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\MandateRepository")
 */

class Mandate
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
     *@ORM\JoinColumn(nullable=true)
     */
    private $contractor;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="beginAt", type="datetime")
     */
    private $beginAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endAt", type="datetime")
     */
    private $endAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var ArrayCollection
     *@ORM\OneToMany(targetEntity="Cairn\UserBundle\Entity\Operation", mappedBy="mandate" , cascade={"persist"})
     *@ORM\JoinColumn(nullable=true)
     */
    private $operations;

    const CANCELED = 0;
    const UP_TO_DATE = 1;
    const OVERDUE = 2;
    const COMPLETE = 3;
    const SCHEDULED = 4;

    const ARRAY_ALL_STATUS = array(Mandate::CANCELED,Mandate::UP_TO_DATE,Mandate::OVERDUE,Mandate::COMPLETE,Mandate::SCHEDULED);

    public function __construct()
    {
        $today = new \Datetime();
        $this->setCreatedAt($today);
        $this->setStatus(self::SCHEDULED);
        $this->operations = new ArrayCollection();
    }

    public static function getStatusName($status)
    {
        switch ($status){
        case "0":
            return 'revoked';
            break;
        case "1":
            return 'up-to-date';
            break;
        case "2":
            return 'overdue';
            break;
        case "3":
            return 'honoured';
            break;
        case "4":
            return 'scheduled';
            break;
        default:
            return NULL;
        }
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
     * Get contractor.
     *
     * @return \stdClass
     */
    public function getContractor()
    {
        return $this->contractor;
    }

    /**
     * Set contractor.
     *
     * @return \stdClass
     */
    public function setContractor($contractor)
    {
        $this->contractor = $contractor;

        return $this;
    }

    /**
     * Set amount.
     *
     * @param float $amount
     *
     * @return Mandate
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Mandate
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set beginAt.
     *
     * @param \DateTime $beginAt
     *
     * @return Mandate
     */
    public function setBeginAt($beginAt)
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    /**
     * Get beginAt.
     *
     * @return \DateTime
     */
    public function getBeginAt()
    {
        return $this->beginAt;
    }

    /**
     * Set endAt.
     *
     * @param \DateTime $endAt
     *
     * @return Mandate
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt.
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Mandate
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
     * Add operation
     *
     * @param \Cairn\UserBundle\Entity\Operation $operation
     *
     * @return User
     */
    public function addOperation(\Cairn\UserBundle\Entity\Operation $operation)
    {
        $this->operations[] = $operation;

        return $this;
    }

    
    /**
     * Get operations.
     *
     * @return \Cairn\UserBundle\Entity\Operation|null
     */
    public function getOperations()
    {
        return $this->operations;
    }
}
