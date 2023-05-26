<?php

//@codingStandardsIgnoreStart
/**
 * @deprecated 1.6, use c::get
 */
class cobj {
    /**
     * Get property value from object with safe implementation.
     *
     * @param object $object
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     *
     * @deprecated since 1.2 use c::get
     */
    public static function get($object, $key, $default = null) {
        return isset($object->$key) ? $object->$key : $default;
    }
}
//@codingStandardsIgnoreEnd
