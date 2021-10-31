<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::ucwords.
 *
 * @param mixed $str
 */
// @codingStandardsIgnoreStart
function _ucwords($str) {
    if (CUTF8::is_ascii($str)) {
        return ucwords($str);
    }

    // [\x0c\x09\x0b\x0a\x0d\x20] matches form feeds, horizontal tabs, vertical tabs, linefeeds and carriage returns.
    // This corresponds to the definition of a 'word' defined at http://php.net/ucwords
    return preg_replace_callback(
        '/(?<=^|[\x0c\x09\x0b\x0a\x0d\x20])[^\x0c\x09\x0b\x0a\x0d\x20]/u',
        function ($matches) {
            return CUTF8::strtoupper($matches[0]);
        },
        $str
    );
}
