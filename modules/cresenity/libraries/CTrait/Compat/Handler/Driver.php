<?php

defined('SYSPATH') OR die('No direct access allowed.');

trait CTrait_Compat_Handler_Driver {

    /**
     * 
     * @param string $url
     * @return $this
     */
    public function set_url($url) {
        return $this->setUrl($url);
    }

    /**
     * @deprecated
     * @return string
     */
    public function generated_url() {
        return $this->generatedUrl();
    }

    /**
     * 
     * @deprecated
     * @param string $owner
     * @return $this
     */
    public function set_owner($owner) {
        return $this->setOwner($owner);
    }

    /**
     * @deprecated
     * @param string $urlParam
     * @return $this
     */
    public function set_url_param($urlParam) {
        return $this->setUrlParam($urlParam);
    }

    /**
     * @deprecated
     * @param string $k
     * @param string $urlParam
     * @return $this
     */
    public function add_url_param($k, $urlParam) {
        return $this->addUrlParam($k, $urlParam);
    }

    /**
     * 
     * @deprecated, please use setTarget
     * @param string $target
     * @return $this
     */
    public function set_target($target) {
        return $this->setTarget($target);
    }

}
