<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:49:26 PM
 */
abstract class CCache_Driver_FileDriver_EngineAbstract implements CCache_Driver_FileDriver_EngineInterface {
    protected $options;

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
