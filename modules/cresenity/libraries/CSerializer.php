<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @deprecated 1.6 dont use this anymore
 */
class CSerializer {
    public static function json() {
        return new CSerializer_JsonSerializer();
    }
}
