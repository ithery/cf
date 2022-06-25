<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 22, 2018, 7:32:39 PM
 */
class CHelper_Base64 {
    public static function encode($data) {
        return base64_encode($data);
    }

    public static function decode($data) {
        return base64_decode($data);
    }

    public function isEncoded($data) {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
            return true;
        }
        return false;
    }
}
