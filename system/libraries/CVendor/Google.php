<?php
class CVendor_Google {
    /**
     * @param array $options
     *
     * @return \CVendor_Google_Recaptcha_RecaptchaV2
     */
    public static function recaptchaV2($options = []) {
        $secretKey = carr::get($options, 'secretKey', CF::config('vendor.google.recaptcha_v2_api_key'));
        $siteKey = carr::get($options, 'siteKey', CF::config('vendor.google.recaptcha_v2_site_key'));

        return new CVendor_Google_Recaptcha_RecaptchaV2($secretKey, $siteKey);
    }

    /**
     * @param array $options
     *
     * @return \CVendor_Google_Recaptcha_RecaptchaV3
     */
    public static function recaptchaV3($options = []) {
        $secretKey = carr::get($options, 'secretKey', CF::config('vendor.google.recaptcha_v3_api_key'));
        $siteKey = carr::get($options, 'siteKey', CF::config('vendor.google.recaptcha_v3_site_key'));

        return new CVendor_Google_Recaptcha_RecaptchaV3($secretKey, $siteKey);
    }

    /**
     * @param array $options
     *
     * @return CAnalytics_Google_AnalyticGA4
     */
    public static function ga4Analytic($options) {
        $client = CVendor_Google_Analytic::createForGA4Config($options);

        return new CAnalytics_Google_AnalyticGA4($client, carr::get($options, 'property_id'));
    }
}
