<?php

defined('SYSPATH') OR die('No direct access allowed.');

trait CTrait_Compat_Handler_Driver {
    
    public function set_url($url) {
        return $this->setUrl($url);
    }
}
