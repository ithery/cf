<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CTrait_HasOptions {

    protected $options;

    public function getOptions() {
        return $this->options;
    }

    public function setOptions(array $options) {
        $this->options = $options;
        return $this;
    }

    public function setOption($key, $option) {
        carr::set($this->options, $key, $option);
        return $this;
    }

    public function getOption($key, $defaultValue = null) {
        return carr::get($this->options, $key, $defaultValue);
    }

}
