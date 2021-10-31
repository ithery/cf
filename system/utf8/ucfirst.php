<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::ucfirst.
 *
 * @param mixed $str
 */
// @codingStandardsIgnoreStart
function _ucfirst($str) {
    if (CUTF8::is_ascii($str)) {
        return ucfirst($str);
    }

    preg_match('/^(.?)(.*)$/us', $str, $matches);

    return CUTF8::strtoupper($matches[1]) . $matches[2];
}
