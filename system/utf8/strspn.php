<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::strspn.
 *
 * @param mixed      $str
 * @param mixed      $mask
 * @param null|mixed $offset
 * @param null|mixed $length
 */
// @codingStandardsIgnoreStart
function _strspn($str, $mask, $offset = null, $length = null) {
    if ($str == '' or $mask == '') {
        return 0;
    }

    if (CUTF8::is_ascii($str) and CUTF8::is_ascii($mask)) {
        return ($offset === null) ? strspn($str, $mask) : (($length === null) ? strspn($str, $mask, $offset) : strspn($str, $mask, $offset, $length));
    }

    if ($offset !== null or $length !== null) {
        $str = CUTF8::substr($str, $offset, $length);
    }

    // Escape these characters:  - [ ] . : \ ^ /
    // The . and : are escaped to prevent possible warnings about POSIX regex elements
    $mask = preg_replace('#[-[\].:\\\\^/]#', '\\\\$0', $mask);
    preg_match('/^[^' . $mask . ']+/u', $str, $matches);

    return isset($matches[0]) ? CUTF8::strlen($matches[0]) : 0;
}
