<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PushNotification
 *
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\PushNotificationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"push" = "Cairn\UserBundle\Entity\PushNotification", "payment" = "Cairn\UserBundle\Entity\PaymentPushNotification", "register" = "Cairn\UserBundle\Entity\RegistrationPushNotification" })
 */
class PushNotification
{
   /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="device_token", type="string", length=255)
     */
    protected $deviceToken;

    /**
     * @var string
     */
    protected $keyword;

    /**
     * @var int
     */
    protected $timeToLive;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var bool
     */
    protected $collapsible;


    /**
     * @var \Cairn\UserBundle\Entity\AppData
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\AppData", cascade={"persist"})
     *@ORM\JoinColumn(name="app_data_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $appData;


    const PRIORITY_HIGH = 'high';
    const PRIORITY_NORMAL = 'normal';

    const KEYWORD_PAYMENT = 'payment';
    const KEYWORD_REGISTER = 'register';

    const TTL_PAYMENT = 86400;
    const TTL_REGISTER = 86400;

    const ARRAY_KEYWORDS = array(self::KEYWORD_PAYMENT, self::KEYWORD_REGISTER);

    static public function mapKeyWordToClass(string $keyword)
    {
        if($keyword == self::KEYWORD_PAYMENT){
            return PaymentPushNotification::class;
        }elseif($keyword == self::KEYWORD_REGISTER){
            return RegistrationPushNotification::class;

        }

        return NULL;
    }

    public function __construct(string $deviceToken, $keyword, $priority, $timeToLive, $collapsible)
    {
        $this->setDeviceToken($deviceToken);
        $this->setKeyword($keyword);
        $this->setPriority($priority);
        $this->setTimeToLive($timeToLive);
        $this->setCollapsible($collapsible);
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
     * Set deviceToken.
     *
     * @param string $deviceToken
     *
     * @return PushNotification
     */
    public function setDeviceToken($deviceToken)
    {
        $this->deviceToken = $deviceToken;

        return $this;
    }

    /**
     * Get deviceToken.
     *
     * @return string
     */
    public function getDeviceToken()
    {
        return $this->deviceToken;
    }

    /**
     * Set keyword.
     *
     * @param string $keyword
     *
     * @return PushNotification
     */
    protected function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword.
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set timeToLive.
     *
     * @param int $timeToLive
     *
     * @return PushNotification
     */
    protected function setTimeToLive($timeToLive)
    {
        $this->timeToLive = $timeToLive;

        return $this;
    }

    /**
     * Get timeToLive.
     *
     * @return int
     */
    public function getTimeToLive()
    {
        return $this->timeToLive;
    }

    /**
     * Set priority.
     *
     * @param string $priority
     *
     * @return PushNotification
     */
    protected function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority.
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set collapsible.
     *
     * @param bool $collapsible
     *
     * @return PushNotification
     */
    protected function setCollapsible($collapsible)
    {
        $this->collapsible = $collapsible;

        return $this;
    }

    /**
     * Get collapsible.
     *
     * @return bool
     */
    public function getCollapsible()
    {
        return $this->collapsible;
    }

    /**
     * Set appData
     *
     * @param \Cairn\UserBundle\Entity\AppData $appData
     *
     * @return User
     */
    public function setAppData(\Cairn\UserBundle\Entity\AppData $appData)
    {
        $this->appData = $appData;

        return $this;
    }

    /**
     * Get appData
     *
     * @return \Cairn\UserBundle\Entity\AppData
     */
    public function getAppData()
    {
        return $this->appData;
    }


}
