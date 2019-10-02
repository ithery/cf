<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 1, 2019, 5:19:03 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait DelegatedMacroable {

    use CTrait_Macroable {
        __call as __callMacro;
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (method_exists($this->getDelegate(), $method)) {
            return call_user_func_array([$this->getDelegate(), $method], $parameters);
        }
        array_unshift($parameters, $this);
        return $this->__callMacro($method, $parameters);
    }

    /**
     * @return object
     */
    abstract public function getDelegate();
}
