<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:07:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Handler_Driver_Append {

    /**
     * 
     * @deprecated, please use setMethod
     * @param string $method
     * @return $this
     */
    public function set_method($method) {
        return $this->setMethod($method);
    }

    /**
     * 
     * @deprecated, please use addParamInput
     * @param type $inputs
     * @return type
     */
    public function add_param_input($inputs) {
        return $this->addParamInput($inputs);
    }

    /**
     * 
     * @deprecated, please use addParamInputByName
     * @param type $inputs
     * @return type
     */
    public function add_param_input_by_name($inputs) {
        return $this->addParamInputByName($inputs);
    }

}
