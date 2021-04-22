<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:03:42 AM
 */
class CColor {
    /**
     * Create CColor_Random object
     *
     * @param array $options
     *
     * @return CColor_Random
     */
    public static function random($options = []) {
        return new CColor_Random($options);
    }

    /**
     * Create CColor_String object
     *
     * @param string $string
     * @param array  $options
     *
     * @return CColor_String
     */
    public static function fromString($string, $options = []) {
        return new CColor_String($string, $options);
    }
}
