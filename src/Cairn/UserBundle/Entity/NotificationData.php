<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Cairn\UserBundle\Entity\User;
use \Cairn\UserBundle\Entity\PaymentNotification;
use \Cairn\UserBundle\Entity\RegistrationNotification;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * NotificationData
 *
 * @ORM\Table(name="notification_data")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\NotificationDataRepository")
 */
class NotificationData
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
     * @var string|null
     *
     * @ORM\Column(name="pin_code", type="string", length=10, nullable=true)
     */
    private $pinCode;

    /**
     * @var array
     *
     * @ORM\Column(name="device_tokens", type="array")
     */
    private $deviceTokens;

    /**
     * @var ArrayCollection
     *@ORM\OneToMany(targetEntity="Cairn\UserBundle\Entity\WebPushSubscription", mappedBy="notificationData" , cascade={"persist","remove"})
     *@ORM\JoinColumn(nullable=false)
     */
    private $webPushSubscriptions;

    /**
     * @var User
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\User", inversedBy="notificationData", cascade={"persist"})
     *@ORM\JoinColumn(name="user_id", nullable=false,referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection
     *@ORM\OneToMany(targetEntity="Cairn\UserBundle\Entity\BaseNotification", mappedBy="notificationData" , cascade={"persist","remove"},orphanRemoval=true)
     *@ORM\JoinColumn(nullable=false)
     */
    protected $baseNotifications;


    public function __construct(User $user)
    {
        $this->baseNotifications = new ArrayCollection();
        $this->webPushSubscriptions = new ArrayCollection();

        $this->setDeviceTokens([]);
        $this->setUser($user);
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
     * Set pinCode.
     *
     * @param string|null $pinCode
     *
     * @return NotificationData
     */
    public function setPinCode($pinCode = null)
    {
        $this->pinCode = $pinCode;

        return $this;
    }

    /**
     * Get pinCode.
     *
     * @return string|null
     */
    public function getPinCode()
    {
        return $this->pinCode;
    }

    /**
     * Set deviceTokens.
     *
     * @param array $deviceTokens
     *
     * @return NotificationData
     */
    public function setDeviceTokens($deviceTokens)
    {
        $this->deviceTokens = $deviceTokens;

        return $this;
    }

    /**
     * Get deviceTokens.
     *
     * @return array
     */
    public function getDeviceTokens()
    {
        return $this->deviceTokens;
    }

    public function addDeviceToken(string $token)
    {
        if(! in_array($token,$this->deviceTokens)){
            $this->deviceTokens[] = $token;
        }

        return $this;
    }

    public function removeDeviceToken(string $token)
    {
        $this->deviceTokens = array_diff($this->deviceTokens,array($token));

        return $this;
    }

    
    /**
     * Set user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return NotificationData
     */
    public function setUser(\Cairn\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
        $user->setNotificationData($this);
        $this->baseNotifications = new ArrayCollection();

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
     * Add baseNotification.
     *
     * @param \Cairn\UserBundle\Entity\BaseNotification $baseNotification
     *
     * @return NotificationData
     */
    public function addBaseNotification(\Cairn\UserBundle\Entity\BaseNotification $baseNotification)
    {
        $this->baseNotifications[] = $baseNotification;
        $baseNotification->setNotificationData($this);

        return $this;
    }

    /**
     * Remove baseNotification.
     *
     * @param \Cairn\UserBundle\Entity\BaseNotification $baseNotification
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBaseNotification(\Cairn\UserBundle\Entity\BaseNotification $baseNotification)
    {
        return $this->baseNotifications->removeElement($baseNotification);
    }

    /**
     * Get baseNotifications.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBaseNotifications()
    {
        return $this->baseNotifications;
    }

    /**
     * Add webPushSubscription.
     *
     * @param \Cairn\UserBundle\Entity\WebPushSubscription $webPushSubscription
     *
     * @return NotificationData
     */
    public function addWebPushSubscription(\Cairn\UserBundle\Entity\WebPushSubscription $webPushSubscription)
    {
        $this->webPushSubscriptions[] = $webPushSubscription;

        return $this;
    }

    /**
     * Remove webPushSubscription.
     *
     * @param \Cairn\UserBundle\Entity\WebPushSubscription $webPushSubscription
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeWebPushSubscription(\Cairn\UserBundle\Entity\WebPushSubscription $webPushSubscription)
    {
        return $this->webPushSubscriptions->removeElement($webPushSubscription);
    }

    /**
     * Get webPushSubscriptions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWebPushSubscriptions()
    {
        return $this->webPushSubscriptions;
    }
}
