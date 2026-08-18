<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CNavigation_RendererInterface {
    /**
     * @param null|array $navs
     * @param int        $level
     * @param int        $child
     *
     * @return bool|string
     */
    public function render($navs = null, $level = 0, &$child = 0);
}
