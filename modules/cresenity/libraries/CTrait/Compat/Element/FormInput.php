<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 2:29:43 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput {

    /**
     * @deprecated since version 1.2
     */
    public function set_value($val) {
        return $this->setValue($val);
    }
    
    public function set_submit_onchange($bool) {
        return $this->setSubmitOnchange($bool);
    }    
    
    public function set_ajax($bool) {
        return $this->setAjax($bool);
    }
    
    public function set_disabled($bool) {
        return $this->setDisabled($bool);
    }
    
    public function set_size($size) {
        return $this->setSize($size);
    }
    
    public function set_readonly($bool) {
        return $this->setReadOnly($bool);
    }
    
    public function get_field_id() {
        return $this->getFieldId();
    }
    
    public function add_transform($name, $args = array()) {
        return $this->addTransform($name, $args);
    }
    
    public function set_list($list) {
        return $this->setList($list);
    }
    
    public function set_name($val) {
        return $this->setName($val);
    }
    
    public function add_validation($name, $value = "") {
        return $this->setValidation($name, $value);
    }
    
    public function set_type($type) {
        return $this->setType($type);
    }
    
    public function set_on_text($text) {
        return $this->setOnText($text);
    }
    
    public function set_off_text($text) {
        return $this->setOffText($text);
    }
    
    public function set_checked($bool) {
        return $this->setChecked($bool);
    }
    
    public function show_updown() {
        return $this->showUpdown();
    }
    
    public function hide_updown() {
        return $this->hideUpdown();
    }
    
    public function toarray() {
        return $this->toArray();
    }
    
    protected function html_attr() {
        return $this->htmlAttr();
    }

}
