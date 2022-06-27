<?php

class CManager_Transform_Repository {
    protected $methods;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->methods = [];
    }

    public function addMethods($methods, callable $callback) {
        if (!is_array($methods)) {
            $methods = [$methods];
        }
        foreach ($methods as $m) {
            $this->methods[$m] = $callback;
        }
    }

    public function resolveMethod($method) {
        if (isset($this->methods[$method])) {
            return CManager_Transform_Parser::explodeMethods($this->methods[$method]);
        }

        return null;
    }
}
