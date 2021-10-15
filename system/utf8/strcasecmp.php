<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::strcasecmp.
 *
 * @param string $str1
 * @param string $str2
 */
// @codingStandardsIgnoreStart
function _strcasecmp($str1, $str2) {
    if (CUTF8::is_ascii($str1) and CUTF8::is_ascii($str2)) {
        return strcasecmp($str1, $str2);
    }

    $str1 = CUTF8::strtolower($str1);
    $str2 = CUTF8::strtolower($str2);

    return strcmp($str1, $str2);
}
