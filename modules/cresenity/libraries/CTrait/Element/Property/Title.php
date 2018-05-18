<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 11:58:27 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Title {

    protected $title;
    protected $rawTitle;

    public function setTitle($title, $lang = true) {
        $this->rawTitle = $title;
        if ($lang) {
            $label = clang::__($title);
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

}
