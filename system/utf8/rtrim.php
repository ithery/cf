<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::rtrim.
 *
 * @param mixed      $str
 * @param null|mixed $charlist
 */
// @codingStandardsIgnoreStart
function _rtrim($str, $charlist = null) {
    if ($charlist === null) {
        return rtrim($str);
    }

    if (CUTF8::is_ascii($charlist)) {
        return rtrim($str, $charlist);
    }

    $charlist = preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);

    return preg_replace('/[' . $charlist . ']++$/uD', '', $str);
}
