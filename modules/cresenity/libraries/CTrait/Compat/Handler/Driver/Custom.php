<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 3:22:30 PM
 */
// @codingStandardsIgnoreStart

trait CTrait_Compat_Handler_Driver_Custom {
    /**
     * @param string $js
     *
     * @return $this
     * @deprecated, please use setJs
     */
    public function set_js($js) {
        return $this->setJs($js);
    }
}
