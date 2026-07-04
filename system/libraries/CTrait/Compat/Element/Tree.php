<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Tree
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Tree {
    /**
     * @param array $custom_field_data
     *
     * @deprecated since version 1.2, please use setCustomFieldData
     *
     * @return $this
     */
    public function set_custom_field_data($custom_field_data) {
        return $this->setCustomFieldData($custom_field_data);
    }

    /**
     * @param callable          $callback
     * @param array|string|null $require
     *
     * @deprecated since version 1.2, please use setCallback
     *
     * @return $this
     */
    public function set_callback(callable $callback, $require = null) {
        return $this->setCallback($callback, $require);
    }
}
//@codingStandardsIgnoreEnd
