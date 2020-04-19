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
     * @var array
     *
     * @ORM\Column(name="encryption_keys", type="array")
     */
    private $encryptionKeys;

    /**
     * @var \Cairn\UserBundle\Entity\NotificationData
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\NotificationData", inversedBy="webPushSubscriptions", cascade={"persist"})
     *@ORM\JoinColumn(name="notification_data_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $notificationData;

    public function __construct(string $endpoint,array $keys)
    {
        $this->endpoint = $endpoint;
        $this->encryptionKeys = $keys;
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
     * Set encryptionKeys.
     *
     * @param array $encryptionKeys
     *
     * @return WebPushSubscription
     */
    public function setEncryptionKeys($encryptionKeys)
    {
        $this->encryptionKeys = $encryptionKeys;

        return $this;
    }

    /**
     * Get encryptionKeys.
     *
     * @return array
     */
    public function getEncryptionKeys()
    {
        return $this->encryptionKeys;
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
}
