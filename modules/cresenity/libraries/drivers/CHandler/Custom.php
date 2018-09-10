<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Custom_Driver extends CHandler_Driver {

    use CTrait_Compat_Handler_Driver_Custom;

    protected $target;
    protected $js;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
    }

    public function setJs($js) {

        $this->js = $js;

        return $this;
    }

    public function script() {
        $js = parent::script();
        $js .= $this->js;


        return $js;
    }

}
