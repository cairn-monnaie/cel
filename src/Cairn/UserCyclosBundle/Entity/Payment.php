<?php

namespace Cairn\UserCyclosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="Cairn\UserCyclosBundle\Repository\PaymentRepository")
 */
class Payment
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
     * @ORM\Column(name="cyclosID", type="bigint", unique=true)
     */
    private $cyclosID;

    /**
     * @var TransactionCategory
     * @ORM\ManyToOne(targetEntity="Cairn\UserCyclosBundle\Entity\TransactionCategory", cascade={"persist"})
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=20, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text", nullable=true)
     */
    private $reason;

    /**
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     */
    private $amount;

    /**
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */ 
    private $date;

    //    /**
    //     *@var int
    //
    //     * @ORM\Column(name="activeID", type="integer", nullable=false)
    //     */
    //    private $activeID;
    //
    //    /**
    //     *@var int
    //
    //     * @ORM\Column(name="passiveID", type="integer", nullable=false)
    //     */
    //    private $passiveID;

    public function getAmount()
    {
        return $this->getCategory()->getNbCairns() + $this->getCategory()->getNbEuros();
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
     * Set cyclosID
     *
     * @param integer $cyclosID
     *
     * @return Payment
     */
    public function setCyclosID($cyclosID)
    {
        $this->cyclosID = $cyclosID;

        return $this;
    }

    /**
     * Get cyclosID
     *
     * @return int
     */
    public function getCyclosID()
    {
        return $this->cyclosID;
    }


    /**
     * Set title
     *
     * @param string $title
     *
     * @return Payment
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return Payment
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
     * Set category
     *
     * @param \Cairn\UserCyclosBundle\Entity\TransactionCategory $category
     *
     * @return Payment
     */
    public function setCategory(\Cairn\UserCyclosBundle\Entity\TransactionCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Cairn\UserCyclosBundle\Entity\TransactionCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    //    /**
    //     * Set activeID
    //     *
    //     * @param integer $activeID
    //     *
    //     * @return Payment
    //     */
    //    public function setActiveID($activeID)
    //    {
    //        $this->activeID = $activeID;
    //
    //        return $this;
    //    }
    //
    //    /**
    //     * Get activeID
    //     *
    //     * @return integer
    //     */
    //    public function getActiveID()
    //    {
    //        return $this->activeID;
    //    }
    //
    //    /**
    //     * Set passiveID
    //     *
    //     * @param integer $passiveID
    //     *
    //     * @return Payment
    //     */
    //    public function setPassiveID($passiveID)
    //    {
    //        $this->passiveID = $passiveID;
    //
    //        return $this;
    //    }
    //
    //    /**
    //     * Get passiveID
    //     *
    //     * @return integer
    //     */
    //    public function getPassiveID()
    //    {
    //        return $this->passiveID;
    //    }
}
