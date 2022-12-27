<?php

class CVendor_TugasHarian {
    public static function company($token = null, $options = []) {
        if ($token == null) {
            $token = CF::config('vendor.tugasharian.token');
        }

        if (!isset($options['sandbox'])) {
            $options['sandbox'] = CF::config('vendor.tugasharian.sandbox', false);
        }

        return new CVendor_TugasHarian_Company($token, $options);
    }
}
