<?php

defined('SYSPATH') or die('No direct access allowed.');

use CApp_Navigation_Helper as Helper;

class CNavigation_Renderer_ClosureRenderer extends CNavigation_RendererAbstract {
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
