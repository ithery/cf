<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::strpos.
 *
 * @param string $str
 * @param string $search
 * @param int    $offset
 */
// @codingStandardsIgnoreStart
function _strpos($str, $search, $offset = 0) {
    $offset = (int) $offset;

    if (CUTF8::is_ascii($str) and CUTF8::is_ascii($search)) {
        return strpos($str, $search, $offset);
    }

    if ($offset == 0) {
        $array = explode($search, $str, 2);

        return isset($array[1]) ? CUTF8::strlen($array[0]) : false;
    }

    $str = CUTF8::substr($str, $offset);
    $pos = CUTF8::strpos($str, $search);

    return ($pos === false) ? false : ($pos + $offset);
}
