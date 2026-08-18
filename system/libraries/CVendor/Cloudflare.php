<?php

class CVendor_Cloudflare {
    /**
     * @param string $email
     * @param string $apiKey
     *
     * @return CVendor_Cloudflare_Api
     */
    public static function api($email, $apiKey) {
        return new CVendor_Cloudflare_Api($email, $apiKey);
    }

    /**
     * @param array $options
     *
     * @return CVendor_Cloudflare_Turnstile
     */
    public static function turnstile($options = []) {
        $secretKey = carr::get($options, 'secretKey', CF::config('vendor.cloudflare.turnstile_secret_key'));
        $siteKey = carr::get($options, 'siteKey', CF::config('vendor.cloudflare.turnstile_site_key'));

        return new CVendor_Cloudflare_Turnstile($secretKey, $siteKey);
    }
}
