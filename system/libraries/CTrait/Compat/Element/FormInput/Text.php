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
     * @deprecated, please use the $buttonPosition property directly
     *
     * @return null|mixed
     */
    public function get_button_position() {
        /** @var CElement_FormInput_Text $this */
        return $this->buttonPosition;
    }

    /**
     * Reads an undefined dynamic property unless add_action() (below) was
     * called first on this same instance.
     *
     * @return null|CElement_Component_Action
     *
     * @deprecated
     */
    public function get_action() {
        /** @var CElement_FormInput_Text $this */
        return $this->action;
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

    /**
     * @deprecated, please set the $buttonPosition property directly
     *
     * @param mixed $button_position
     *
     * @return $this
     */
    public function set_button_position($button_position) {
        /** @var CElement_FormInput_Text $this */
        $this->buttonPosition = $button_position;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Action
     *
     * @deprecated
     */
    public function add_action($id = '') {
        /** @var CElement_FormInput_Text $this */
        $this->action = CElement_Factory::createComponent('Action', $id);

        return $this->action;
    }
}
//@codingStandardsIgnoreEnd
