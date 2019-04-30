<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SmsData
 *
 * @ORM\Table(name="user_sms_data")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\SmsDataRepository")
 * @UniqueEntity(fields = {"identifier"},message="Cet identifiant SMS est déjà utilisé") 
 */
class SmsData
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
     * @ORM\Column(name="identifier", type="string", length=30, nullable=true)
     */
    private $identifier;

    /**
     * @var bool
     *
     * Can ask for LOGIN, BALANCE and receive payments
     *
     * @ORM\Column(name="smsEnabled", type="boolean")
     */
    private $smsEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="payment_enabled", type="boolean")
     */
    private $paymentEnabled;


    /**
     * @var int
     *
     * @ORM\Column(name="dailyAmountThreshold", type="integer")
     */
    private $dailyAmountThreshold;

    /**
     * @var int
     *
     * @ORM\Column(name="dailyNumberPaymentsThreshold", type="integer")
     */
    private $dailyNumberPaymentsThreshold;

    /**
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\User", inversedBy="smsData", cascade={"persist"})
     *@ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(User $user)
    {
        $this->setUser($user);
        $this->setSmsEnabled(true);

        if($user->hasRole('ROLE_PRO')){
            $this->setPaymentEnabled(false);
        }else{
            $this->setPaymentEnabled(true);
        }

        $this->setDailyNumberPaymentsThreshold(4);
        $this->setDailyAmountThreshold(30);
    }

    static function makeIdentifier($name, $extra = '')
    {
        $name = preg_replace('/[-\/]+/', ' ', $name);
        $ln = explode(' ', $name);

//        var_dump($ln);
        //get rid off articles
        $tmp = $ln;
        for($i = 0; $i < count($ln); $i++){
            if( strlen($ln[$i]) < 3){
                array_splice($ln,$i,1);
                $i -= 1;
//                var_dump($ln);
            }
        }

//        var_dump($ln);
        if(count($ln) == 0){//name contains only words with less than 3 characters
            $ln = $tmp;
        }

        if(count($ln) == 1){
            $ln = $ln[0];   
        }elseif(count($ln) == 2){
            $ln = $ln[0] . $ln[1];
        }else{
            if( strlen($ln[0]) == 3){
                $tmp = $ln[0].$ln[1];
                $offset = 2;
            }else{
                $tmp = $ln[0];
                $offset = 1;
            }
            
            for($i = $offset; $i < count($ln); $i++){
                $tmp .= substr($ln[$i], 0, 1);
            }
            $ln = $tmp;
        }

       
        $identifier = strtoupper($ln);
        $identifier = preg_replace('/[^A-Z0-9]/', '', $identifier);

        //identifier must be easy to use, so it is shorcutted if too long : 8 characters at most
        $lengthExtra = strlen($extra);

        if($lengthExtra == 0){
            $identifier = substr($identifier,0,8);
        }else{
            $identifier = substr($identifier,0,8 - $lengthExtra);
        }
        $identifier .= $extra;
        return $identifier;
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
     * @return SmsData
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
     * Set identifier.
     *
     * @param string $identifier
     *
     * @return SmsData
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set smsEnabled.
     *
     * @param bool $smsEnabled
     *
     * @return SmsData
     */
    public function setSmsEnabled($smsEnabled)
    {
        $this->smsEnabled = $smsEnabled;

        if(! $smsEnabled){
            $this->setPaymentEnabled(false);
        }else{
            if($this->getUser()->hasRole('ROLE_PERSON')){
                $this->setPaymentEnabled(true);
            }
        }

        return $this;
    }

    /**
     * Get smsEnabled.
     *
     * @return bool
     */
    public function isSmsEnabled()
    {
        return $this->smsEnabled;
    }


    /**
     * Set paymentEnabled.
     *
     * @param bool $paymentEnabled
     *
     * @return PaymentData
     */
    public function setPaymentEnabled($paymentEnabled)
    {
        $this->paymentEnabled = $paymentEnabled;

        return $this;
    }

    /**
     * Get paymentEnabled.
     *
     * @return bool
     */
    public function isPaymentEnabled()
    {
        return $this->paymentEnabled;
    }

    /**
     * Set dailyAmountThreshold.
     *
     * @param int $dailyAmountThreshold
     *
     * @return SmsData
     */
    public function setDailyAmountThreshold($dailyAmountThreshold)
    {
        $this->dailyAmountThreshold = $dailyAmountThreshold;

        return $this;
    }

    /**
     * Get dailyAmountThreshold.
     *
     * @return int
     */
    public function getDailyAmountThreshold()
    {
        return $this->dailyAmountThreshold;
    }

    /**
     * Set dailyNumberPaymentsThreshold.
     *
     * @param int $dailyNumberPaymentsThreshold
     *
     * @return SmsData
     */
    public function setDailyNumberPaymentsThreshold($dailyNumberPaymentsThreshold)
    {
        $this->dailyNumberPaymentsThreshold = $dailyNumberPaymentsThreshold;

        return $this;
    }

    /**
     * Get dailyNumberPaymentsThreshold.
     *
     * @return int
     */
    public function getDailyNumberPaymentsThreshold()
    {
        return $this->dailyNumberPaymentsThreshold;
    }

    /**
     * Set user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return SmsData
     */
    public function setUser(\Cairn\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Cairn\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

}
