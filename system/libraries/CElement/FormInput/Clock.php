<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Clock extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Clock,
        CTrait_Element_Property_Placeholder;

    protected $show_second;

    protected $show_meridian;

    protected $minute_step;

    public function __construct($id) {
        parent::__construct($id);

        $this->type = 'clockpicker';
        $this->show_second = false;
        $this->show_meridian = false;
        $this->minute_step = 1;

        $this->placeholder = '';
        $this->addClass('form-control');
        $dataModule = [
            'css' => [
                'plugins/clockpicker/jquery-clockpicker.css',
                'plugins/clockpicker/bootstrap-clockpicker.css',
            ],
            'js' => [
                'plugins/clockpicker/jquery-clockpicker.js',
                'plugins/clockpicker/bootstrap-clockpicker.js',
            ],
        ];
        CManager::registerModule('clockpicker', $dataModule);
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);

        $js->appendln("$('#" . $this->id . "').clockpicker({");
        $js->appendln("donetext: 'OK'");

        $js->appendln('});');

        return $js->text();
    }
}
