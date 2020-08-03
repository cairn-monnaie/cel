<?php

namespace Cairn\UserBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZipCity
 *
 * @ORM\Table(name="zip_city")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\ZipCityRepository")
 */
class ZipCity
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
     * @ORM\Column(name="zip_code", type="string", length=5)
     * @Assert\Length(min=5 ,max=5, exactMessage="Contient exactement {{limit}} chiffres")
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=180)
     */
    private $city;


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
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(){
        return $this->getZipCode().' '.$this->getCity();
    }

}

