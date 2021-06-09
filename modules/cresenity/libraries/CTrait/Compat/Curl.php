<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 29, 2018, 10:57:46 PM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Curl {
    /**
     * @deprecated since version 1.2, please use function addBreadcrumb
     *
     * @return CCurl
     */
    public function set_ssl() {
        return $this->setSSL();
    }

    /**
     * @deprecated since version 1.2, please use function setPost
     *
     * @param array $data
     *
     * @return $this
     */
    public function set_post($data) {
        return $this->setPost($data);
    }

    public function set_timeout($milisecond) {
        return $this->setTimeout($milisecond);
    }

    public function set_opt($key, $value, $overwrite = true) {
        return $this->setOpt($key, $value, $overwrite);
    }

    /**
     * Get value from key
     *
     * @param string $key
     *
     * @return mixed
     *
     * @deprecated use getOpt
     */
    public function get_opt($key) {
        return $this->getOpt($key);
    }

    public function get_handle() {
        return $this->getHandle();
    }

    public function set_http_user_agent($http_user_agent) {
        return $this->setHttpUserAgent($http_user_agent);
    }

    public function set_url($url) {
        return $this->setUrl($url);
    }

    public function set_raw_post($string) {
        return $this->setRawPost($string);
    }

    public function set_referrer($referrer) {
        return $this->setReferrer($referrer);
    }

    public function set_useragent($useragent) {
        return $this->setUserAgent($useragent);
    }

    public function set_http_header($http_header) {
        return $this->setHttpHeader($http_header);
    }

    public function get_post_data() {
        return $this->getPostData();
    }

    /**
     * Parse Header
     *
     * @param string $header_str
     *
     * @return void
     *
     * @deprecated use parseHeader
     */
    public function parse_header($header_str = null) {
        return $this->parseHeader($header_str);
    }

    public function get_info($opt = 0) {
        return $this->getInfo($opt);
    }

    public function set_engine($engine) {
        return $this->setEngine($engine);
    }

    public function get_status($key = null) {
        return $this->getStatus($key);
    }

    public function get_http_code() {
        return $this->getHttpCode();
    }

    public function get_header($caseless = null) {
        return $this->getHeader($caseless);
    }

    public function get_followed_headers() {
        return $this->getFollowedHeaders();
    }

    public function has_error() {
        return $this->hasError();
    }

    public function set_cookies_file($filename) {
        return $this->setCookiesFile($filename);
    }

    public function set_soap_action($action) {
        return $this->setSoapAction($action);
    }
}
//@codingStandardsIgnoreEnd
