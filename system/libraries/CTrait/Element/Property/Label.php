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
     * Label of element.
     *
     * @var string
     */
    protected $label;

    /**
     * Label of element before translation.
     *
     * @var string
     */
    protected $rawLabel;

    /**
     * @param string     $label
     * @param bool|array $lang
     *
     * @return $this
     */
    public function setLabel($label, $lang = true) {
        if (!is_string($label)) {
            $label = '';
        }
        $this->rawLabel = $label;
        if ($lang !== false) {
            $label = c::__($label, is_array($lang) ? $lang : []);
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
