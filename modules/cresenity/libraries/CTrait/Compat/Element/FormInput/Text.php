<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2018, 2:00:52 PM
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
        return $this->setPlaceholder($placeholder, $lang);
    }

    public function get_input_style() {
        return $this->input_style;
    }

    public function get_button_position() {
        return $this->button_position;
    }

    public function get_action() {
        return $this->action;
    }

    public function set_input_style($input_style) {
        $this->input_style = $input_style;
        return $this;
    }

    public function set_button_position($button_position) {
        $this->button_position = $button_position;
        return $this;
    }

    public function add_action($id = '') {
        $this->action = CElement_Factory::createComponent('Action', $id);
        return $this->action;
    }
}
//@codingStandardsIgnoreEnd
