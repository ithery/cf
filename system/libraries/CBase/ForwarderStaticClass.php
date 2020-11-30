<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
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
