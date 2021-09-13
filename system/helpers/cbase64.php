<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Base64 helper class.
 */

//@codingStandardsIgnoreStart
class cbase64 {
    public static function encode($data) {
        return base64_encode($data);
    }

    public static function decode($data) {
        return base64_decode($data);
    }

    public function is_encoded($data) {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
            return true;
        }
        return false;
    }
}
