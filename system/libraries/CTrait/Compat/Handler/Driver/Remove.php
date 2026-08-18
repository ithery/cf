<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CObservable_Listener_Handler_RemoveHandler
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
