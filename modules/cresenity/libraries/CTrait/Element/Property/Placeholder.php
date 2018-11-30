<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 2:38:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Placeholder {

    /**
     *
     * @var string 
     */
    public $placeholder;

    /**
     *
     * @var string 
     */
    public $rawPlaceholder;

    /**
     * 
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder, $lang = true) {
        $this->rawPlaceholder = $placeholder;
        if ($lang) {
            $placeholder = clang::__($placeholder);
        }
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getPlaceholder() {
        return $this->rawPlaceholder;
    }

    
    /**
     * 
     * @return string
     */
    public function getTranslationPlaceholder() {
        return $this->placeholder;
    }
}
