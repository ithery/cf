<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 10, 2018, 2:18:13 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Resources_Engine {

    public function get_url($filename = null, $size = '', $encode = true) {
        return $this->getUrl($filename, $size, $encode);
    }

    public function get_path($filename, $size = null) {
        return $this->getPath($filename,$size);
    }
}
