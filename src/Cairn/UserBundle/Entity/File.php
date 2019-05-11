<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * File
 *
 * @ORM\Table(name="File")
 * @ORM\Entity(repositoryClass="Cairn\UserBundle\Repository\FileRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class File
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
     * @ORM\Column(name="url", type="string", length=255, unique=false, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255,nullable=true)
     */
    private $alt;


    private $file;

    private $tempFilename;

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
     * Set url
     *
     * @param string $url
     *
     * @return File
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return File
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {

        $this->file = $file;

        if(null !== $this->url){
            $this->tempFileName = $this->url;
//            $this->url = null;
            $this->alt = null;
        }else{
            //little trick to allow PreUpdate callback to be called if the user had no file before
            //if no attribute is modified, PreUpdate is not called, then PreUpload function is not called and
            //finally the file is not uploaded
            $this->url = 'xxx';
        }

    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null === $this->file) {
            return;
        }

        $this->url = $this->file->guessExtension();
        $this->alt = $this->file->getClientOriginalName();
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $this->file->move(
            $this->getUploadRootDir(), // Le répertoire de destination
            $this->id.'.'.$this->url   // Le nom du fichier à créer, ici « id.extension »
        );

    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->url;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFilename)) {
            unlink($this->tempFilename);
        }
    }

    public function getUploadDir($env = NULL)
    {
        //chemin relatif pour navigateur utilisateur 
        if ($env == 'test'){
            return 'web/uploads/img';
        }
        return 'uploads/img';
    }

    protected function getUploadRootDir()
    {
        // chemin relatif vers répertoire web
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getWebPath($env = NULL)
    {
        return $this->getUploadDir($env).'/'.$this->getId().'.'.$this->getUrl();
    }

}

