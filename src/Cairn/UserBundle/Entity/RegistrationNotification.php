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
        $ios =  [
            "aps" => [
                "alert" =>[
                    'title'=>$pushTemplate->getTitle(),
                    'body'=>$pushTemplate->getContent(),
                ],
                "type" => self::KEYWORD_REGISTER,
                "id" => strval($user->getID())
            ]
        ];

        $android = [
            "notification" => [
                'title'=>$pushTemplate->getTitle(),
                'body'=>$pushTemplate->getContent(),
            ],
            'collapse_key'=>  self::KEYWORD_REGISTER,
            //'android'=>array(
            //    'ttl'=> $ttl,
            //    'priority'=> $priority,
            //),
            "data"=>[
                'type' =>  self::KEYWORD_REGISTER,
                'id' => strval($user->getID())
            ]
        ];

        $web = array(
             'title'=> $pushTemplate->getTitle(),
             'payload'=> [
                 'tag' => self::KEYWORD_REGISTER,
                 'body' => $pushTemplate->getContent(),
                 'actions' => [
                     [
                         'action' => 'pro-website-action',
                         'title' => $pushTemplate->getActionTitle()
                     ]
                 ],
                 'data'=>[
                     'website'=> $pushTemplate->getRedirectionUrl()
                 ]
             ]
         );
         if($image = $user->getImage()){
              $web['payload']['image'] = $image->getWebPath();
         }

        return ['android'=>$android,'ios'=>$ios,'web'=>$web];
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
