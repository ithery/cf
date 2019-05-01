<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 2:57:28 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class COptional {

    use CTrait_Macroable {
        __call as macroCall;
    }
    
    /**
     * 
     * @param mixed $value
     * @return COptional
     */
    public static function create($value) {
        return new static($value);
    }

    /**
     * The underlying object.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new optional instance.
     *
     * @param  mixed  $value
     * @return void
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * Dynamically access a property on the underlying object.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key) {
        if (is_object($this->value)) {
            return $this->value->{$key};
        }
    }

    /**
     * Dynamically pass a method to the underlying object.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (is_object($this->value)) {
            return $this->value->{$method}(...$parameters);
        }
    }

    /**
     * Dynamically pass a method to the underlying object.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (is_object($this->value)) {
            return $this->value->{$method}(...$parameters);
        }
    }

}
