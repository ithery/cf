<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CVendor_Watzap_Contract_AdapterInterface {
    /**
     * @param string       $url
     * @param array|string $content
     * @param null|mixed   $headers
     *
     * @throws CVendor_Watzap_Exception_HttpException
     *
     * @return string
     */
    public function post($url, $content = '', $headers = null);
}
