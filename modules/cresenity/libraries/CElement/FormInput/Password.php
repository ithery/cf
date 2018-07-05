<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 2:34:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Password extends CElement_FormInput {

    use CTrait_Compat_Element_FormInput_Password,
        CTrait_Element_Property_Placeholder,
        CTrait_Element_Property_AutoComplete;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "password";
        $this->autoComplete = false;
        $this->placeholder = "";
        $this->addClass('form-control');
    }

    public function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        $this->setAttr('autocomplete', $this->autoComplete ? 'on' : 'off');
    }

}
