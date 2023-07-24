<?php

defined('SYSPATH') or die('No direct access allowed.');

class CString {
    public static function initials($name = null) {
        return new CString_Initials($name);
    }

    public static function language() {
        return new CString_Language();
    }

    public static function createPatternBuilder() {
        return new CString_PatternBuilder();
    }

    /**
     * @param string      $number
     * @param null|string $locale
     *
     * @throws Symfony\Component\Routing\Exception\InvalidParameterException
     *
     * @return string
     */
    public static function numberToWords($number, $locale = null) {
        return CString_NumberToWords::toWords($number, $locale);
    }

    /**
     * @return CString_Formatter
     */
    public static function formatter() {
        return new CBase_ForwarderStaticClass(CString_Formatter::class);
    }
}
