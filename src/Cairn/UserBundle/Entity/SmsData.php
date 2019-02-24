<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(name="phoneNumber", type="string", length=15)
     */
    private $phoneNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="smsEnabled", type="boolean")
     */
    private $smsEnabled;

    /**
     * @var string
     *
     * @ORM\Column(name="smsClient", type="string", length=255, unique=true)
     */
    private $smsClient;

    /**
     * @var int
     *
     * @ORM\Column(name="dailyAmountThreshold", type="integer")
     */
    private $dailyAmountThreshold;

    /**
     * @var int
     *
     * @ORM\Column(name="dailyNumberPaymentsThreshold", type="integer")
     */
    private $dailyNumberPaymentsThreshold;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\User", inversedBy="smsData", cascade={"persist"})
     *@ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(User $user)
    {
        $this->setUser($user);
        $this->setSmsEnabled(false);
        $this->setDailyNumberPaymentsThreshold(4);
        $this->setDailyAmountThreshold(30);
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
     * Set phoneNumber.
     *
     * @param string $phoneNumber
     *
     * @return SmsData
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set smsEnabled.
     *
     * @param bool $smsEnabled
     *
     * @return SmsData
     */
    public function setSmsEnabled($smsEnabled)
    {
        $this->smsEnabled = $smsEnabled;

        return $this;
    }

    /**
     * Get smsEnabled.
     *
     * @return bool
     */
    public function isSmsEnabled()
    {
        return $this->smsEnabled;
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
     * Set dailyAmountThreshold.
     *
     * @param int $dailyAmountThreshold
     *
     * @return SmsData
     */
    public function setDailyAmountThreshold($dailyAmountThreshold)
    {
        $this->dailyAmountThreshold = $dailyAmountThreshold;

        return $this;
    }

    /**
     * Get dailyAmountThreshold.
     *
     * @return int
     */
    public function getDailyAmountThreshold()
    {
        return $this->dailyAmountThreshold;
    }

    /**
     * Set dailyNumberPaymentsThreshold.
     *
     * @param int $dailyNumberPaymentsThreshold
     *
     * @return SmsData
     */
    public function setDailyNumberPaymentsThreshold($dailyNumberPaymentsThreshold)
    {
        $this->dailyNumberPaymentsThreshold = $dailyNumberPaymentsThreshold;

        return $this;
    }

    /**
     * Get dailyNumberPaymentsThreshold.
     *
     * @return int
     */
    public function getDailyNumberPaymentsThreshold()
    {
        return $this->dailyNumberPaymentsThreshold;
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

}
