<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

interface CVendor_Google_Recaptcha_RecaptchaInterface {
    /* -----------------------------------------------------------------
      |  Getters & Setters
      | -----------------------------------------------------------------
     */

    /**
     * Set HTTP Request Client.
     *
     * @param  CVendor_Google_Recaptcha_Http_Request  $request
     *
     * @return self
     */
    public function setRequestClient(CVendor_Google_Recaptcha_Http_Request $request);

    /**
     * Set language code.
     *
     * @param  string  $lang
     *
     * @return self
     */
    public function setLang($lang);

    /* -----------------------------------------------------------------
      |  Main Methods
      | -----------------------------------------------------------------
     */

    /**
     * Verify Response.
     *
     * @param  string  $response
     * @param  string  $clientIp
     *
     * @return CVendor_Google_Recaptcha_Http_ResponseV3
     */
    public function verify($response, $clientIp = null);

    /**
     * Get script tag.
     *
     * @return string
     */
    public function script();

    /**
     * Get the NoCaptcha API Script.
     *
     * @return string
     */
    public function getApiScript();
}
