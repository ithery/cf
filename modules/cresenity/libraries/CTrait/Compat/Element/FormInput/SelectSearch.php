<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_SelectSearch {
    protected $format_selection;

    protected $format_result;

    protected $key_field;

    protected $search_field;

    protected $auto_select;

    protected $min_input_length;

    protected $dropdown_classes;

    public function set_multiple($bool) {
        return $this->setMultiple($bool);
    }

    public function set_delay($val) {
        return $this->setDelay($val);
    }

    public function set_auto_select($bool) {
        $this->auto_select = $bool;
        return $this->setAutoSelect($bool);
    }

    public function set_min_input_length($min_input_length) {
        $this->min_input_length = $min_input_length;
        return $this->setMinInputLength($min_input_length);
    }

    public function set_key_field($key_field) {
        $this->key_field = $key_field;
        return $this->setKeyField($key_field);
    }

    public function set_search_field($search_field) {
        $this->search_field = $search_field;
        return $this->setSearchField($search_field);
    }

    public function set_query($query) {
        return $this->setQuery($query);
    }

    public function set_format_result($fmt) {
        $this->format_result = $fmt;
        return $this->setFormatResult($fmt);
    }

    public function set_format_selection($fmt) {
        $this->format_selection = $fmt;
        return $this->setFormatSelection($fmt);
    }

    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }

    public function add_dropdown_class($c) {
        if (is_array($c)) {
            $this->dropdown_classes = array_merge($c, $this->dropdown_classes);
        } else {
            if ($this->bootstrap == '3.3') {
                $c = str_replace('span', 'col-md-', $c);
                $c = str_replace('row-fluid', 'row', $c);
            }
            $this->dropdown_classes[] = $c;
        }
        return $this->addDropdownClass($c);
    }

    public function create_ajax_url() {
        return $this->createAjaxUrl();
    }
}
