<?php

class CVendor_Watzap {
    /**
     * @param null|string $numberKey
     * @param null|string $apiKey
     * @param array       $options
     *
     * @return CVendor_Watzap_Device
     */
    public static function device($numberKey = null, $apiKey = null, $options = []) {
        if ($apiKey == null) {
            $apiKey = CF::config('vendor.watzap.api_key');
        }
        if ($numberKey == null) {
            $numberKey = CF::config('vendor.watzap.number_key');
        }

        return new CVendor_Watzap_Device($numberKey, $apiKey, $options);
    }
}
