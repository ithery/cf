<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CApp_Navigation_EngineInterface {
    public function render($navs = null, $level = 0, &$child = 0);
}
