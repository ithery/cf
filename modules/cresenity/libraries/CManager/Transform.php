<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CManager_Transform {

    protected $callbacks = array();
    protected static $instance = null;

    /**
     * 
     * @return CManager_Transform
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CManager_Transform();
        }
        return self::$instance;
    }

    public function addCallback($method, callable $callback) {
        if (!is_array($method)) {
            $method = array($method);
        }
        foreach ($method as $m) {
            $this->callbacks[$m] = $callback;
        }
    }

    public function methodExists($method) {
        return isset($this->callbacks[$method]);
    }

    public function call($method, $value) {
        if (!$this->methodExists($method)) {
            throw new CException("method :method doesn't exists", array(':method' => $method));
        }
        $callable = $this->callbacks[$method];
        return call_user_func_array($callable, $value);
    }

}
