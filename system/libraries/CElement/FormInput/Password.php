<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Password extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Password,
        CTrait_Element_Property_Placeholder,
        CTrait_Element_Property_AutoComplete;

    /**
     * @var bool
     */
    private $showPassword = false;

    /**
     * @var bool
     */
    private $toggleVisibility = false;

    /**
     * @param null|string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'password';
        $this->autoComplete = false;
        $this->placeholder = '';
        $this->addClass('form-control');
    }

    /**
     * @return void
     */
    public function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        $this->setAttr('placeholder', $this->placeholder);
        $this->setAttr('autocomplete', $this->autoComplete ? 'on' : 'off');
        $this->addClass('cres:element:control:Password');
        $this->setAttr('cres-element', 'control:Password');
        $this->setAttr('cres-config', c::json($this->buildControlConfig()));
        if ($this->readonly) {
            $this->setAttr('readonly', 'readonly');
        }
        if ($this->showPassword) {
            $span = $this->after()->addSpan();
            $span->addClass('input-group-btn show-password text-muted fa fa-eye-slash');
        }
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setShowPassword($bool = true) {
        $this->showPassword = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setToggleVisibility($bool = true) {
        $this->toggleVisibility = $bool;

        return $this;
    }

    /**
     * @return array
     */
    protected function buildControlConfig() {
        $config = [
            'toggleVisibility' => (bool) $this->toggleVisibility,
        ];

        return $config;
    }
}
