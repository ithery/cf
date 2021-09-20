<?php

//@codingStandardsIgnoreStart
/**
 * @deprecated since 1.2 change to c::app()->role()
 */
class crole {
    //@codingStandardsIgnoreEnd

    /**
     * Roles cache
     *
     * @var array
     */
    protected static $roles = [];

    public static function get($id) {
        return c::app()->getRole($id);
    }
}
