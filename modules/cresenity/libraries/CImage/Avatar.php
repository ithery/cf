<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 2:07:19 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage_Avatar {

    public static function api($engineName = 'Initials') {
        $className = 'CImage_Avatar_Api_' . $engineName;
        return new $className();
    }

}
