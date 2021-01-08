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
}
//@codingStandardsIgnoreEnd
