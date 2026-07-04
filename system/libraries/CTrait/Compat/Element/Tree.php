<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Tree
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Tree {
    /**
     * @param array $data
     *
     * @deprecated since version 1.2, please use setData
     *
     * @return $this
     */
    public function set_data($data) {
        return $this->setData($data);
    }

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
     * @param string $target
     *
     * @deprecated since version 1.2, please use setTarget
     *
     * @return $this
     */
    public function set_target($target) {
        return $this->setTarget($target);
    }

    /**
     * @param string $url
     *
     * @deprecated since version 1.2, please use setUrl
     *
     * @return $this
     */
    public function set_url($url) {
        return $this->setUrl($url);
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
