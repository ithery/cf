<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::str_split.
 *
 * @param string $str
 * @param int    $split_length
 */
// @codingStandardsIgnoreStart

function _str_split($str, $split_length = 1) {
    $split_length = (int) $split_length;

    if (CUTF8::is_ascii($str)) {
        return str_split($str, $split_length);
    }

    if ($split_length < 1) {
        return false;
    }

    if (CUTF8::strlen($str) <= $split_length) {
        return [$str];
    }

    preg_match_all('/.{' . $split_length . '}|[^\x00]{1,' . $split_length . '}$/us', $str, $matches);

    return $matches[0];
}
