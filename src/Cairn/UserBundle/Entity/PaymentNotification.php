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

    const TITLE_KEY = 'received_paiement_body';

    public function __construct()
    {
        parent::__construct(self::KEYWORD_PAYMENT, self::PRIORITY_HIGH, self::TTL_PAYMENT, false);
        $this->setTypes([Operation::TYPE_SMS_PAYMENT,Operation::TYPE_TRANSACTION_EXECUTED,Operation::TYPE_MOBILE_APP]);
        $this->setMinAmount(0);
    }

    public static function getPushData(Operation $operation)
    {
        $data =  [
            'amount'=>strval($operation->getAmount()),
            'debitor'=>$operation->getDebitorName(),
            'done_at'=>$operation->getExecutionDate()->format('H:i')
        ];

        $ios =  [
            "aps" => [
                "alert" =>[
                    'loc-key' => self::TITLE_KEY,
                    'loc-args' => array_values($data)
                ],
                "type" => self::KEYWORD_PAYMENT,
                "id" => $operation->getID()
            ]
        ];

        $android = [
            "notification" => [
                'body_loc_key'=> self::TITLE_KEY,
                'body_loc_args'=> array_values($data),
                'title_loc_key'=>'received_paiement_title'
            ],
            'collapse_key'=>  self::KEYWORD_PAYMENT,
            //'android'=>array(
            //    'ttl'=> $ttl,
            //    'priority'=> $priority,
            //),
            "data"=>[
                'type' =>  self::KEYWORD_PAYMENT,
                'id' => strval($operation->getID())
            ]
        ];

        $web = [
            'body'=> $operation->getCreditorContent(),
            'tag'=> self::KEYWORD_PAYMENT,
            'data'=> $data
        ];

        return ['android'=>$android,'ios'=>$ios,'web'=>$web];
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
