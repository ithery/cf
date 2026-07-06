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
     * Reads an undefined dynamic property -- CElement_FormInput_Text has no
     * $input_style property, so this always returns null.
     *
     * @return null|mixed
     *
     * @deprecated
     */
    public function get_input_style() {
        /** @var CElement_FormInput_Text $this */
        return $this->input_style;
    }

    /**
     * Reads an undefined dynamic property -- CElement_FormInput_Text has no
     * $button_position property, so this always returns null.
     *
     * @return null|mixed
     *
     * @deprecated
     */
    public function get_button_position() {
        /** @var CElement_FormInput_Text $this */
        return $this->button_position;
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
     * Sets an undefined dynamic property -- CElement_FormInput_Text has no
     * $input_style property, so this has no observable effect besides making
     * get_input_style() (above) echo it back.
     *
     * @param mixed $input_style
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_input_style($input_style) {
        /** @var CElement_FormInput_Text $this */
        $this->input_style = $input_style;

        return $this;
    }

    /**
     * Sets an undefined dynamic property -- CElement_FormInput_Text has no
     * $button_position property, so this has no observable effect besides
     * making get_button_position() (above) echo it back.
     *
     * @param mixed $button_position
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_button_position($button_position) {
        /** @var CElement_FormInput_Text $this */
        $this->button_position = $button_position;

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
