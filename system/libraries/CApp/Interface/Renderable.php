<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CApp_Interface_Renderable {
    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0);

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0);
}
