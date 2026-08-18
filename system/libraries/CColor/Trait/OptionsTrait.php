<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CColor_Trait_OptionsTrait {
    protected $options = [];

    public function getOption($key) {
        return carr::get($this->options, $key);
    }

    public function setOption($key, $value) {
        $this->options[$key] = $value;

        return $this;
    }

    public function haveOption($key) {
        return $this->getOption($key) !== null;
    }
}
