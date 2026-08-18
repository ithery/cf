<?php

defined('SYSPATH') or die('No direct access allowed.');

use CApp_Navigation_Helper as Helper;

class CNavigation_Renderer_ClosureRenderer extends CNavigation_RendererAbstract {
    /**
     * @var callable|null
     */
    protected $closure;

    /**
     * @param callable $closure
     *
     * @return void
     */
    public function setClosure(callable $closure) {
        $this->closure = $closure;
    }

    /**
     * @param null|array $navs
     * @param int        $level
     * @param int        $child
     *
     * @return mixed
     */
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
