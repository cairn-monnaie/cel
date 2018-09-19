<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banknote
 *
 * @ORM\Table(name="banknote")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\BanknoteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Banknote
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
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

    /**
     * @var Cairn\UserBundle\Entity\BanknoteStatus
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\BanknoteStatus" , cascade = {"persist"})
     *@ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUpdate", type="datetime")
     */
    private $lastUpdate;


    public function __construct()
    {
        $this->lastUpdate = new \Datetime();
    }

    /**
     *
     *@ORM\PreUpdate
     */ 
    public function updateTime()
    {
        $this->date = new \Datetime();
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
     * Set number
     *
     * @param integer $number
     *
     * @return Banknote
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Banknote
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Set status
     *
     * @param \Cairn\UserBundle\Entity\BanknoteStatus $status
     *
     * @return Banknote
     */
    public function setStatus(\Cairn\UserBundle\Entity\BanknoteStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Cairn\UserBundle\Entity\BanknoteStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Banknote
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }
}
