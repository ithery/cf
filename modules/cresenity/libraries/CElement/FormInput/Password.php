<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2018, 2:34:55 PM
 */
class CElement_FormInput_Password extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Password,
        CTrait_Element_Property_Placeholder,
        CTrait_Element_Property_AutoComplete;

    private $showPassword = false;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'password';
        $this->autoComplete = false;
        $this->placeholder = '';
        $this->addClass('form-control');
    }

    public function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        $this->setAttr('placeholder', $this->placeholder);
        $this->setAttr('autocomplete', $this->autoComplete ? 'on' : 'off');

        if ($this->readonly) {
            $this->setAttr('readonly', 'readonly');
        }
    }

    public function after() {
        $after = parent::after();
        if ($this->showPassword) {
            $span = $after->addSpan();
            $span->addClass('input-group-btn show-password text-muted fa fa-eye-slash');
        }

        return $after;
    }

    public function setShowPassword($bool = true) {
        $this->showPassword = $bool;
        return $this;
    }
}
