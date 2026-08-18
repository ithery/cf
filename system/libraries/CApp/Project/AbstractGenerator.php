<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Project_AbstractGenerator {
    /**
     * @var array
     */
    protected $options;

    /**
     * CApp_Project_AbstractGenerator constructor.
     */
    public function __construct() {
        $this->options = [];
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    protected function mergeOptions($options) {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function option($key) {
        return carr::get($this->options, $key);
    }
}
