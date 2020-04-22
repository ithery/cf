<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor_Google {

    /**
     * 
     * @param array $options
     * @return \CVendor_Google_Recaptcha_RecaptchaV2
     */
    public static function recaptchaV2($options = []) {
        $secretKey = carr::get($options, 'secretKey', CF::config('vendor.google.recaptcha_v2_api_key'));
        $siteKey = carr::get($options, 'siteKey', CF::config('vendor.google.recaptcha_v2_site_key'));

        return new CVendor_Google_Recaptcha_RecaptchaV2($secretKey, $siteKey);
    }

    /**
     * 
     * @param array $options
     * @return \CVendor_Google_Recaptcha_RecaptchaV3
     */
    public static function recaptchaV3($options = []) {
        $secretKey = carr::get($options, 'secretKey', CF::config('vendor.google.recaptcha_v3_api_key'));
        $siteKey = carr::get($options, 'siteKey', CF::config('vendor.google.recaptcha_v3_site_key'));
        return new CVendor_Google_Recaptcha_RecaptchaV3($secretKey, $siteKey);
    }

}
