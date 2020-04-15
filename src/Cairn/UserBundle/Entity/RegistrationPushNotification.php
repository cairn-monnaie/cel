<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Cairn\UserBundle\Entity\PushNotification;
use Cairn\UserBundle\Entity\User;


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

    const TITLE_KEY = 'pro_registration';

    public static function getPushData(User $user)
    {
        return [
            'id'=>$user->getId()
        ];
    }

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
