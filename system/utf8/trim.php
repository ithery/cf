<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::trim.
 *
 * @param mixed      $str
 * @param null|mixed $charlist
 */
// @codingStandardsIgnoreStart
function _trim($str, $charlist = null) {
    if ($charlist === null) {
        return trim($str);
    }

    return CUTF8::ltrim(CUTF8::rtrim($str, $charlist), $charlist);
}
