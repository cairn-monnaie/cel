<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Beneficiary
 *
 * @ORM\Table(name="beneficiary")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\BeneficiaryRepository")
 */
class Beneficiary
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
     *@ORM\JoinColumn(nullable=false)
     */
    private $user;


     /**
     * @var \Cairn\UserBundle\Entity\User
     *
     *@ORM\ManyToMany(targetEntity="Cairn\UserBundle\Entity\User", inversedBy="beneficiaries", cascade={"persist"})
     */
    private $sources;

    /**
     * Identifiant Compte Cairn
     * @var string
     *
     * @ORM\Column(name="ICC", type="string",unique=true)
     */
    private $ICC;


    public function __construct()
    {
        $this->sources = new ArrayCollection();
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
     * Set ICC
     *
     * @param integer $ICC
     *
     * @return Beneficiary
     */
    public function setICC($ICC)
    {
        $this->ICC = $ICC;

        return $this;
    }

    /**
     * Get ICC
     *
     * @return int
     */
    public function getICC()
    {
        return $this->ICC;
    }

    /**
     * Set user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return Beneficiary
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
     * Add source
     *
     * @param \Cairn\UserBundle\Entity\User $source
     *
     * @return Beneficiary
     */
    public function addSource(\Cairn\UserBundle\Entity\User $source)
    {
        $this->sources[] = $source;

        return $this;
    }

    /**
     * Remove source
     *
     * @param \Cairn\UserBundle\Entity\User $source
     */
    public function removeSource(\Cairn\UserBundle\Entity\User $source)
    {
        $this->sources->removeElement($source);
    }

    /**
     * Get sources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSources()
    {
        return $this->sources;
    }
}
