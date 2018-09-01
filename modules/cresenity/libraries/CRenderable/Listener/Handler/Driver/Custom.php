<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CRenderable_Listener_Handler_Driver_Custom extends CRenderable_Listener_Handler_Driver {

    use CTrait_Compat_Handler_Driver_Custom;

    protected $target;
    protected $js;

    public function setJs($js) {

        $this->js = $js;

        return $this;
    }

    public function script() {
        $js = '';
        $js .= $this->js;


        return $js;
    }

}
