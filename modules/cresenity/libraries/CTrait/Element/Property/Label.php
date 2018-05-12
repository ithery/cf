<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:11:13 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Label {

    protected $label;
    protected $rawLabel;

    public function setLabel($label, $lang = true) {
        $this->rawLabel = $label;
        if ($lang) {
            $label = clang::__($label);
        }
        $this->label = $label;
        return $this;
    }

    public function getLabel() {
        return $this->rawLabel;
    }

    public function getTranslationLabel() {
        return $this->label;
    }

}
