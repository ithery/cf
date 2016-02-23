<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Label extends CMobile_Element {
    protected $label = "";
    protected $data_error;
    protected $data_success;
    protected $for_id;
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "label";
        $this->label = "";
        $this->data_error = '';
        $this->data_success = '';
    }

    public static function factory($id = "") {
        return new CMobile_Element_Label($id);
    }

    public function set_label($label) {
        $this->label = $label;
        return $this;
    } 

    public function set_for_id($for_id) {
        $this->for_id = $for_id;
        return $this;
    } 

    public function set_data_error($data_error){
        $this->data_error = $data_error; 
        return $this;
    }

    public function set_data_success($data_success){
        $this->data_success = $data_success; 
        return $this;
    }

    protected function html_attr() {
        $html_attr = parent::html_attr();
        $data_error = "";
        $data_success = "";
        $for_id = "";
        
        if(strlen($this->data_error) > 0) {
            $data_error = ' data-error="'.$this->data_error.'"';
        }
        if(strlen($this->data_success) > 0) {
            $data_success = ' data-success="'.$this->data_success.'"';
        }
        if(strlen($this->for_id) > 0) {
            $for_id = ' for="'.$this->for_id.'"';
        }
        $html_attr .= $for_id;
        $html_attr .= $data_error;
        $html_attr .= $data_success;
        return $html_attr;
    }

    public function html($indent = 0) {
        $this->add($this->label);
        return parent::html($indent);
    }

    public function js($indent = 0) {
        return parent::js($indent);
    }

}
