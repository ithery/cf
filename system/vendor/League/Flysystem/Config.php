<?php

namespace League\Flysystem;

use function array_merge;

class Config {
    const OPTION_VISIBILITY = 'visibility';

    const OPTION_DIRECTORY_VISIBILITY = 'directory_visibility';

    /**
     * @var array
     */
    private $options;

    public function __construct(array $options = []) {
        $this->options = $options;
    }

    /**
     * @param string $property
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($property, $default = null) {
        return isset($this->options[$property]) ? $this->options[$property] : $default;
    }

    /**
     * @param array $options
     *
     * @return Config
     */
    public function extend(array $options) {
        return new Config(array_merge($this->options, $options));
    }

    /**
     * @param array $defaults
     *
     * @return Config
     */
    public function withDefaults(array $defaults) {
        return new Config($this->options + $defaults);
    }
}
