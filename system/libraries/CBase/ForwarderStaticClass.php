<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 */
class CBase_ForwarderStaticClass {
    protected $class;

    public function __construct($class) {
        $this->class = $class;
    }

    public function __call($method, $arguments) {
        return call_user_func_array([$this->class, $method], $arguments);
    }
}
