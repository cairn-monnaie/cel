<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppData
 *
 * @ORM\Table(name="app_data")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\AppDataRepository")
 */
class AppData
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
     * @var int|null
     *
     * @ORM\Column(name="pinCode", type="smallint", nullable=true)
     */
    private $pinCode;

    /**
     * @var bool
     *
     * @ORM\Column(name="firstLogin", type="boolean")
     */
    private $firstLogin;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\User", inversedBy="appData", cascade={"persist"})
     *@ORM\JoinColumn(name="user_id", nullable=false,referencedColumnName="id")
     */
    private $user;


    public function __construct()
    {
        $this->setFirstLogin(true);
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
     * Set pinCode.
     *
     * @param int|null $pinCode
     *
     * @return AppData
     */
    public function setPinCode($pinCode = null)
    {
        $this->pinCode = $pinCode;

        return $this;
    }

    /**
     * Get pinCode.
     *
     * @return int|null
     */
    public function getPinCode()
    {
        return $this->pinCode;
    }

    /**
     * Set firstLogin.
     *
     * @param bool $firstLogin
     *
     * @return AppData
     */
    public function setFirstLogin($firstLogin)
    {
        $this->firstLogin = $firstLogin;

        return $this;
    }

    /**
     * Get firstLogin.
     *
     * @return bool
     */
    public function isFirstLogin()
    {
        return $this->firstLogin;
    }


    /**
     * Set user
     *
     * @param \Cairn\UserBundle\Entity\User $user
     *
     * @return AppData
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
