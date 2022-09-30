<?php

class CVendor_Wago {
    public static function device($token = null, $options = []) {
        if ($token == null) {
            $token = CF::config('vendor.wago.token');
        }

        if (!isset($options['sandbox'])) {
            $options['sandbox'] = CF::config('vendor.wago.sandbox', false);
        }

        return new CVendor_Wago_Device($token, $options);
    }
}
