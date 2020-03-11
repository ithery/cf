<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_GlobalData {

    protected $data = [];
    protected static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function reset() {
        
        $this->data = [];
    }

    private function __construct() {
        
       $this->reset();
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

    public function exists($key) {
        return isset($this->data[$key]);
    }

    public function push($key, $value) {
        if (isset($this->data[$key]) && is_array($this->data[$key])) {
           
            $this->data[$key] = array_merge($this->data[$key],$value);
        }
    }

}
