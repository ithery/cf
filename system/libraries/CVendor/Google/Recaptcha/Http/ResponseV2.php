<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor_Google_Recaptcha_Http_ResponseV2 extends CVendor_Google_Recaptcha_Http_AbstractResponse {
    /* -----------------------------------------------------------------
      |  Main Methods
      | -----------------------------------------------------------------
     */

    /**
     * Build the response from an array.
     *
     * @param  array $array
     *
     * @return \Arcanedev\NoCaptcha\Utilities\ResponseV2|mixed
     */
    public static function fromArray(array $array) {
        $hostname = carr::get($array, 'hostname');
        $challengeTs = carr::get($array, 'challenge_ts');
        $apkPackageName = carr::get($array, 'apk_package_name');

        if (isset($array['success']) && $array['success'] == true)
            return new static(true, [], $hostname, $challengeTs, $apkPackageName);

        if (!(isset($array['error-codes']) && is_array($array['error-codes'])))
            $array['error-codes'] = [CVendor_Google_Recaptcha_Http_ResponseV3::E_UNKNOWN_ERROR];

        return new static(false, $array['error-codes'], $hostname, $challengeTs, $apkPackageName);
    }

    /**
     * Convert the response object to array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'success' => $this->isSuccess(),
            'hostname' => $this->getHostname(),
            'challenge_ts' => $this->getChallengeTs(),
            'apk_package_name' => $this->getApkPackageName(),
            'error-codes' => $this->getErrorCodes(),
        ];
    }

}
