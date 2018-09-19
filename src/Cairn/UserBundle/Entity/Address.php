<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\AddressRepository")
 */
class Address
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
     * @ORM\Column(name="street", type="string", length=255)
     */
    private $street;

    /**
     * @var Cairn\UserBundle\Entity\ZipCity
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\ZipCity" , cascade = {"persist"})
     *@ORM\JoinColumn(nullable=false)
     *@Assert\Valid()
     */
    private $zipCity;

    /**
     */
    private $user;


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
     * Set street
     *
     * @param string $street
     *
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }


    /**
     * Set user
     *
     * @param \Entity\User $user
     *
     * @return Address
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set zipCity
     *
     * @param \Cairn\UserBundle\Entity\ZipCity $zipCity
     *
     * @return Address
     */
    public function setZipCity(\Cairn\UserBundle\Entity\ZipCity $zipCity)
    {
        $this->zipCity = $zipCity;

        return $this;
    }

    /**
     * Get zipCity
     *
     * @return \Cairn\UserBundle\Entity\ZipCity
     */
    public function getZipCity()
    {
        return $this->zipCity;
    }
}
