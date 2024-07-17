<?php

defined('SYSPATH') or die('No direct access allowed.');

use Opis\Closure\SerializableClosure;

abstract class CAjax_Engine implements CAjax_EngineInterface {
    /**
     * @var CAjax_Method
     */
    protected $ajaxMethod;

    /**
     * @var array
     */
    protected $input;

    /**
     * @var array
     */
    protected $args;

    /**
     * @param string $methodCall
     */
    public function __construct(CAjax_Method $ajaxMethod) {
        $this->ajaxMethod = $ajaxMethod;
        $this->input = array_merge($_GET, $_POST);
        if (strtoupper($ajaxMethod->getMethod()) == 'GET') {
            $this->input = $_GET;
        }
        if (strtoupper($ajaxMethod->getMethod()) == 'POST') {
            $this->input = $_POST;
        }
    }

    public function setInput(array $input) {
        $this->input = $input;
    }

    /**
     * Get Input.
     *
     * @return array
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->ajaxMethod->getMethod();
    }

    /**
     * Get Data.
     *
     * @return array
     */
    public function getData() {
        return $this->ajaxMethod->getData();
    }

    /**
     * Get Type.
     *
     * @return string
     */
    public function getType() {
        return $this->ajaxMethod->getType();
    }

    /**
     * Get args.
     *
     * @return array
     */
    public function getArgs() {
        return $this->ajaxMethod->getArgs();
    }

    public function toJsonResponse($errCode, $errMessage, $data = []) {
        return c::response()->json([
            'errCode' => $errCode,
            'errMessage' => $errMessage,
            'data' => $data,
        ]);
    }

    public function invokeCallback($callback, array $args = []) {
        if ($callback instanceof SerializableClosure) {
            return $callback->__invoke(...$args);
        }
        if ($callback instanceof CFunction_SerializableClosure) {
            return $callback->__invoke(...$args);
        }

        return call_user_func_array($callback, $args);
    }
}
