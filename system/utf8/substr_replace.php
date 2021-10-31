<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::substr_replace.
 *
 * @param mixed      $str
 * @param mixed      $replacement
 * @param mixed      $offset
 * @param null|mixed $length
 */
// @codingStandardsIgnoreStart
function _substr_replace($str, $replacement, $offset, $length = null) {
    if (CUTF8::is_ascii($str)) {
        return ($length === null) ? substr_replace($str, $replacement, $offset) : substr_replace($str, $replacement, $offset, $length);
    }

    $length = ($length === null) ? CUTF8::strlen($str) : (int) $length;
    preg_match_all('/./us', $str, $str_array);
    preg_match_all('/./us', $replacement, $replacement_array);

    array_splice($str_array[0], $offset, $length, $replacement_array[0]);

    return implode('', $str_array[0]);
}
