<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Context {

    protected $data = [];

    public function __construct($initialData = []) {
        $this->data = $initialData;
    }

    public function setBackgroundColor($color) {
        $globalData = CEmail::builder()->globalData();
        $globalData->set('backgroundColor', $color);

        return $this;
    }

    public function getBackgroundColor($color) {
        $globalData = CEmail::builder()->globalData();
        return $globalData->get('backgroundColor');
    }

    public function getContainerWidth() {
        return $this->get('containerWidth');
    }

    public function get($key, $defaultValue = null) {
        return carr::get($this->data, $key, $defaultValue);
    }

    public function set($key, $value) {
        return carr::set($this->data, $key, $value);
    }

    public function data() {
        return $this->data;
    }

    public function addMediaQuery($className, $options) {
        $parsedWidth = carr::get($options, 'parsedWidth');
        $unit = carr::get($options, 'unit');
        $globalData = CEmail::builder()->globalData();

        $globalData->set('mediaQueries.' . $className, '{ width:' . $parsedWidth . $unit . ' !important; max-width:' . $parsedWidth . $unit . '; }');
    }

}
