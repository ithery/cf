<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 2:07:19 AM
 */
class CImage_Avatar {
    private $engineName;

    public function __construct($engineName = 'Initials') {
        $this->engineName = $engineName;
    }

    /**
     * @return CImage_Avatar_Api_Initials
     */
    public function api() {
        $className = 'CImage_Avatar_Api_' . $this->engineName;

        return new $className();
    }
}
