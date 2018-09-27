<?php
//src/Cairn/UserBundle/Entity/User.php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

use FOS\UserBundle\Model\User as BaseUser;

use Cairn\UserBundle\Entity\Card;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="cairn_user")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields = {"name"},message="Ce nom est déjà utilisé") 
 * @UniqueEntity(fields = {"username"},message="Ce pseudo est déjà utilisé") 
 * @UniqueEntity(fields = {"email"},message="Cette adresse email est déjà utilisée") 
 * @UniqueEntity(fields = {"cyclosID"},message="Cet ID est déjà utilisé") 
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
     * @ORM\Column(name="name", type="string", unique=true, nullable=true)
     * @Assert\Length(min=4,minMessage="{{ limit }} caractères minimum")
     */
    private $name; 

    /**
     * @ORM\Column(name="cyclos_id", type="bigint", unique=true, nullable=false)
     * @Assert\Length(min=19, minMessage="Contient exactement {{ limit }} chiffres")
     */
    private $cyclosID; 

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\Address", cascade={"persist","remove"})
     *@Assert\Valid()
     */ 
    private $address;

    /**
     * @ORM\Column(name="description", type="text", unique=false)
     * @Assert\NotBlank(message="Entrez une description de votre activité.")
     * @Assert\Length(min=20, minMessage="Description un peu courte. {{ limit }} caractères minimum")
     */
    private $description; 

    /**
     * @ORM\Column(name="creation_date", type="datetime", unique=false, nullable=false)
     */
    private $creationDate; 

    /**
     * @var ArrayCollection
     *@ORM\ManyToMany(targetEntity="Cairn\UserBundle\Entity\User", cascade={"persist"})
     *@ORM\JoinColumn(referencedColumnName="id")
     *
     */
    private $referents;

    /**
     *@ORM\ManyToMany(targetEntity="Cairn\UserBundle\Entity\Beneficiary", mappedBy="sources",  cascade={"persist"})
     */ 
    private $beneficiaries;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\Image", cascade={"persist","remove"})
     */
    private $image;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\Card", mappedBy="user", cascade={"persist","remove"})
     */
    private $card;

    /**
     * @ORM\Column(name="nb_cards", type="smallint", unique=false, nullable=false)
     */
    private $nbCards;

    /**
     * @ORM\Column(name="pwd_tries", type="smallint", unique=false, nullable=false)
     */
    private $passwordTries;

    /**
     * @ORM\Column(name="card_key_tries", type="smallint", unique=false, nullable=false)
     */
    private $cardKeyTries;


    public function __construct()
    {
        parent::__construct();
        self::$_counter ++;
        $this->creationDate = new \Datetime();
        $this->beneficiaries = new ArrayCollection();
        $this->referents = new ArrayCollection();
        $this->setPasswordTries(0);
        $this->setCardKeyTries(0);
        $this->setNbCards(0);
    }

    public function getCity()
    {
        return $this->getAddress()->getZipCity()->getCity();
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
     *
     *@Assert\Callback() 
     */
    public function isUserValid(ExecutionContextInterface $context)
    {

        //check length for example
        if(strlen($this->getUsername()) < 5){
            $context->buildViolation('Login trop court ! 5 caractères minimum')
                ->atPath('username')
                ->addViolation();
        }

        if(preg_match('#[^\w\.]#',$this->getUsername())){
            $context->buildViolation('Le pseudo contient uniquement des caractères alphanumériques, tirets de soulignements ou point')
                ->atPath('username')
                ->addViolation();

        }

        if(strlen($this->getUsername()) > 16){
            $context->buildViolation('Le pseudo doit contenir moins de 16 caractères.')
                ->atPath('username')
                ->addViolation();

        }
        if(preg_match('#'.$this->getUsername().'#',$this->getPlainPassword())){
            $context->buildViolation('Le pseudo ne peut pas être contenu dans le mot de passe.')
                ->atPath('plainPassword')
                ->addViolation();

        }
        if( preg_match('#^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$#',$this->getPlainPassword())){
            $context->buildViolation('Le mot de passe doit contenir un caractère spécial.')
                ->atPath('plainPassword')
                ->addViolation();
        }
        if(!preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,3}$#',$this->getEmail())){
            $context->buildViolation("Email invalide. Un email ne contient ni majuscule ni accent.Le symbole @ est suivi d\'au moins 2 chiffres/lettres, et le point de 2 ou 3 lettres.")
                ->atPath('email')
                ->addViolation();
        }

    }

    /*
     *@ORM\PostRemove
     */
    private static function decreaseCounter()
    {
        self::$_counter--;
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

    //    public function fromDTOToEntity($userDTO)
    //    {
    //        $this->setUsername($userDTO->username);
    //        $this->setEmail($userDTO->email);
    //        $this->setPassword($userDTO->passwords->value);
    //        if(property_exists($userDTO,'id')){
    //            $this->setCyclosID($userDTO->id);
    //        }
    //    }   

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

    /**
     *@ORM\PreUpdate
     */ 
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
     * @param \Cairn\UserBundle\Entity\Image $image
     *
     * @return User
     */
    public function setImage(\Cairn\UserBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Cairn\UserBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
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
        if($this->passwordTries >= 3){
            $this->setEnabled(false);
        }
        return $this;
    }

    /**
     * Get passwordTries
     *
     * @return integer
     */
    public function getPasswordTries()
    {
        return $this->passwordTries;
    }

    /**
     * Set cardKeyTries
     *
     * @param integer $cardKeyTries
     *
     * @return User
     */
    public function setCardKeyTries($cardKeyTries)
    {
        $this->cardKeyTries = $cardKeyTries;
        if($this->cardKeyTries >= 3){
            $this->setEnabled(false);
        }

        return $this;
    }

    /**
     * Get cardKeyTries
     *
     * @return integer
     */
    public function getCardKeyTries()
    {
        return $this->cardKeyTries;
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
        if($card){
            $this->setNbCards($this->getNbCards() + 1);
        }
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
     * Set nbCards
     *
     * @param integer $nbCards
     *
     * @return User
     */
    public function setNbCards($nbCards)
    {
        $this->nbCards = $nbCards;

        return $this;
    }

    /**
     * Get nbCards
     *
     * @return integer
     */
    public function getNbCards()
    {
        return $this->nbCards;
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
}
