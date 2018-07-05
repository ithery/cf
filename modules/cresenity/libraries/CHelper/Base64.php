<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 22, 2018, 7:32:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CHelper_Base64 {

    public static function encode($data) {
        return base64_encode($data);
    }

    public static function decode($data) {
        return base64_decode($data);
    }

    function isEncoded() {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
            return TRUE;
        }
        return FALSE;
    }

}
