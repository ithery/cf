<?php

defined('SYSPATH') OR die('No direct access allowed.');

trait CTrait_Compat_FormInputSelectSearch {
    
    public function set_multiple($bool) {
        return $this->setMultiple($bool);
    }
    
    public function set_delay($val) {
        return $this->setDelay($val);
    }
    
    public function set_auto_select($bool) {
        return $this->setAutoSelect($bool);
    }
    
    public function set_min_input_length($min_input_length) {
        return $this->setMinInputLength($min_input_length);
    }
    
    public function set_key_field($key_field) {
        return $this->setKeyField($key_field);
    }
    
    public function set_search_field($search_field) {
        return $this->setSearchField($search_field);
    }
    
    public function set_query($query) {
        return $this->setQuery($query);
    }
    
    public function set_format_result($fmt) {
        return $this->setFormatResult($fmt);
    }
    
    public function set_format_selection($fmt) {
        return $this->setFormatSelection($fmt);
    }
    
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }
    
    public function add_dropdown_class($c) {
        return $this->addDropdownClass($c);
    }
    
    public function create_ajax_url() {
        return $this->createAjaxUrl();
    }
}
