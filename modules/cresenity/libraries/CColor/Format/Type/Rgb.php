<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:22:00 AM
 */
class CColor_Format_Type_Rgb extends CColor_Format_TypeAbstract {
    public function toCssStyle($opacity = 1) {
        $r = carr::get($this->value, 'r');
        $g = carr::get($this->value, 'g');
        $b = carr::get($this->value, 'b');

        return "rgba(${r},${g},${b},${opacity})";
    }
}
