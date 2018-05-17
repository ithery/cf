<?php

defined('SYSPATH') OR die('No direct access allowed.');

trait CTrait_Compat_AjaxMethod {
    
    public function set_data($key, $data) {
        return $this->setData($key, $data);
    }    
    
    public function set_type($type) {
        return $this->setType($type);
    }
    
    public function set_method($method) {
        return $this->setMethod($method);
    }
    
    public function makeurl($indent = 0) {
        return $this->makeUrl($indent);
    }
}
