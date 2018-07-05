<?php

defined('SYSPATH') OR die('No direct access allowed.');

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

}
