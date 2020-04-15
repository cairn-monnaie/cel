<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Cairn\UserBundle\Entity\PushNotification;
use Operation;

/**
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\PaymentPushNotificationRepository")
 */
class PaymentPushNotification extends PushNotification
{
    
    /**
     * @var array|null
     *
     * @ORM\Column(name="types", type="array")
     */
    private $types;

    /**
     * @var int
     * @Assert\GreaterThanOrEqual( value = 0)
     * @ORM\Column(name="min_amount", type="integer")
     */
    private $minAmount;

    const TITLE_KEY = 'push_received_paiement';

    public function __construct(string $deviceToken = '', array $types = [],$minAmount = 0)
    {
        parent::__construct($deviceToken, self::KEYWORD_PAYMENT, self::PRIORITY_HIGH, self::TTL_PAYMENT, false);
        $this->setTypes($types);
        $this->setMinAmount($minAmount);
    }

    public static function getPushData(Operation $operation)
    {
        return [
            'type'=>$operation->getType(),
            'amount'=>$operation->getAmount(),
            'debitor'=>$operation->getDebitorName(),
            'done_at'=>$operation->getExecutionDate()->format('H:i')
        ];
    }
    
    /**
     * Set types.
     *
     * @param array|null $types
     *
     * @return PaymentPushNotification
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
     * @return PaymentPushNotification
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
