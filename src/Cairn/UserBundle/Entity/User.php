<?php
//src/Cairn/UserBundle/Entity/User.php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

use FOS\UserBundle\Model\User as BaseUser;

use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\ApiClient;
use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\NotificationData;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="cairn_user")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields = {"cyclosID"},message="Cet ID est déjà utilisé") 
 * @UniqueEntity(fields = {"email"},message="Cet email est déjà utilisé") 
 * @UniqueEntity(fields = {"username"},message="Ce nom d utilisateur est déjà utilisé") 
 */
class User extends BaseUser
{
    public static $_counter= 0;
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", unique=false, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="firstname", type="string", unique=false, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(name="cyclos_id", type="bigint", unique=true)
     * @Assert\Length(min=17, minMessage="Contient au moins {{ limit }} chiffres")
     */
    private $cyclosID;

    /**
     * @ORM\Column(name="cyclos_token", type="string", length=255,unique=true,nullable=true)
     */
    private $cyclosToken;

    /**
     * @ORM\Column(name="main_icc", type="string", unique=true, nullable=true)
     */
    private $mainICC;

    /**
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    private $url;

    /**
     * @var array
     *
     * @ORM\Column(name="keywords", type="array")
     */
    private $keywords;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\Address", cascade={"persist","remove"})
     *@Assert\Valid()
     */ 
    private $address;

    /**
     * @ORM\Column(name="description", type="text", unique=false)
     * @Assert\NotBlank(message="Entrez une description de votre activité.")
     */
    private $description; 

    /**
     * @ORM\Column(name="creation_date", type="datetime", unique=false, nullable=false)
     */
    private $creationDate; 

    /**
     * @ORM\Column(name="nb_phone_number_requests", type="smallint", unique=false, nullable=false)
     */
    private $nbPhoneNumberRequests; 

    /**
     * @var ArrayCollection
     *@ORM\ManyToMany(targetEntity="Cairn\UserBundle\Entity\User", cascade={"persist"})
     *@ORM\JoinColumn(referencedColumnName="id")
     */
    private $referents;

    /**
     *@ORM\ManyToMany(targetEntity="Cairn\UserBundle\Entity\Beneficiary", mappedBy="sources",  cascade={"persist","remove"})
     */ 
    private $beneficiaries;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\File", cascade={"persist","remove"})
     *@ORM\JoinColumn(name="image_id", nullable=true,referencedColumnName="id", onDelete="SET NULL")
     */
    private $image;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\File", cascade={"persist","remove"})
     *@ORM\JoinColumn(name="identity_document_id", nullable=true,referencedColumnName="id", onDelete="SET NULL")
     */
    private $identityDocument;

    /**
     *@ORM\ManyToOne(targetEntity="Cairn\UserBundle\Entity\Card", inversedBy="users", cascade={"persist","remove"})
     *@ORM\JoinColumn(name="card_id", nullable=true,referencedColumnName="id", onDelete="SET NULL")
     */
    private $card;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\SmsData", mappedBy="user", cascade={"persist","remove"})
     */
    private $smsData;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\NotificationData", mappedBy="user", cascade={"persist","remove"})
     */
    private $notificationData;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\ApiClient", mappedBy="user", cascade={"persist"})
     */
    private $apiClient;

    /**
     * @ORM\Column(name="pwd_tries", type="smallint", unique=false, nullable=false)
     */
    private $passwordTries;

    /**
     * @ORM\Column(name="card_key_tries", type="smallint", unique=false, nullable=false)
     */
    private $cardKeyTries;

    /**
     * @ORM\Column(name="phone_number_activation_tries", type="smallint", unique=false, nullable=false)
     */
    private $phoneNumberActivationTries;

    /**
     * @ORM\Column(name="card_association_tries", type="smallint", unique=false, nullable=false)
     */
    private $cardAssociationTries;

    /**
     * @ORM\Column(name="removal_request", type="boolean", unique=false, nullable=false)
     */
    private $removalRequest;

    /**
     * @ORM\Column(name="first_login", type="boolean", unique=false, nullable=false)
     */
    private $firstLogin;

    public function __construct()
    {
        parent::__construct();

        $this->creationDate = new \Datetime();
        $this->beneficiaries = new ArrayCollection();
        $this->referents = new ArrayCollection();
        $this->setPasswordTries(0);
        $this->setCardKeyTries(0);
        $this->setCardAssociationTries(0);
        $this->removalRequest = false;
        $this->firstLogin = true;
        $this->setNbPhoneNumberRequests(0);
        $this->setPhoneNumberActivationTries(0);

        $this->setKeywords([]);

        $this->setNotificationData(new NotificationData($this));
    }

