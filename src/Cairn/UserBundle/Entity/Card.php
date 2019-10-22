<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Card
 *
 * @ORM\Table(name="card")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\CardRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Card
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
     * @var text
     *
     * @ORM\Column(name="fields", type="text", length=400)
     */
    private $fields;

    /**
     * @var int
     *
     * @ORM\Column(name="rows", type="smallint")
     */
    private $rows;

    /**
     * @var int
     *
     * @ORM\Column(name="cols", type="smallint")
     */
    private $cols;

    /**
     *@ORM\OneToMany(targetEntity="Cairn\UserBundle\Entity\User", mappedBy="card")
     *@ORM\JoinColumn(nullable=true)
     */
    private $users;

    /**
     * @ORM\Column(name="creation_date", type="datetime", unique=false, nullable=true)
     */
    private $creationDate;

    /**
     * @ORM\Column(name="expiration_date", type="datetime", unique=false, nullable=true)
     */
    private $expirationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=400,nullable=false)
     */
    private $salt;

    /**
     * @var int
     *
     * @ORM\Column(name="code", type="string", length=10)
     */
    private $code;


    public function __construct($rows,$cols,$salt, $code, $expirationDelay = NULL)
    {
        $this->users = new ArrayCollection();

        $this->setRows($rows);
        $this->setCols($cols);
        $this->setSalt($salt);

        $this->creationDate = new \Datetime();
        $this->setCode($code);

        if($expirationDelay){
            $delay = date_modify(new \Datetime(),'+ '.$expirationDelay.' days');
            $this->setExpirationDate($delay);
        }
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Generates a card with dimensions defined as global parameter
     *
     * Using random values is legit for production environment, but is impossible to use for automatic testing.
     * For this reason, the card keys will depend on the environment
     */
    public function generateCard($env)
    {
        $matrix = array();
        for($row = 0; $row < $this->getRows(); $row++){
            $line = array();
            for($col =0; $col < $this->getCols();$col++){
                if($env != 'prod'){
                    $line[] = 1111;
                }
                else{
                    $line[] =  rand(1000,9999);
                }
            }
            $matrix[] = $line;
        }

        $this->setFields($matrix);
        return $this->getFields();

    }

    public function getKey($index)
    {
        $rows = $this->getRows();                                       

        $pos_row = intdiv($index,$rows);                                    
        $pos_col = $index % $rows;                                          
        return $this->getFields()[$pos_row][$pos_col];
    }

    /**                                                                        
     * Generates a random array index and its equivalent string cell position  
     *                           
     * Possible to retrieve positions from fixed index or fixed string                                              
     * @example For a 5x5 card. index 7 equals cell position B2                
     * @param Card $card                                                       
     * @return stdClass with attributes cell and index                         
     */                                                                        
    public function generateCardPositions($position = NULL)
    {
        $rows = $this->getRows();
        $nbFields = $rows * $this->getCols();

        if(!$position){
            $position = rand(0,$nbFields-1);
        }
        $pos_row = intdiv($position,$rows);
        $pos_col = $position % $rows;
        $string_pos = chr(65+ $pos_row) . strval($pos_col + 1);

        return ['cell' => $string_pos ,'index'=>$position];
    }
    /**
     * Set fields
     *
     * @param string $fields
     *
     * @return Card
     */
    public function setFields($fields)
    {

        $this->fields = serialize($fields);

        return $this;
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function getFields()
    {
        return unserialize($this->fields);
    }


    /**
     * Add user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return User
     */
    public function addUser(\Cairn\UserBundle\Entity\User $user = NULL)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     */
    public function removeUser(\Cairn\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
        $user->setCard(NULL);

        return count($this->users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set rows
     *
     * @param integer $rows
     *
     * @return Card
     */
    public function setRows($rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Get rows
     *
     * @return integer
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Set cols
     *
     * @param integer $cols
     *
     * @return Card
     */
    public function setCols($cols)
    {
        $this->cols = $cols;

        return $this;
    }

    /**
     * Get cols
     *
     * @return integer
     */
    public function getCols()
    {
        return $this->cols;
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Card
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set expirationDate
     *
     * @param \DateTime $expirationDate
     *
     * @return Card
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

     /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Card
     */
   public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

     /**
     * Get salt
     *
     * @return string
     */
   public function getSalt()
    {
        return $this->salt;
    }

     /**
     * Set code
     *
     * @param string $code
     *
     * @return Card
     */
   public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

     /**
     * Get code
     *
     * @return string
     */
   public function getCode()
    {
        return $this->code;
    }

}
