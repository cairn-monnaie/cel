<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reconversion
 *
 * @ORM\Table(name="reconversion")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\ReconversionRepository")
 */
class Reconversion
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
     * @var \DateTime
     *
     * @ORM\Column(name="submissionDate", type="datetime")
     */
    private $submissionDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validationDate", type="datetime", nullable=true)
     */
    private $validationDate;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="user", type="object")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status;


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
     * @return Reconversion
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
     * Set submissionDate
     *
     * @param \DateTime $submissionDate
     *
     * @return Reconversion
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
     * Set validationDate
     *
     * @param \DateTime $validationDate
     *
     * @return Reconversion
     */
    public function setValidationDate($validationDate)
    {
        $this->validationDate = $validationDate;

        return $this;
    }

    /**
     * Get validationDate
     *
     * @return \DateTime
     */
    public function getValidationDate()
    {
        return $this->validationDate;
    }

    /**
     * Set user
     *
     * @param \stdClass $user
     *
     * @return Reconversion
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \stdClass
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return Reconversion
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Reconversion
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
}

