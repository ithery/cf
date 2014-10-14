<?php defined('SYSPATH') OR die('No direct access allowed.'); 

interface IRenderable {
    public function html($indent=0);
    public function js($indent=0);
}