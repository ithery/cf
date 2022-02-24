<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:45:46 PM
 */
// @codingStandardsIgnoreStart

trait CTrait_Compat_Handler_Driver_Remove {
    /**
     * @param string $parent
     *
     * @return $this
     * @deprecated, please use setParent
     */
    public function set_parent($parent) {
        return $this->setParent($parent);
    }
}
