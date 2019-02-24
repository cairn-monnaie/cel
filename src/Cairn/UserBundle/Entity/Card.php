<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\User", inversedBy="card")
     *@ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(name="creation_date", type="datetime", unique=false, nullable=true)
     */
    private $creationDate;

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


    public function __construct($user,$rows,$cols,$salt, $code)
    {
        $this->setUser($user);
        $this->setRows($rows);
        $this->setCols($cols);
        $this->setSalt($salt);

        $this->creationDate = new \Datetime();
        $this->setCode($code);
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
     * @return string
     */
    public function getFields()
    {
        return unserialize($this->fields);
    }

    /**
     * Set user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return Card
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
