<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * SmsData
 *
 * @ORM\Table(name="user_sms_data")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\SmsDataRepository")
 * @UniqueEntity(fields = {"smsClient"},message="Ce client SMS est déjà utilisé") 
 */
class SmsData
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
     * @ORM\Column(name="smsClient", type="string", length=255, nullable=true, unique=true)
     */
    private $smsClient;

    /**
     *@ORM\OneToMany(targetEntity="Cairn\UserBundle\Entity\Phone", mappedBy="smsData", cascade={"persist"})
     */
    private $phones;


    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\User", inversedBy="smsData", cascade={"persist"})
     *@ORM\JoinColumn(name="user_id", nullable=false,referencedColumnName="id")
     */
    private $user;


    public function __construct(User $user)
    {
        $this->setUser($user);
        $this->phones = new ArrayCollection();
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
     * Set smsClient.
     *
     * @param string $smsClient
     *
     * @return SmsData
     */
    public function setSmsClient($smsClient)
    {
        $this->smsClient = $smsClient;

        return $this;
    }

    /**
     * Get smsClient.
     *
     * @return string
     */
    public function getSmsClient()
    {
        return $this->smsClient;
    }


    /**
     * Set user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return SmsData
     */
    public function setUser(\Cairn\UserBundle\Entity\User $user = null)
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
     * Add phone
     *
     * @param \Cairn\UserBundle\Entity\Phone $phone
     *
     * @return User
     */
    public function addPhone(\Cairn\UserBundle\Entity\Phone $phone)
    {
        $this->phones[] = $phone;

        return $this;
    }

    /**
     * Remove phone
     *
     * @param \Cairn\UserBundle\Entity\Phone $phone
     */
    public function removePhone(\Cairn\UserBundle\Entity\Phone $phone)
    {
        $this->phones->removeElement($phone);
    }

    /**
     * Get phones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * check if phone exists
     *
     * @param \Cairn\UserBundle\Entity\Phone $phone
     */
    public function hasPhone(\Cairn\UserBundle\Entity\Phone $testPhone)
    {
        $phones = $this->getPhones();

        foreach($phones as $phone){
            if($phone == $testPhone){
                return true;
            }
        }
        return false;
    }


}
