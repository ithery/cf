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

    /**
     * @return CManager_Transform_Repository
     */
    public function repository() {
        return CManager_Transform_Repository::instance();
    }

    public function addCallback($method, callable $callback) {
        return $this->repository()->addMethods($method, $callback);
    }

    public function parseMethod($methods) {
        return CManager_Transform_Parser::parse($methods);
    }

    public function methodExists($methods) {
        return $this->isTransformable($methods);
    }

    public function isTransformable($methods) {
        return CManager_Transform_Transformer::isTransformable($methods);
    }

    /**
     * @param mixed $method
     * @param mixed $item
     * @param mixed $args
     *
     * @return mixed
     */
    public function call($method, $item, $args = []) {
        $transformer = new CManager_Transform_Transformer($method);

        return $transformer->transform($item, $args);
    }

    public function __call($name, $arguments) {
        return $this->call($name, carr::get($arguments, 0), carr::get($arguments, 1));
    }
}
