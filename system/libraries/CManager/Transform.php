<?php

class CManager_Transform {
    protected $callbacks = [];

    protected static $instance = null;

    /**
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
            $method = [$method];
        }
        foreach ($method as $m) {
            $this->callbacks[$m] = $callback;
        }
    }

    public function methodExists($method) {
        return !empty($this->callbacks) && isset($this->callbacks[$method]);
    }

    public function call($method, $value) {
        if (!$this->methodExists($method)) {
            throw new Exception(c::__("method :method doesn't exists", [':method' => $method]));
        }
        $callable = $this->callbacks[$method];

        return call_user_func_array($callable, $value);
    }
}
