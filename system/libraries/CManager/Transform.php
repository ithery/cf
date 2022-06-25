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

    public function getCallable($methods) {
        list($method, $params) = $this->parseMethod($methods);
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

    public function parseMethod($methods) {
        return CManager_Transform_Parser::parse($methods);
    }

    public function methodExists($methods) {
        return $this->isTransformable($methods);
    }

    public function isTransformable($methods) {
        return CManager_Transform_Transformer::isTransformable($methods);
    }

    public function call($method, $item, $args = []) {
        $transformer = new CManager_Transform_Transformer($method);

        return $transformer->transform($item, $args);
        // $callable = $this->getCallable($method);
        // $parameters = array_merge([$item], array_values($args));
        // if ($callable == null) {
        //     throw new Exception(c::__("method :method doesn't exists", [':method' => $method]));
        // }

        // return call_user_func_array($callable, $parameters);
    }
}
