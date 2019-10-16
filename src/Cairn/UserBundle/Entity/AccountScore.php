<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountScore
 *
 * @ORM\Table(name="account_score")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\AccountScoreRepository")
 */
class AccountScore
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
     * @ORM\Column(name="format", type="string", length=5)
     */
    private $format;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string", length=255,nullable=true, unique=true)
     */
    private $confirmationToken;

    /**
     * @var array
     *
     * @ORM\Column(name="schedule", type="array")
     */
    private $schedule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="nb_sent_today", type="smallint")
     */
    private $nbSentToday;

    /**
     * @var string
     *
     * @ORM\Column(name="considered_day", type="string", length=4,nullable=false, unique=false)
     */
    private $consideredDay;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\User",  cascade={"persist"})
     *@ORM\JoinColumn(name="user_id", nullable=false,referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    const PDF_FORMAT = 'pdf';
    const CSV_FORMAT = 'csv';

    public function __construct()
    {
        $this->schedule = array('Mon'=>array(),'Tue'=>array(),'Wed'=>array(), 'Thu'=>array(), 'Fri'=>array(), 'Sat'=>array(), 'Sun'=>array());
        $this->nbSentToday = 0;

        $this->consideredDay = date('D'); 
    }

    public static function getPossibleTypes()
    {
        return array(self::PDF_FORMAT, self::CSV_FORMAT);
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
     * Set format.
     *
     * @param string $format
     *
     * @return AccountScore
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return AccountScore
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set confirmationToken.
     *
     * @param string $confirmationToken
     *
     * @return AccountScore
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken.
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set schedule.
     *
     * @param array $schedule
     *
     * @return AccountScore
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule.
     *
     * @return array
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set nbSentToday.
     *
     * @param \DateTime $nbSentToday
     *
     * @return AccountScore
     */
    public function setNbSentToday($nbSentToday)
    {
        $this->nbSentToday = $nbSentToday;

        $nextDayToConsider = new \Datetime();

        $cmpt = 0;
        if( count($this->getSchedule()[$nextDayToConsider->format('D')]) == $this->getNbSentToday() ){
            $nextDayToConsider->modify('+1 day');
            $cmpt++;
            while( empty($this->getSchedule()[$nextDayToConsider->format('D')]) ){
                $nextDayToConsider->modify('+1 day');
                $cmpt++;

                if($cmpt >= 7){
                    break;
                }
            }

            $this->setConsideredDay($nextDayToConsider->format('D'));
            $this->nbSentToday = 0;
        }

        return $this;
    }

    /**
     * Get nbSentToday.
     *
     * @return \DateTime
     */
    public function getNbSentToday()
    {
        return $this->nbSentToday;
    }

    /**
     * Set consideredDay.
     *
     * @param \DateTime $consideredDay
     *
     * @return AccountScore
     */
    public function setConsideredDay($consideredDay)
    {
        $this->consideredDay = $consideredDay;

        return $this;
    }

    /**
     * Get consideredDay.
     *
     * @return \DateTime
     */
    public function getConsideredDay()
    {
        return $this->consideredDay;
    }

    /**
     * Set user.
     *
     * @param array $user
     *
     * @return AccountScore
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

}
