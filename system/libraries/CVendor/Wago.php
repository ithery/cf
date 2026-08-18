<?php

use Cresenity\Vendor\Wago\Wago;

class CVendor_Wago {
    /**
     * @param null|string $token
     * @param array       $options
     *
     * @return \Cresenity\Vendor\Wago\Device
     */
    public static function device($token = null, $options = []) {
        if ($token == null) {
            $token = CF::config('vendor.wago.token');
        }

        if (!isset($options['sandbox'])) {
            $options['sandbox'] = CF::config('vendor.wago.sandbox', false);
        }

        if ($options['sandbox']) {
            $options['baseUri'] = 'https://wapro.dev.ittron.co.id/api/device/';
        }

        if (isset($options['logging']) && $options['logging']) {
            $options['logPath'] = DOCROOT . 'temp/logs/vendor/' . CF::appCode() . '/wago/guzzle-log.log';
        }

        return Wago::device($token, $options);
    }
}
