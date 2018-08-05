<?php

defined('SYSPATH') OR die('No direct access allowed.');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CTrait_Compat_Handler_Driver_Reload {

    /**
     * 
     * @deprecated, please use setTarget
     * @param string $target
     * @return $this
     */
    public function set_target($target) {
        return $this->setTarget($target);
    }
    
    /**
     * 
     * @deprecated, please use setMethod
     * @param string $method
     * @return $this
     */
    public function set_method($method) {
        return $this->setMethod($method);
    }

}
