<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Cairn\UserBundle\Entity\BaseNotification;
use Cairn\UserBundle\Entity\Operation;

/**
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\PaymentNotificationRepository")
 */
class PaymentNotification extends BaseNotification
{
    
    /**
     * @var array|null
     *
     * @ORM\Column(name="types", type="array", nullable=true)
     */
    private $types;

    /**
     * @var int
     * @Assert\GreaterThanOrEqual( value = 0)
     * @ORM\Column(name="min_amount", type="integer", nullable=true)
     */
    private $minAmount;

    //IF YOU CHANGE THIS VALUE, CHANGE web/service-worker.js !!!
    const TITLE_KEY = 'received_paiement_body';

    public function __construct(array $types = [],$minAmount = 0)
    {
        parent::__construct(self::KEYWORD_PAYMENT, self::PRIORITY_HIGH, self::TTL_PAYMENT, false);
        $this->setTypes($types);
        $this->setMinAmount($minAmount);
    }

    public static function getPushData(Operation $operation)
    {
        $data =  [
            'amount'=>$operation->getAmount(),
            'debitor'=>$operation->getDebitorName(),
            'done_at'=>$operation->getExecutionDate()->format('H:i')
        ];

        return [
            'ios' => [
                'loc-key' => self::TITILE_KEY,
                'loc-args' => array_values($data)
            ],
            'android' => [
                'body_loc_key'=> self::TITLE_KEY,
                'body_loc_args'=> array_values($data),
                'title_loc_key'=>'received_paiement_title'
            ],
            'web' => [
                'body'=> $operation->getCreditorContent(),
                'tag'=> self::TITLE_KEY,
                'data'=> $data
            ]
        ];
    }

        
    
    /**
     * Set types.
     *
     * @param array|null $types
     *
     * @return PaymentNotification
     */
    public function setTypes($types = null)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types.
     *
     * @return array|null
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set minAmount.
     *
     * @param int $minAmount
     *
     * @return PaymentNotification
     */
    public function setMinAmount($minAmount)
    {
        $this->minAmount = $minAmount;

        return $this;
    }

    /**
     * Get minAmount.
     *
     * @return int
     */
    public function getMinAmount()
    {
        return $this->minAmount;
    }
}
