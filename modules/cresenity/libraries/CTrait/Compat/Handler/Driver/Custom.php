<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 3:22:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Handler_Driver_Custom {

    /**
     * 
     * @deprecated, please use setJs
     * @param string $js
     * @return $this
     */
    public function set_js($js) {
        return $this->setJs($target);
    }

}
