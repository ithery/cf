<?php

defined('SYSPATH') or die('No direct access allowed.');

use CApp_Navigation_Helper as Helper;

class CApp_Navigation_Engine_Closure extends CApp_Navigation_Engine {
    protected $closure;

    public function setClosure(callable $closure) {
        $this->closure = $closure;
    }

    public function render($navs = null, $level = 0, &$child = 0) {
        if ($navs == null) {
            $navs = $this->navs;
        }
        if ($this->closure != null) {
            return call_user_func($this->closure, $navs);
        }

        return '';
    }
}
