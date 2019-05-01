<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:38:02 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage {

    /**
     * Create CImage_Avatar Object
     * 
     * @param string $engineName
     * @return \CImage_Avatar
     */
    public static function avatar($engineName = 'Initials') {

        return new CImage_Avatar($engineName);
    }

    
}
