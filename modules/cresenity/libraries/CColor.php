<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:03:42 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CColor {
    /**
     * 
     * Create CColor_Random object
     * 
     * @return CColor_Random
     */
    public static function random() {
       return new CColor_Random(); 
    }
}