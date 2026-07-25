<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CApp
 */
trait CApp_Concern_TitleTrait {
    /**
     * @var null|string
     */
    protected $title;

    /**
     * @var null|string
     */
    protected $rawTitle;

    /**
     * @var bool
     */
    private $showTitle = true;

    /**
     * @param string $title
     * @param bool   $lang
     *
     * @return $this
     */
    public function setTitle($title, $lang = true) {
        /** @var CApp $this */
        $this->rawTitle = $title;
        if ($lang) {
            $title = c::__($title);
        }
        $this->title = $title;

        $this->seo()->setTitle($title);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle() {
        return $this->rawTitle;
    }

    /**
     * @return null|string
     */
    public function getTranslationTitle() {
        return $this->title;
    }

    /**
     * @return bool
     */
    public function haveTitle() {
        return strlen($this->title) > 0;
    }

    /**
     * Call getTitle if parameter title is not passed
     * Call setTitle if parameter title is passed.
     *
     * @param string $title
     * @param bool   $lang
     *
     * @return mixed
     */
    public function title($title = null, $lang = true) {
        if ($title != null) {
            return $this->setTitle($title, $lang);
        }

        return $this->getTitle();
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function showTitle($bool) {
        $this->showTitle = $bool;

        return $this;
    }
}
