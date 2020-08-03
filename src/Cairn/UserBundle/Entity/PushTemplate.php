<?php

namespace Cairn\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PushTemplate
 *
 */
class PushTemplate
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $actionTitle;

    /**
     * @var string
     */
    private $redirectionUrl;


    /**
     * Set title.
     *
     * @param string $title
     *
     * @return PushTemplate
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return PushTemplate
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set actionTitle.
     *
     * @param string $actionTitle
     *
     * @return PushTemplate
     */
    public function setActionTitle($actionTitle)
    {
        $this->actionTitle = $actionTitle;

        return $this;
    }

    /**
     * Get actionTitle.
     *
     * @return string
     */
    public function getActionTitle()
    {
        return $this->actionTitle;
    }


    /**
     * Set redirectionUrl.
     *
     * @param string $redirectionUrl
     *
     * @return PushTemplate
     */
    public function setRedirectionUrl($redirectionUrl)
    {
        $this->redirectionUrl = $redirectionUrl;

        return $this;
    }

    /**
     * Get redirectionUrl.
     *
     * @return string
     */
    public function getRedirectionUrl()
    {
        return $this->redirectionUrl;
    }
}
