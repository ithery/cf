<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:07:16 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CCache_DriverAbstract implements CCache_DriverInterface {

    protected $options = array();

    public function __construct($options) {
        $this->options = $options;
    }

    public function getOption($key, $default = null) {
        return carr::get($this->options, $key, $default);
    }

    public function setOption($key, $value) {
        return carr::set($this->options, $key, $value);
    }

    public function hasOption($key) {
        return $this->getOption($key) !== null;
    }

}
