<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebPushSubscription
 *
 * @ORM\Table(name="web_push_subscription")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\WebPushSubscriptionRepository")
 */
class WebPushSubscription
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
     * @ORM\Column(name="endpoint", type="string", length=255, unique=true)
     */
    private $endpoint;

    
    /**
     * @var bool
     * @ORM\Column(name="is_apple_os", type="boolean", unique=false, nullable=false)
     */
    private $isAppleOS;

    /**
     * @var array
     *
     * @ORM\Column(name="encryption_keys", type="array")
     */
    private $encryption_keys;

    /**
     * @var \Cairn\UserBundle\Entity\NotificationData
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\NotificationData", inversedBy="webPushSubscriptions", cascade={"persist"})
     *@ORM\JoinColumn(name="notification_data_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $notificationData;

    
    public function __construct(string $endpoint = '',bool $appleOS = false,array $encryption_keys = [])
    {
        $this->endpoint = $endpoint;
        $this->encryption_keys = $encryption_keys;
        $this->isAppleOS = $appleOS;
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
     * Set endpoint.
     *
     * @param string $endpoint
     *
     * @return WebPushSubscription
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set encryption_keys.
     *
     * @param array $encryption_keys
     *
     * @return WebPushSubscription
     */
    public function setEncryption_keys($encryption_keys)
    {
        $this->encryption_keys = $encryption_keys;

        return $this;
    }

    /**
     * Get encryption_keys.
     *
     * @return array
     */
    public function getEncryptionKeys()
    {
        return $this->encryption_keys;
    }

    /**
     * Set notificationData.
     *
     * @param \Cairn\UserBundle\Entity\NotificationData|null $notificationData
     *
     * @return WebPushSubscription
     */
    public function setNotificationData(\Cairn\UserBundle\Entity\NotificationData $notificationData = null)
    {
        $this->notificationData = $notificationData;

        return $this;
    }

    /**
     * Get notificationData.
     *
     * @return \Cairn\UserBundle\Entity\NotificationData|null
     */
    public function getNotificationData()
    {
        return $this->notificationData;
    }


    /**
     * Set isAppleOS.
     *
     * @param bool $isAppleOS
     *
     * @return WebPushSubscription
     */
    public function setIsAppleOS($isAppleOS)
    {
        $this->isAppleOS = $isAppleOS;

        return $this;
    }

    /**
     * Get isAppleOS.
     *
     * @return bool
     */
    public function isAppleOS()
    {
        return $this->isAppleOS;
    }
}
