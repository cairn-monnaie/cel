<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Phone
 *
 * @ORM\Table(name="phone")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\PhoneRepository")
 * @UniqueEntity(fields = {"identifier"},message="Cet identifiant SMS est déjà utilisé") 
 */
class Phone
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
     * @ORM\Column(name="identifier", type="string", length=30, unique=true, nullable=true)
     */
    private $identifier;

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
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\SmsData", inversedBy="phones", cascade={"persist"})
     *@ORM\JoinColumn(name="sms_data_id", nullable=false,referencedColumnName="id", onDelete="CASCADE")
     */
    private $smsData;


    public function __construct(SmsData $smsData)
    {
        $this->setSmsData($smsData);

        if($this->getUser()->hasRole('ROLE_PRO')){
            $this->setPaymentEnabled(false);
        }else{
            $this->setPaymentEnabled(true);
        }

        $this->setDailyNumberPaymentsThreshold(4);
        $this->setDailyAmountThreshold(50);
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


    public function getUser()
    {
        return $this->getSmsData()->getUser();
    }

    static function cleanPhoneNumber($phoneNumber) {
        return preg_replace('/[^0-9+]/', '',trim($phoneNumber)); 
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
     * Set smsData.
     *
     * @param string $smsData
     *
     * @return SmsData
     */
    public function setSmsData($smsData)
    {
        $this->smsData = $smsData;

        return $this;
    }

    /**
     * Get smsData.
     *
     * @return string
     */
    public function getSmsData()
    {
        return $this->smsData;
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
        $this->phoneNumber = self::cleanPhoneNumber($phoneNumber);

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

}
