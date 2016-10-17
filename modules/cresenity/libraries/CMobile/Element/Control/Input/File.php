<?php

class CMobile_Element_Control_Input_File extends CMobile_Element_Control_AbstractInput {

    public function __construct($id) {
        parent::__construct($id);
        //CManager::instance()->register_module('datepicker_material');

        $this->type = "file";
		
    }

    public static function factory($id) {
        return new CMobile_Element_Control_Input_File($id);
    }

    protected function html_attr() {
        $html_attr = parent::html_attr();
        return $html_attr;
    }

    protected function build() {
        $this->set_attr('type', $this->type);
        $value = '';
        if(strlen($this->value) > 0) {
            $value = $this->value;
        }
        $this->add_class('validate');
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js());
        return $js->text();
    }
}
