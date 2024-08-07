<?php

/**
 * @see CFunction
 * @deprecated 1.3, use CFunction
 */
class CDynFunction {
    use CTrait_Compat_DynFunction;

    public $func = '';

    public $params = [];

    public $requires = [];

    public $type = 'defined'; //defined,class,

    private function __construct($func) {
        CDebug::collector()->collectDeprecated('dont use CDynFunction, use CFunction');
        $this->func = $func;
    }

    public static function factory($func) {
        return new CDynFunction($func);
    }

    public function setFunction($cfunc) {
        $this->func = $cfunc;

        return $this;
    }

    public function getFunction() {
        return $this->func;
    }

    public function addParam($p) {
        $this->params[] = $p;

        return $this;
    }

    public function addRequire($p) {
        $this->requires[] = $p;

        return $this;
    }

    public function setRequire($p) {
        $this->requires = $p;

        return $this;
    }

    public function execute($args = []) {
        if (!is_array($args)) {
            $args = [$args];
        }
        foreach ($this->requires as $r) {
            require_once $r;
        }
        $params = array_merge($args, $this->params);

        $error = 0;

        if ($error == 0) {
            if (is_array($this->func)) {
                if (is_callable($this->func)) {
                    return call_user_func_array($this->func, $params);
                } else {
                    $error++;
                }
            }
        }
        if ($error == 0) {
            if ($this->func instanceof Closure) {
                return call_user_func_array($this->func, $params);
            }
        }
        if ($error == 0) {
            if (is_callable($this->func)) {
                return call_user_func_array($this->func, $params);
            }
        }
        if ($error == 0) {
            $transformManager = CManager_Transform::instance();
            if ($transformManager->isTransformable($this->func)) {
                $item = carr::first($params);
                $parameters = array_slice($params, 1);

                return $transformManager->call($this->func, $item, $parameters);
            }
        }
        if ($error == 0) {
            //not the function name, let check it if it is function from ctransform class
            if (is_callable(['ctransform', $this->func])) {
                return call_user_func_array(['ctransform', $this->func], $params);
            }
        }
        if ($error == 0) {
            //it is not method from ctransform class, try the other class if it is found ::
            if (is_string($this->func) && strpos($this->func, '::') !== false) {
                return call_user_func_array(explode('::', $this->func), $params);
            }
        }
        if ($error == 0) {
            //not array let check if it is a function name
            if (@function_exists($this->func)) {
                return call_user_func_array($this->func, $params);
            }
        }

        if ($error > 0) {
            $functionName = $this->func;
            if (is_array($functionName)) {
                $functionName = implode('::', $functionName);
            }
            if ($functionName instanceof Closure) {
                $functionName = 'Closure';
            }
            if (!is_string($functionName)) {
                $functionName = 'Unknown';
            }

            throw new Exception(c::__('function :function is not callable', ['function' => $functionName]));
        }
        //last return this name of function
        return $this->func;
    }
}