    public function __toString()
    {
        if ($this->getFirstname()){
            return $this->getFirstname().' '.$this->getName();
        }else{
            return $this->getName();
        }
    }

    public function getAutocompleteLabel($addEmail = true){
        $base = $this->getName(). ' ['. $this->getAddress()->getZipCity()->getName() . '] ';

        if($addEmail){
            $base .= ' ('.  $this->getEmail() .')';
        }
        return  $base;
    }

    public function getCity()
    {
        return $this->getAddress()->getZipCity()->getCity();
    }

    public function getPhones()
    {
        if($smsData = $this->getSmsData()){
            return $smsData->getPhones();
        }else{
            return array();
        }
    }

    public function getWebPushSubscriptions()
    {
        if($nfData = $this->getNotificationData()){
            return $nfData->getWebPushSubscriptions();
        }else{
            return array();
        }
    }

    public function getPhoneNumbers()
    {
        $phoneNumbers = array();

        if($smsData = $this->getSmsData()){
            $phones = $smsData->getPhones();
            foreach($phones as $phone){
                $phoneNumbers[] = $phone->getPhoneNumber();
            }
        }

        return $phoneNumbers;
    }


    public function isAdherent()
    {
        return ($this->hasRole('ROLE_PRO') || $this->hasRole('ROLE_PERSON'));
    }

    public function isAdmin()
    {
        return ($this->hasRole('ROLE_ADMIN') || $this->hasRole('ROLE_SUPER_ADMIN'));
    }

    static function makeUsername($lastname, $firstname = '' , $extra = '')
    {
        $lastname = preg_replace('/[-\/]+/', ' ', $lastname);
        $ln = explode(' ', $lastname);
        if (strlen($ln[0]) < 3 && count($ln) > 1)
            $ln = $ln[0] . $ln[1];
        else
            $ln = $ln[0];
        $username = strtolower(substr(explode(' ', $firstname)[0], 0, 1) . $ln);
        $username = preg_replace('/[^a-z0-9]/', '', $username);
        $username .= $extra;
        return $username;
    }

