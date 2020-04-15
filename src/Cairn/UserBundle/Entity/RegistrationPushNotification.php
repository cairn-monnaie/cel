<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Cairn\UserBundle\Entity\PushNotification;

/**
 * RegistrationPushNotification
 *
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\RegistrationNotificationRepository")
 */
class RegistrationPushNotification extends PushNotification
{
    
    /**
     * @var int|null
     * @Assert\GreaterThan( value = 0)
     * @ORM\Column(name="radius", type="integer", nullable=true)
     */
    private $radius;

    public function __construct(string $deviceToken = '', $radius = 1000000)
    {
        parent::__construct($deviceToken, self::KEYWORD_REGISTER, self::PRIORITY_NORMAL, self::TTL_REGISTER, true);
        $this->setRadius($radius);
    }

    
    /**
     * Set radius.
     *
     * @param int|null $radius
     *
     * @return RegistrationPushNotification
     */
    public function setRadius($radius = null)
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * Get radius.
     *
     * @return int|null
     */
    public function getRadius()
    {
        return $this->radius;
    }
}
