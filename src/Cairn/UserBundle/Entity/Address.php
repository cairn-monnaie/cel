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
     * @ORM\Column(name="street1", type="string", length=255)
     */
    private $street1;

    /**
     * @var string
     *
     * @ORM\Column(name="street2", type="string", length=255, nullable = true)
     */
    private $street2;

    /**
     * @var Cairn\UserBundle\Entity\ZipCity
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\ZipCity" , cascade = {"persist"})
     *@ORM\JoinColumn(nullable=false)
     *@Assert\Valid()
     */
    private $zipCity;


    public function __toString()
    {
        return $this->getStreet1().' '.$this->getStreet2().' '.$this->getZipCity()->getName();
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
     * Set street1
     *
     * @param string $street
     *
     * @return Address
     */
    public function setStreet1($street)
    {
        $this->street1 = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet1()
    {
        return $this->street1;
    }


    /**
     * Set street2
     *
     * @param string $street
     *
     * @return Address
     */
    public function setStreet2($street)
    {
        $this->street2 = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet2()
    {
        return $this->street2;
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
