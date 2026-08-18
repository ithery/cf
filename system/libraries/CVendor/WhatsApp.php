<?php

class CVendor_WhatsApp {
    /**
     * @param null|string $token
     * @param null|mixed  $businessAccountId
     * @param null|mixed  $phoneNumberId
     * @param array       $options
     *
     * @return CVendor_WhatsApp_Api
     */
    public static function api($token = null, $businessAccountId = null, $phoneNumberId = null, $options = []) {
        if ($token == null) {
            $token = CF::config('vendor.whatsapp.token');
        }
        if ($businessAccountId == null) {
            $businessAccountId = CF::config('vendor.whatsapp.business_account_id');
        }
        if ($phoneNumberId == null) {
            $phoneNumberId = CF::config('vendor.whatsapp.phone_number_id');
        }

        return new CVendor_WhatsApp_Api($token, $businessAccountId, $phoneNumberId, $options);
    }
}
