<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::stristr.
 *
 * @param string $str
 * @param string $search
 */
// @codingStandardsIgnoreStart
function _stristr($str, $search) {
    if (CUTF8::is_ascii($str) and CUTF8::is_ascii($search)) {
        return stristr($str, $search);
    }

    if ($search == '') {
        return $str;
    }

    $str_lower = CUTF8::strtolower($str);
    $search_lower = CUTF8::strtolower($search);

    preg_match('/^(.*?)' . preg_quote($search_lower, '/') . '/s', $str_lower, $matches);

    if (isset($matches[1])) {
        return substr($str, strlen($matches[1]));
    }

    return false;
}
