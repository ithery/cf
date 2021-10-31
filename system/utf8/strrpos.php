<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::strrpos.
 *
 * @param mixed $str
 * @param mixed $search
 * @param mixed $offset
 */
// @codingStandardsIgnoreStart
function _strrpos($str, $search, $offset = 0) {
    $offset = (int) $offset;

    if (CUTF8::is_ascii($str) and CUTF8::is_ascii($search)) {
        return strrpos($str, $search, $offset);
    }

    if ($offset == 0) {
        $array = explode($search, $str, -1);

        return isset($array[0]) ? CUTF8::strlen(implode($search, $array)) : false;
    }

    $str = CUTF8::substr($str, $offset);
    $pos = CUTF8::strrpos($str, $search);

    return ($pos === false) ? false : ($pos + $offset);
}
