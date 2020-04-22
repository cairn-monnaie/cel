<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Cairn\UserBundle\Entity\BaseNotification;
use Cairn\UserBundle\Entity\User;


/**
 * RegistrationNotification
 *
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\RegistrationNotificationRepository")
 */
class RegistrationNotification extends BaseNotification
{
    
    /**
     * @var int|null
     * @Assert\GreaterThan( value = 0)
     * @ORM\Column(name="radius", type="integer", nullable=true)
     */
    private $radius;

    //IF YOU CHANGE THIS VALUE, CHANGE web/service-worker.js !!!
    const TITLE_KEY = 'pro_registration';

    public static function getPushData(User $user)
    {
        $data = [
            'tag'=> self::TITLE_KEY,
            'id'=>$user->getId(),
            'username'=>$user->getUsername(),
            'name'=>$user->getName(),
            'address'=>$user->getAddress()->__toString()
        ];

        if($logo = $user->getImage()){
            $data['image'] = $logo->getWebPath(); 
        }

        return $data;
    }

    public function __construct($radius = 5)
    {
        parent::__construct(self::KEYWORD_REGISTER, self::PRIORITY_VERY_LOW, self::TTL_REGISTER, true);
        $this->setRadius($radius);
    }

    
    /**
     * Set radius.
     *
     * @param int|null $radius
     *
     * @return RegistrationNotification
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