    static function randomPassword() {
        $alphabet = 'abcdefghijklmnDEFGHIJKLMNOPQRSTUVWXYZ1234567890@_-#';
        $pass = array(); 
        $alphaLength = strlen($alphabet) - 1; 
        //cyclos does not accept passwords with more than 12 characters
        for ($i = 0; $i < 11; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $pass[] = '@'; //just to make sure there is a special character in the password;
        return implode($pass); //turn the array into a string
    }


    /**
     * Set cyclosID
     *
     * @param integer $cyclosID
     *
     * @return User
     */
    public function setCyclosID($cyclosID)
    {
        $this->cyclosID = $cyclosID;

        return $this;
    }

    /**
     * Get cyclosID
     *
     * @return integer
     */
    public function getCyclosID()
    {
        return $this->cyclosID;
    }

    /**
     * Set cyclosToken
     *
     * @param string $cyclosToken
     *
     * @return User
     */
    public function setCyclosToken($cyclosToken)
    {
        $this->cyclosToken = $cyclosToken;

        return $this;
    }

    /**
     * Get cyclosToken
     *
     * @return string
     */
    public function getCyclosToken()
    {
        return $this->cyclosToken;
    }


    /**
     * Set mainICC
     *
     * @param integer $main_icc
     *
     * @return User
     */
    public function setMainICC($main_icc)
    {
        $this->mainICC = $main_icc;

        return $this;
    }

    /**
     * Get mainICC
     *
     * @return integer
     */
    public function getMainICC()
    {
        return $this->mainICC;
    }

    /**
     * Set keywords.
     *
     * @param array $keywords
     *
     * @return User
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords.
     *
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    public function fromEntityToDTO()
    {
        $userDTO = new \stdClass();
        $userDTO->name = $this->getUsername();
        $userDTO->username = $userDTO->name;
        $userDTO->email = $this->getEmail();
        $password = new \stdClass();
        $password->assign = true;
        $password->type = "login";//in Cyclos : System -> User config -> password types -> click on login Password
        $password->value = $this->getPassword();
        $password->confirmationValue = $userDTO->confirmationPassword;
        $userDTO->passwords = $password;

        return $userDTO;
    }

    private function saveDTO()
    {
        $userManager = new UserManager();
        $userDTO = $this->fromEntityToDTO();
        $userID = $userManager->editUser($userDTO);
        $this->setCyclosID($userID);
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set nbPhoneNumberRequests
     *
     * @param int $nbPhoneNumberRequests
     *
     * @return User
     */
    public function setNbPhoneNumberRequests($nbPhoneNumberRequests)
    {
        $this->nbPhoneNumberRequests = $nbPhoneNumberRequests;

        return $this;
    }

    /**
     * Get nbPhoneNumberRequests
     *
     * @return int
     */
    public function getNbPhoneNumberRequests()
    {
        return $this->nbPhoneNumberRequests;
    }


    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return User
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param \Cairn\UserBundle\Entity\File $image
     *
     * @return User
     */
    public function setImage(\Cairn\UserBundle\Entity\File $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Cairn\UserBundle\Entity\File
     */
    public function getImage()
    {
        return $this->image;
    }

     /**
     * Set identityDocument
     *
     * @param \Cairn\UserBundle\Entity\File $identityDocument
     *
     * @return User
     */
    public function setIdentityDocument(\Cairn\UserBundle\Entity\File $identityDocument = null)
    {
        $this->identityDocument = $identityDocument;

        return $this;
    }

    /**
     * Get identityDocument
     *
     * @return \Cairn\UserBundle\Entity\File
     */
    public function getIdentityDocument()
    {
        return $this->identityDocument;
    }

    /**
     * Add beneficiary
     *
     * @param \Cairn\UserBundle\Entity\Beneficiary $beneficiary
     *
     * @return User
     */
    public function addBeneficiary(\Cairn\UserBundle\Entity\Beneficiary $beneficiary)
    {
        $this->beneficiaries[] = $beneficiary;

        return $this;
    }

    /**
     * Remove beneficiary
     *
     * @param \Cairn\UserBundle\Entity\Beneficiary $beneficiary
     */
    public function removeBeneficiary(\Cairn\UserBundle\Entity\Beneficiary $beneficiary)
    {
        $this->beneficiaries->removeElement($beneficiary);
    }

    /**
     * Get beneficiaries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBeneficiaries()
    {
        return $this->beneficiaries;
    }

    /**
     * check if beneficiary exists
     *
     * @param \Cairn\UserBundle\Entity\Beneficiary $beneficiary
     */
    public function hasBeneficiary(\Cairn\UserBundle\Entity\Beneficiary $testBeneficiary)
    {
        $beneficiaries = $this->getBeneficiaries();

        foreach($beneficiaries as $beneficiary){
            if($beneficiary == $testBeneficiary){
                return true;
            }
        }
        return false;
    }

    /**
     * Set passwordTries
     *
     * @param integer $passwordTries
     *
     * @return User
     */
    public function setPasswordTries($passwordTries)
    {
        $this->passwordTries = $passwordTries;
        return $this;
    }

    /**
     * Get passwordTries
     *
     * @return int
     */
    public function getPasswordTries()
    {
        return $this->passwordTries;
    }

    /**
     * Set cardKeyTries
     *
     * @param int $cardKeyTries
     *
     * @return User
     */
    public function setCardKeyTries($cardKeyTries)
    {
        $this->cardKeyTries = $cardKeyTries;

        return $this;
    }

    /**
     * Get cardKeyTries
     *
     * @return int
     */
    public function getCardKeyTries()
    {
        return $this->cardKeyTries;
    }

    /**
     * Set phoneNumberActivationTries
     *
     * @param int $phoneNumberActivationTries
     *
     * @return User
     */
    public function setPhoneNumberActivationTries($phoneNumberActivationTries)
    {
        $this->phoneNumberActivationTries = $phoneNumberActivationTries;

        return $this;
    }

    /**
     * Get phoneNumberActivationTries
     *
     * @return int
     */
    public function getPhoneNumberActivationTries()
    {
        return $this->phoneNumberActivationTries;
    }

    /**
     * Set cardAssociationTries
     *
     * @param int $cardAssociationTries
     *
     * @return User
     */
    public function setCardAssociationTries($cardAssociationTries)
    {
        $this->cardAssociationTries = $cardAssociationTries;

        return $this;
    }

    /**
     * Get cardAssociationTries
     *
     * @return int
     */
    public function getCardAssociationTries()
    {
        return $this->cardAssociationTries;
    }

    /**
     * Set card
     *
     * @param \Cairn\UserBundle\Entity\Card $card
     *
     * @return User
     */
    public function setCard(\Cairn\UserBundle\Entity\Card $card = null)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Get card
     *
     * @return \Cairn\UserBundle\Entity\Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Set notificationData
     *
     * @param \Cairn\UserBundle\Entity\NotificationData $notificationData
     *
     * @return User
     */
    public function setNotificationData(\Cairn\UserBundle\Entity\NotificationData $notificationData = null)
    {
        $this->notificationData = $notificationData;

        return $this;
    }

    /**
     * Get notificationData
     *
     * @return \Cairn\UserBundle\Entity\NotificationData
     */
    public function getNotificationData()
    {
        return $this->notificationData;
    }

    /**
     * Set smsData
     *
     * @param \Cairn\UserBundle\Entity\SmsData $smsData
     *
     * @return User
     */
    public function setSmsData(\Cairn\UserBundle\Entity\SmsData $smsData = null)
    {
        $this->smsData = $smsData;

        return $this;
    }

    /**
     * Get smsData
     *
     * @return \Cairn\UserBundle\Entity\SmsData
     */
    public function getSmsData()
    {
        return $this->smsData;
    }

    /**
     * Set apiClient
     *
     * @param \Cairn\UserBundle\Entity\ApiClient $apiClient
     *
     * @return User
     */
    public function setApiClient(\Cairn\UserBundle\Entity\ApiClient $apiClient = null)
    {
        $this->apiClient = $apiClient;

        return $this;
    }

    /**
     * Get apiClient
     *
     * @return \Cairn\UserBundle\Entity\ApiClient
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set removalRequest
     *
     * @param boolean $removalRequest
     *
     * @return User
     */
    public function setRemovalRequest($removalRequest)
    {
        $this->removalRequest = $removalRequest;

        return $this;
    }

    /**
     * Get removalRequest
     *
     * @return boolean
     */
    public function getRemovalRequest()
    {
        return $this->removalRequest;
    }

    /**
     * Set firstLogin
     *
     * @param boolean $firstLogin
     *
     * @return User
     */
    public function setFirstLogin($firstLogin)
    {
        $this->firstLogin = $firstLogin;

        return $this;
    }

    /**
     * Get firstLogin
     *
     * @return boolean
     */
    public function isFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * check if referent
     *
     * @param \Cairn\UserBundle\Entity\User $referent
     *
     * @return boolean
     */
    public function hasReferent(\Cairn\UserBundle\Entity\User $referent)
    {
        return $this->referents->contains($referent);
    }

    /**
     *
     *function used to trick the ManyToMany relationship when trying to set a single local group referent :
     *in RegistrationType form, the EntityType class, when used with "referents" attribute,
     * needs the option "multiple" set to true(arrayCollection)
     */
    public function setSingleReferent(\Cairn\UserBundle\Entity\User $referent = null)
    {
        if (!$referent) {
            return;
        }

        $this->referents[] = $referent;
    }

    // Which one should it use for pre-filling the form's default data?
    // That's defined by this getter.  I think you probably just want the first?
    public function getSingleReferent()
    {
        return $this->referents->first();
    }
    /**
     * Add referent
     *
     * @param \Cairn\UserBundle\Entity\User $referent
     *
     * @return User
     */
    public function addReferent(\Cairn\UserBundle\Entity\User $referent)
    {
        $this->referents[] = $referent;

        return $this;
    }

    /**
     * Remove referent
     *
     * @param \Cairn\UserBundle\Entity\User $referent
     */
    public function removeReferent(\Cairn\UserBundle\Entity\User $referent)
    {
        $this->referents->removeElement($referent);
    }

    /**
     * Get referents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReferents()
    {
        return $this->referents;
    }

    /**
     * Add referent
     *
     * @param \Cairn\UserBundle\Entity\User $referent
     *
     * @return User
     */
    public function getLocalGroupReferent()
    {
        $referents = $this->getReferents();
        foreach($referents as $referent){
            if($referent->hasRole('ROLE_ADMIN')){
                return $referent;
            }
        }
        return NULL;
    }

    /**
     * Set firstname.
     *
     * @param string|null $firstname
     *
     * @return User
     */
    public function setFirstname($firstname = null)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname.
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Get firstLogin.
     *
     * @return bool
     */
    public function getFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return User
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }
}
