<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_MiniColor extends CElement_FormInput {
    use CTrait_Element_Property_Placeholder;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'text';
        $this->addClass('form-control');
        $this->placeholder = '';
        CManager::registerModule('minicolors');
    }

    public function build() {
        $this->setAttr('placeholder', $this->placeholder);
        $this->setAttr('value', $this->value);
        $this->addClass('cres:element:control:ColorPicker');
        $this->setAttr('cres-element', 'control:ColorPicker');
        $this->setAttr('cres-config', c::json($this->buildControlConfig()));
    }

    protected function buildControlConfig() {
        $config = [
            'applyJs' => 'minicolor',
            'minicolorsOptions' => [
                'control' => 'hue',
                'position' => 'bottom left',
            ]
        ];

        return $config;
    }
}
