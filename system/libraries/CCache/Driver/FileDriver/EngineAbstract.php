<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:49:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CCache_Driver_FileDriver_EngineAbstract implements CCache_Driver_FileDriver_EngineInterface {

    protected $key;
    protected $options;

    public function __construct($key, $options) {
        $this->key = $key;
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
