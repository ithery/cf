<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * CUTF8::str_pad.
 *
 * @param string $str
 * @param int    $final_str_length
 * @param string $pad_str
 * @param int    $pad_type
 */
// @codingStandardsIgnoreStart
function _str_pad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT) {
    if (CUTF8::is_ascii($str) and CUTF8::is_ascii($pad_str)) {
        return str_pad($str, $final_str_length, $pad_str, $pad_type);
    }

    $str_length = CUTF8::strlen($str);

    if ($final_str_length <= 0 or $final_str_length <= $str_length) {
        return $str;
    }

    $pad_str_length = CUTF8::strlen($pad_str);
    $pad_length = $final_str_length - $str_length;

    if ($pad_type == STR_PAD_RIGHT) {
        $repeat = ceil($pad_length / $pad_str_length);

        return CUTF8::substr($str . str_repeat($pad_str, $repeat), 0, $final_str_length);
    }

    if ($pad_type == STR_PAD_LEFT) {
        $repeat = ceil($pad_length / $pad_str_length);

        return CUTF8::substr(str_repeat($pad_str, $repeat), 0, floor($pad_length)) . $str;
    }

    if ($pad_type == STR_PAD_BOTH) {
        $pad_length /= 2;
        $pad_length_left = floor($pad_length);
        $pad_length_right = ceil($pad_length);
        $repeat_left = ceil($pad_length_left / $pad_str_length);
        $repeat_right = ceil($pad_length_right / $pad_str_length);

        $pad_left = CUTF8::substr(str_repeat($pad_str, $repeat_left), 0, $pad_length_left);
        $pad_right = CUTF8::substr(str_repeat($pad_str, $repeat_right), 0, $pad_length_right);

        return $pad_left . $str . $pad_right;
    }

    throw new CUTF8_Exception('CUTF8::str_pad: Unknown padding type (:pad_type)', [
        ':pad_type' => $pad_type,
    ]);
}
