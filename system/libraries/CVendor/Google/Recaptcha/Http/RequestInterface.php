<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

interface CVendor_Google_Recaptcha_Http_RequestInterface {

    /**
     * Run the request and get response.
     *
     * @param  string  $url
     * @param  bool    $curled
     *
     * @return string
     */
    public function send($url, $curled = true);
}
