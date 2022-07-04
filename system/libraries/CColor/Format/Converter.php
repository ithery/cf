<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:27:10 AM
 */
class CColor_Format_Converter {
    public static function hsvToHex($hsv) {
        if (!($hsv instanceof CColor_Format_Type_Hsv)) {
            $hsv = new CColor_Format_Type_Hsv($hsv);
        }
        return $hsv->toHex();
    }
}
