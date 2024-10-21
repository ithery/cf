<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CVendor_Figma_Contract_AdapterInterface {
    /**
     * @param string     $url
     * @param null|mixed $query
     * @param null|mixed $headers
     *
     * @throws CVendor_Figma_Exception_HttpException
     *
     * @return string
     */
    public function get($url, $query = null, $headers = null);

    /**
     * @param string     $url
     * @param mixed      $parameters
     * @param null|mixed $headers
     *
     * @throws CVendor_Figma_Exception_HttpException
     */
    public function delete($url, $parameters, $headers = null);

    /**
     * @param string       $url
     * @param array|string $content
     * @param null|mixed   $headers
     *
     * @throws CVendor_Figma_Exception_HttpException
     *
     * @return string
     */
    public function put($url, $content = '', $headers = null);

    /**
     * @param string       $url
     * @param array|string $content
     * @param null|mixed   $headers
     *
     * @throws CVendor_Figma_Exception_HttpException
     *
     * @return string
     */
    public function post($url, $content = '', $headers = null);
}
