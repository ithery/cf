<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 2:07:19 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage_Avatar {

    private $engineName;

    public function __construct($engineName = 'Initials') {
        $this->engineName = $engineName;
    }

    /**
     * 
     * @return CImage_Avatar_ApiAbstract
     */
    public static function api() {
        $className = 'CImage_Avatar_Api_' . $this->engineName;
        return new $className();
    }

}
