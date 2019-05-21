<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationPermission
 *
 * @ORM\Table(name="notification_permission")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\NotificationPermissionRepository")
 */
class NotificationPermission
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
     * @var bool
     *
     * @ORM\Column(name="emailEnabled", type="boolean")
     */
    private $emailEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="webPushEnabled", type="boolean")
     */
    private $webPushEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="smsEnabled", type="boolean")
     */
    private $smsEnabled;

    public function __construct()
    {
        $this->smsEnabled = false;
        $this->emailEnabled = false;
        $this->webPushEnabled = false;
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
     * Set emailEnabled.
     *
     * @param bool $emailEnabled
     *
     * @return NotificationPermission
     */
    public function setEmailEnabled($emailEnabled)
    {
        $this->emailEnabled = $emailEnabled;

        return $this;
    }

    /**
     * Get emailEnabled.
     *
     * @return bool
     */
    public function getEmailEnabled()
    {
        return $this->emailEnabled;
    }

    /**
     * Set webPushEnabled.
     *
     * @param bool $webPushEnabled
     *
     * @return NotificationPermission
     */
    public function setWebPushEnabled($webPushEnabled)
    {
        $this->webPushEnabled = $webPushEnabled;

        return $this;
    }

    /**
     * Get webPushEnabled.
     *
     * @return bool
     */
    public function getWebPushEnabled()
    {
        return $this->webPushEnabled;
    }

    /**
     * Set smsEnabled.
     *
     * @param bool $smsEnabled
     *
     * @return NotificationPermission
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
    public function getSmsEnabled()
    {
        return $this->smsEnabled;
    }
}
