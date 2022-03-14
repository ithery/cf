<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_SelectTag {
    public function set_multiple($bool) {
        $this->multiple = $bool;

        return $this;
    }

    public function set_min_input_length($min_input_length) {
        $this->min_input_length = $min_input_length;

        return $this;
    }

    public function set_key_field($key_field) {
        $this->key_field = $key_field;

        return $this;
    }

    public function set_search_field($search_field) {
        $this->search_field = $search_field;

        return $this;
    }

    public function set_query($query) {
        $this->query = $query;

        return $this;
    }

    public function set_format_result($fmt) {
        $this->format_result = $fmt;

        return $this;
    }

    public function set_format_selection($fmt) {
        $this->format_selection = $fmt;

        return $this;
    }

    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;

        return $this;
    }
}
