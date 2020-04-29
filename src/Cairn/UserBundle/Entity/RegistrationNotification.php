<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Cairn\UserBundle\Entity\BaseNotification;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\PushTemplate;


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

    const TITLE_KEY = 'received_pro_body';

    public static function getPushData(User $user, PushTemplate $pushTemplate)
    {
        $data =  [
            'name'=>$user->getName(),
            'address'=>$user->getAddress()->__toString(),
            'description'=>$user->getDescription(),
            'type' => self::KEYWORD_REGISTER
        ];

        if($image = $user->getImage()){
            $data['image'] = $image->getWebPath();
        }

        return [
            'ios' => [
                'title'=>$pushTemplate->getTitle(),
                'body'=>$pushTemplate->getContent(),
                'loc-key' => self::TITLE_KEY,
                'loc-args' => array_values($data)
            ],
            'android' => [
                'title'=>$pushTemplate->getTitle(),
                'body'=>$pushTemplate->getContent(),
                'body_loc_key'=> self::TITLE_KEY,
                'body_loc_args'=> array_values($data)
            ]

        ];
    }

    public function __construct($radius = 50)
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
