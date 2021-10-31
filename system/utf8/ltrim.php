<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::ltrim.
 *
 * @param mixed      $str
 * @param null|mixed $charlist
 */
// @codingStandardsIgnoreStart
function _ltrim($str, $charlist = null) {
    if ($charlist === null) {
        return ltrim($str);
    }

    if (CUTF8::is_ascii($charlist)) {
        return ltrim($str, $charlist);
    }

    $charlist = preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);

    return preg_replace('/^[' . $charlist . ']+/u', '', $str);
}
