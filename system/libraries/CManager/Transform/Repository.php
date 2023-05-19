<?php

class CManager_Transform_Repository {
    protected $methods;

    private static $instance;

    /**
     * @return CManager_Transform_Repository
     */
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
        if ($method instanceof CManager_Transform_Contract_TransformMethodInterface) {
            return null;
        }
        if ($method instanceof Closure || $method instanceof \Opis\Closure\SerializableClosure) {
            return null;
        }

        if (isset($this->methods[$method])) {
            return CManager_Transform_Parser::explodeMethods($this->methods[$method]);
        }

        return null;
    }
}
