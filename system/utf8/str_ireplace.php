<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::str_ireplace.
 *
 * @param mixed      $search
 * @param mixed      $replace
 * @param mixed      $str
 * @param null|mixed $count
 */
// @codingStandardsIgnoreStart
function _str_ireplace($search, $replace, $str, &$count = null) {
    if (CUTF8::is_ascii($search) and CUTF8::is_ascii($replace) and CUTF8::is_ascii($str)) {
        return str_ireplace($search, $replace, $str, $count);
    }

    if (is_array($str)) {
        foreach ($str as $key => $val) {
            $str[$key] = CUTF8::str_ireplace($search, $replace, $val, $count);
        }

        return $str;
    }

    if (is_array($search)) {
        $keys = array_keys($search);

        foreach ($keys as $k) {
            if (is_array($replace)) {
                if (array_key_exists($k, $replace)) {
                    $str = CUTF8::str_ireplace($search[$k], $replace[$k], $str, $count);
                } else {
                    $str = CUTF8::str_ireplace($search[$k], '', $str, $count);
                }
            } else {
                $str = CUTF8::str_ireplace($search[$k], $replace, $str, $count);
            }
        }

        return $str;
    }

    $search = CUTF8::strtolower($search);
    $str_lower = CUTF8::strtolower($str);

    $total_matched_strlen = 0;
    $i = 0;

    while (preg_match('/(.*?)' . preg_quote($search, '/') . '/s', $str_lower, $matches)) {
        $matched_strlen = strlen($matches[0]);
        $str_lower = substr($str_lower, $matched_strlen);

        $offset = $total_matched_strlen + strlen($matches[1]) + ($i * (strlen($replace) - 1));
        $str = substr_replace($str, $replace, $offset, strlen($search));

        $total_matched_strlen += $matched_strlen;
        $i++;
    }

    $count += $i;

    return $str;
}
