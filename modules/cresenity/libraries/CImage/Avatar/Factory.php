<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 2:03:00 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CImage_Avatar_Factory {
    public static function create($engineName) {
        $className = 'CImage_Avatar_Engine_'.$engineName;
        return new $className();
    }
}