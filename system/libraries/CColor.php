<?php

defined('SYSPATH') or die('No direct access allowed.');

class CColor {
    /**
     * Create CColor_Random object.
     *
     * @param array $options
     *
     * @return CColor_Random
     */
    public static function random($options = []) {
        return new CColor_Random($options);
    }

    /**
     * Create CColor_String object.
     *
     * @param string $string
     * @param array  $options
     *
     * @return CColor_String
     */
    public static function fromString($string, $options = []) {
        return new CColor_String($string, $options);
    }

    /**
     * @param string $bgHex
     * @param string $fgHex
     *
     * @return CColor_Css
     */
    public static function css($bgHex = '', $fgHex = '') {
        return CColor_Css::make($bgHex, $fgHex);
    }

    /**
     * @param string $color
     *
     * @throws CColor_Exception_AmbiguousColorStringException|CColor_Exception_InvalidColorException
     *
     * @return CColor_FormatAbstract
     */
    public static function create($color) {
        $color = str_replace(' ', '', $color);
        // Definitive types
        if (preg_match('/^(?P<type>(rgba?|hsla?|hsv))/i', $color, $match)) {
            $class = self::resolveFormatClass($match['type']);

            return new $class($color);
        }
        // Best guess
        if (preg_match('/^#?[a-f0-9]{8}$/i', $color)) {
            return new CColor_Format_Hexa($color);
        }
        if (preg_match('/^#?[a-f0-9]{3}([a-f0-9]{3})?$/i', $color)) {
            return new CColor_Format_Hex($color);
        }
        if (preg_match('/^[a-z]+$/i', $color)) {
            return new CColor_Format_Hex($color);
        }
        if (preg_match('/^\d{1,3},\d{1,3},\d{1,3}$/', $color)) {
            return new CColor_Format_Rgb($color);
        }
        if (preg_match('/^\d{1,3},\d{1,3},\d{1,3},[0-9\.]+$/', $color)) {
            return new CColor_Format_Rgba($color);
        }
        if (preg_match('/^\d{1,3},\d{1,3}%,\d{1,3}%,[0-9\.]+$/', $color)) {
            return new CColor_Format_Hsla($color);
        }
        // Cannot determine between hsv and hsl
        throw new CColor_Exception_AmbiguousColorStringException("Cannot determine color type of '{$color}'");
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private static function resolveFormatClass($class) {
        return 'CColor_Format_' . ucfirst(strtolower($class));
    }
}
