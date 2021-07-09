<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:11:13 AM
 */
trait CTrait_Element_Property_Label {
    /**
     * Label of element
     *
     * @var string
     */
    protected $label;

    /**
     * Label of element before translation
     *
     * @var string
     */
    protected $rawLabel;

    /**
     * @param string $label
     * @param string $lang
     *
     * @return $this
     */
    public function setLabel($label, $lang = true) {
        $this->rawLabel = $label;
        if ($lang) {
            $label = clang::__($label);
        }
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->rawLabel;
    }

    /**
     * @return string
     */
    public function getTranslationLabel() {
        return $this->label;
    }
}
