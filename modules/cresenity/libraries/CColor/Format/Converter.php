<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:27:10 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CColor_Format_Converter {

    public static function HsvToHex($hsv) {
        if (!($hsv instanceof CColor_Format_Type_Hsv)) {
            $hsv = new CColor_Format_Type_Hsv($hsv);
        }
        return $hsv->toHex();
    }

}
