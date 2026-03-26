<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CApp_Interface_Renderable {
    public function html($indent = 0);

    public function js($indent = 0);
}
