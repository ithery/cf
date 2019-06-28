<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 3:22:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Handler_Driver_Dialog {

    protected $js_class;
    protected $js_class_manual;

    /**
     * @deprecated since version 1.2
     * @param type $js_class
     * @return $this
     */
    public function set_js_class($js_class) {
        //set js class manual
        $this->js_class_manual = $js_class;
        return $this;
    }

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
