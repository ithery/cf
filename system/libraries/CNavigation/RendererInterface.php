<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CNavigation_RendererInterface {
    public function render($navs = null, $level = 0, &$child = 0);
}
