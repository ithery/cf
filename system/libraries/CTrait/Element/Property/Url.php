<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 09, 2021, 11:58:27 PM
 */
trait CTrait_Element_Property_Url {
    protected $url;

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function haveUrl() {
        return strlen($this->url) > 0;
    }
}
