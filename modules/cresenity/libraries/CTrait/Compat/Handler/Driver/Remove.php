<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 3:45:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Handler_Driver_Remove {

    /**
     * 
     * @deprecated, please use setParent
     * @param string $parent
     * @return $this
     */
    public function set_parent($parent) {
        return $this->setParent($parent);
    }

}
