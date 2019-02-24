<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sms
 *
 * @ORM\Table(name="sms")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\SmsRepository")
 */
class Sms
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=15)
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="requested_at", type="datetime")
     */
    private $requestedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="cardKey", type="string", length=8)
     */
    private $cardKey;

    const STATE_WAITING_KEY = 0;
    const STATE_EXPIRED = 1;

    public function __construct($phoneNumber,$content,$state,$cardKey)
    {
        $this->phoneNumber = $phoneNumber;
        $this->content = $content;
        $this->state = $state;
        $this->requestedAt = new \Datetime();
        $this->cardKey = $cardKey;
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
     * Set phoneNumber.
     *
     * @param string $phoneNumber
     *
     * @return Sms
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Sms
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set requestedAt.
     *
     * @param \DateTime $requestedAt
     *
     * @return Sms
     */
    public function setRequestedAt($requestedAt)
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    /**
     * Get requestedAt.
     *
     * @return \DateTime
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return Sms
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set cardKey.
     *
     * @param string $cardKey
     *
     * @return Sms
     */
    public function setCardKey($cardKey)
    {
        $this->cardKey = $cardKey;

        return $this;
    }

    /**
     * Get cardKey.
     *
     * @return string
     */
    public function getCardKey()
    {
        return $this->cardKey;
    }
}
