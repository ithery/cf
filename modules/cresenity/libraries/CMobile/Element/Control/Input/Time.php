<?php

class CMobile_Element_Control_Input_Time extends CMobile_Element_Control_AbstractInput {

    public function __construct($id) {
        parent::__construct($id);
        //CManager::instance()->register_module('datepicker_material');

        $this->type = "text";
		
    }

    public static function factory($id) {
        return new CMobile_Element_Control_Input_Time($id);
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
        // $this->set_attr('data-value', $value);
        $this->add_class('timepicker');
        $this->add_class('validate');
        
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js());
        $js->append("$('#" . $this->id . "').pickatime({
                        autoclose: true,
                      });");
        
        return $js->text();
    }
}
