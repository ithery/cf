<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::strrev.
 *
 * @param mixed $str
 */
// @codingStandardsIgnoreStart
function _strrev($str) {
    if (CUTF8::is_ascii($str)) {
        return strrev($str);
    }

    preg_match_all('/./us', $str, $matches);

    return implode('', array_reverse($matches[0]));
}
