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

    public function getCallable($method) {
        $callable = carr::get($this->callbacks, $method);
        if ($callable == null) {
            //locate from CManager_Transform_DefaultMethod
            if (method_exists(CManager_Transform_DefaultMethod::class, $method)) {
                $callable = [CManager_Transform_DefaultMethod::class, $method];
            }
        }
        if ($callable == null) {
            if (method_exists(ctransform::class, $method)) {
                $callable = [ctransform::class, $method];
            }
        }

        return $callable;
    }

    public function methodExists($method) {
        return $this->getCallable($method) != null;
    }

    public function call($method, $args) {
        $callable = $this->getCallable($method);

        if ($callable == null) {
            throw new Exception(c::__("method :method doesn't exists", [':method' => $method]));
        }

        return call_user_func_array($callable, $args);
    }
}
