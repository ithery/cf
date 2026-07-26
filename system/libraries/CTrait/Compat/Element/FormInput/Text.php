<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_FormInput_Text
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Text {
    /**
     * @deprecated, please use setPlaceholder
     *
     * @param string $placeholder
     * @param mixed  $lang
     *
     * @return $this
     */
    public function set_placeholder($placeholder, $lang = true) {
        /** @var CElement_FormInput_Text $this */
        return $this->setPlaceholder($placeholder, $lang);
    }

    /**
     * @deprecated, please use the $inputStyle property directly
     *
     * @return null|mixed
     */
    public function get_input_style() {
        /** @var CElement_FormInput_Text $this */
        return $this->inputStyle;
    }

    /**
     * @deprecated, please set the $inputStyle property directly
     *
     * @param mixed $input_style
     *
     * @return $this
     */
    public function set_input_style($input_style) {
        /** @var CElement_FormInput_Text $this */
        $this->inputStyle = $input_style;

        return $this;
    }
}
//@codingStandardsIgnoreEnd
