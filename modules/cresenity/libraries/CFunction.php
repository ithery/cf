<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Cresenity
 *
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @version Release:1.1
 *
 * @since Feb 17, 2018, 12:58:00 AM
 */
class CFunction {
    /**
     * @var string|callable
     */
    public $func = '';

    /**
     * @var array
     */
    public $args = [];

    public $requires = [];

    public $type = 'defined'; //defined,class,

    private function __construct($func) {
        $this->func = CHelper::closure()->deserializeClosure($func);
    }

    public static function factory($func) {
        return new CFunction($func);
    }

    public function setFunction($func) {
        $this->func = $func;
        return $this;
    }

    public function getFunction() {
        return $this->func;
    }

    public function setArgs(array $args) {
        $this->args = $args;
        return $this;
    }

    public function addArg($arg) {
        $this->args[] = $arg;
        return $this;
    }

    public function addRequire($p) {
        $this->requires[] = $p;
        return $this;
    }

    public function setRequire($p) {
        if ($p == null) {
            $p = [];
        }
        if (is_string($p)) {
            $p = [$p];
        }

        $this->requires = $p;
        return $this;
    }

    public function execute($args = []) {
        if (!is_array($args)) {
            $args = [$args];
        }
        foreach ($this->requires as $r) {
            if (strlen($r) > 0 && file_exists($r)) {
                require_once $r;
            }
        }
        $args = array_merge($args, $this->args);

        $error = 0;
        if ($error == 0) {
            if (is_array($this->func)) {
                if (is_callable($this->func)) {
                    return call_user_func_array($this->func, $args);
                } else {
                    $error++;
                }
            }
        }
        if ($error == 0) {
            if ($this->func instanceof Closure) {
                return call_user_func_array($this->func, $args);
            }
        }
        if ($error == 0) {
            if (is_callable($this->func)) {
                return call_user_func_array($this->func, $args);
            }
        }
        if ($error == 0) {
            //not array let check if it is a function name
            if (function_exists($this->func)) {
                return call_user_func_array($this->func, $args);
            }
        }
        if ($error == 0) {
            //not the function name, let check it if it is function from ctransform class
            if (is_callable(['ctransform', $this->func])) {
                return call_user_func_array(['ctransform', $this->func], $args);
            }
        }
        if ($error == 0) {
            //not the function name, let check it if it is function from CHelper_Transform class
            $transform = CHelper::transform();
            if (is_callable([$transform, $this->func])) {
                return call_user_func_array([$transform, $this->func], $args);
            }
        }
        if ($error == 0) {
            //it is not method from ctransform class, try the other class if it is found ::
            if (strpos($this->func, '::') !== false) {
                return call_user_func_array(explode('::', $this->func), $args);
            }
        }
        //last return this name of function
        if ($error == 0) {
            return $this->func;
        }
        return 'ERROR ON CFUNCTION';
    }
}
