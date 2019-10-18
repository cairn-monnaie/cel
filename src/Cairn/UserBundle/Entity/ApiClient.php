<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * ApiClient
 *
 * @ORM\Table(name="api_client")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\ApiClientRepository")
 */
class ApiClient
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
     * @ORM\Column(name="login", type="string", length=100, unique=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="accessToken", type="string", length=255, unique=true)
     */
    private $accessToken;

    /**
     * @var string|null
     *
     * @ORM\Column(name="webhook", type="string", length=255, nullable=true, unique=true)
     */
    private $webhook;

    /**
     *@ORM\OneToOne(targetEntity="Cairn\UserBundle\Entity\User", inversedBy="apiClient", cascade={"persist"})
     *@ORM\JoinColumn(name="user_id", nullable=false,referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    public function __construct(User $user = null)
    {
        $this->setUser($user);
    }

    /**
     *
     *@Assert\Callback() 
     */
    public function isApiClientValid(ExecutionContextInterface $context)
    {
        if(! preg_match('#^[a-zA-Z0-9\-]*$#',$this->getLogin()) ){
            $context->buildViolation('Login invalide : seuls les chiffres, lettres et traits d union sont autorisés')
                ->atPath('login')
                ->addViolation();
        }
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
     * Set login.
     *
     * @param string $login
     *
     * @return ApiClient
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set accessToken.
     *
     * @param string $accessToken
     *
     * @return ApiClient
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set webhook.
     *
     * @param string|null $webhook
     *
     * @return ApiClient
     */
    public function setWebhook($webhook = null)
    {
        $this->webhook = $webhook;

        return $this;
    }

    /**
     * Get webhook.
     *
     * @return string|null
     */
    public function getWebhook()
    {
        return $this->webhook;
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
