<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 12, 2019, 3:48:50 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Project_AbstractGenerator {

    protected $options;

    public function __construct() {
        $this->options = array();
    }

    protected function mergeOptions($options) {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function option($key) {
        return carr::get($this->options, $key);
    }

}
