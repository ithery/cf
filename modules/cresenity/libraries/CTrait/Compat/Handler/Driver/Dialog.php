<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 3:22:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Handler_Driver_Dialog {

    /**
     * 
     * @deprecated, please use setTitle
     * @param string $title
     * @return $this
     */
    public function set_title($title) {
        return $this->setTitle($title);
    }

    public function add_param_input($inputs) {
        return $this->addParamInput($inputs);
    }
}
