<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BanknoteStatus
 *
 * @ORM\Table(name="banknote_status")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\BanknoteStatusRepository")
 */
class BanknoteStatus
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
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status;

    /**
     * @var Cairn\UserBundle\Entity\User
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\User")
     *@ORM\JoinColumn(nullable=false)
     */
    private $exchangeOffice;

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
     * Set status
     *
     * @param string $status
     *
     * @return BanknoteStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }



    /**
     * Set exchangeOffice
     *
     * @param \Cairn\UserBundle\Entity\User $exchangeOffice
     *
     * @return BanknoteStatus
     */
    public function setExchangeOffice(\Cairn\UserBundle\Entity\User $exchangeOffice)
    {
        $this->exchangeOffice = $exchangeOffice;

        return $this;
    }

    /**
     * Get exchangeOffice
     *
     * @return \Cairn\UserBundle\Entity\User
     */
    public function getExchangeOffice()
    {
        return $this->exchangeOffice;
    }
}
