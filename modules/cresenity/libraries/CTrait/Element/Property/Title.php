<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 11:58:27 PM
 */
trait CTrait_Element_Property_Title {
    protected $title;
    protected $rawTitle;

    public function setTitle($title, $lang = true) {
        $this->rawTitle = $title;
        if ($lang) {
            $title = clang::__($title);
        }
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->rawTitle;
    }

    public function getTranslationTitle() {
        return $this->title;
    }

    public function haveTitle() {
        return strlen($this->title) > 0;
    }

    /**
     * Call getTitle if parameter title is not passed
     * Call setTitle if parameter title is passed
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
}
