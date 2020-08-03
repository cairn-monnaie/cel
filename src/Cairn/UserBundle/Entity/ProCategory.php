<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProCategory
 *
 * @ORM\Table(name="pro_category")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\ProCategoryRepository")
 */
class ProCategory
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
     * @ORM\Column(name="slug", type="string", length=50, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    public function __construct(string $slug,string $name)
    {
        $this->setSlug($slug);
        $this->setName($name);
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return ProCategory
     */
    public function setSlug($slug)
    {
        if(! preg_match('/^[a-z][\_\-a-z0-9]*$/', $slug)){
            throw new \Exception('invalid_slug');
        }

        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ProCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
