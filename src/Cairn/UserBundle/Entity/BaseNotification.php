<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseNotification
 *
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\BaseNotificationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"push" = "Cairn\UserBundle\Entity\BaseNotification", "payment" = "Cairn\UserBundle\Entity\PaymentNotification", "register" = "Cairn\UserBundle\Entity\RegistrationNotification" })
 */
class BaseNotification
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
     * @var bool
     * @ORM\Column(name="web_push_enabled", type="boolean", unique=false, nullable=false)
     */
    protected $webPushEnabled;

    /**
     * @var bool
     * @ORM\Column(name="app_push_enabled", type="boolean", unique=false, nullable=false)
     */
    protected $appPushEnabled;

    /**
     * @var bool
     * @ORM\Column(name="sms_enabled", type="boolean", unique=false, nullable=false)
     */
    protected $smsEnabled;

    /**
     * @var bool
     * @ORM\Column(name="email_enabled", type="boolean", unique=false, nullable=false)
     */
    protected $emailEnabled;

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
     * @var \Cairn\UserBundle\Entity\NotificationData
     *
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\NotificationData", inversedBy="baseNotifications", cascade={"persist"})
     *@ORM\JoinColumn(name="notification_data_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $notificationData;


    const PRIORITY_HIGH = 'high';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_VERY_LOW = 'very_low';

    const KEYWORD_PAYMENT = 'payment';
    const KEYWORD_REGISTER = 'register';

    const TTL_PAYMENT = 86400;
    const TTL_REGISTER = 86400;

    const ARRAY_KEYWORDS = array(self::KEYWORD_PAYMENT, self::KEYWORD_REGISTER);

    static public function mapKeywordToClass(string $keyword)
    {
        if($keyword == self::KEYWORD_PAYMENT){
            return PaymentNotification::class;
        }elseif($keyword == self::KEYWORD_REGISTER){
            return RegistrationNotification::class;

        }

        return NULL;
    }

    public function getTargetData($phoneNumber = NULL)
    {
        $notificationData = $this->getNotificationData() ;

        $res = ['webSubscriptions'=>[], 'deviceTokens'=>[],'email'=>'','phone'=>''];
        if($this->isWebPushEnabled()){
            $res['webSubscriptions'] = $notificationData->getWebPushSubscriptions();
        }
        if($this->isAppPushEnabled()){
            $res['deviceTokens'] = array('android'=>$notificationData->getAndroidDeviceTokens(),'ios'=>$notificationData->getIosDeviceTokens());
        }
        if($this->isEmailEnabled()){
            $res['email'] = $notificationData->getUser()->getEmail();
        }
        if($this->isSmsEnabled() && $phoneNumber){
            $res['phone'] = $phoneNumber ;
        }

        return $res;
    }

    public function __construct($keyword, $priority, $timeToLive, $collapsible)
    {
        $this->setWebPushEnabled(false); 
        $this->setAppPushEnabled(false);
        $this->setEmailEnabled(false);
        $this->setSmsEnabled(false);

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
     * Set webPushEnabled.
     *
     * @param bool $webPushEnabled
     *
     * @return BaseNotification
     */
    public function setWebPushEnabled($webPushEnabled)
    {
        $this->webPushEnabled = $webPushEnabled;

        return $this;
    }

    /**
     * is webPushEnabled.
     *
     * @return bool
     */
    public function isWebPushEnabled()
    {
        return $this->webPushEnabled;
    }

    /**
     * Set appPushEnabled.
     *
     * @param bool $appPushEnabled
     *
     * @return BaseNotification
     */
    public function setAppPushEnabled($appPushEnabled)
    {
        $this->appPushEnabled = $appPushEnabled;

        return $this;
    }

    /**
     * is appPushEnabled.
     *
     * @return bool
     */
    public function isAppPushEnabled()
    {
        return $this->appPushEnabled;
    }

    /**
     * Set emailEnabled.
     *
     * @param bool $emailEnabled
     *
     * @return BaseNotification
     */
    public function setEmailEnabled($emailEnabled)
    {
        $this->emailEnabled = $emailEnabled;

        return $this;
    }

    /**
     * is emailEnabled.
     *
     * @return bool
     */
    public function isEmailEnabled()
    {
        return $this->emailEnabled;
    }

    /**
     * Set smsEnabled.
     *
     * @param bool $smsEnabled
     *
     * @return BaseNotification
     */
    public function setSmsEnabled($smsEnabled)
    {
        $this->smsEnabled = $smsEnabled;

        return $this;
    }

    /**
     * is smsEnabled.
     *
     * @return bool
     */
    public function isSmsEnabled()
    {
        return $this->smsEnabled;
    }

    /**
     * Set keyword.
     *
     * @param string $keyword
     *
     * @return BaseNotification
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
     * @return BaseNotification
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
     * @return BaseNotification
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
     * @return BaseNotification
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
    public function isCollapsible()
    {
        return $this->collapsible;
    }

    /**
     * Set notificationData
     *
     * @param \Cairn\UserBundle\Entity\NotificationData $notificationData
     *
     * @return User
     */
    public function setNotificationData(\Cairn\UserBundle\Entity\NotificationData $notificationData)
    {
        $this->notificationData = $notificationData;

        return $this;
    }

    /**
     * Get notificationData
     *
     * @return \Cairn\UserBundle\Entity\NotificationData
     */
    public function getNotificationData()
    {
        return $this->notificationData;
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
     * Get appPushEnabled.
     *
     * @return bool
     */
    public function getAppPushEnabled()
    {
        return $this->appPushEnabled;
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

    /**
     * Get emailEnabled.
     *
     * @return bool
     */
    public function getEmailEnabled()
    {
        return $this->emailEnabled;
    }
}
