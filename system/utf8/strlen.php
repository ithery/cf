<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::strlen.
 *
 * @param string $str
 */
// @codingStandardsIgnoreStart
function _strlen($str) {
    if (CUTF8::is_ascii($str)) {
        return strlen($str);
    }

    return strlen(utf8_decode($str));
}
