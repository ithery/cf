<?php

interface CVendor_Google_Recaptcha_Http_RequestInterface {
    /**
     * Run the request and get response.
     *
     * @param string $url
     * @param bool   $curled
     *
     * @return string
     */
    public function send($url, $curled = true);
}
