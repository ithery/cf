<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor_Google_Recaptcha_Http_Request implements CVendor_Google_Recaptcha_Http_RequestInterface {
    /* -----------------------------------------------------------------
      |  Properties
      | -----------------------------------------------------------------
     */

    /**
     * URL to request.
     *
     * @var string
     */
    protected $url;

    /* -----------------------------------------------------------------
      |  Getters & Setters
      | ------------------------------------------------    -----------------
     */

    /**
     * Set URL.
     *
     * @param  string  $url
     *
     * @return self
     */
    protected function setUrl($url) {
        $this->checkUrl($url);

        $this->url = $url;

        return $this;
    }

    /* -----------------------------------------------------------------
      |  Main Methods
      | -----------------------------------------------------------------
     */

    /**
     * Create an api request using curl.
     *
     * @return string
     */
    protected function curl() {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    /**
     * Run the request and get response.
     *
     * @param  string  $url
     * @param  bool    $curled
     *
     * @return string
     */
    public function send($url, $curled = true) {
        $this->setUrl($url);

        $result = ($this->isCurlExists() && $curled === true) ? $this->curl() : file_get_contents($this->url);

        return $this->checkResult($result) ? $result : '{}';
    }

    /* -----------------------------------------------------------------
      |  Check Methods
      | -----------------------------------------------------------------
     */

    /**
     * Check URL.
     *
     * @param  string  $url
     *
     * @throws CVendor_Google_Recaptcha_Exception_InvalidUrlException
     */
    private function checkUrl(&$url) {
        if (!is_string($url))
            throw new CVendor_Google_Recaptcha_Exception_InvalidUrlException(
            'The url must be a string value, ' . gettype($url) . ' given'
            );

        $url = trim($url);

        if (empty($url))
            throw new CVendor_Google_Recaptcha_Exception_InvalidUrlException('The url must not be empty');

        if (filter_var($url, FILTER_VALIDATE_URL) === false)
            throw new CVendor_Google_Recaptcha_Exception_InvalidUrlException('The url [' . $url . '] is invalid');
    }

    /**
     * Check if curl exists.
     *
     * @return bool
     */
    private function isCurlExists() {
        return function_exists('curl_version');
    }

    /**
     * Check Result.
     *
     * @param  string  $result
     *
     * @return bool
     */
    private function checkResult($result) {
        return is_string($result) && !empty($result);
    }

}
