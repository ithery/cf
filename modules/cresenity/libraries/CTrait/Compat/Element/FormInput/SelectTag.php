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
}
