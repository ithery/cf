<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 2:57:28 AM
 */
class COptional implements ArrayAccess {
    use CTrait_Macroable {
        __call as macroCall;
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
     * @param mixed $value
     *
     * @return void
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * Dynamically access a property on the underlying object.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key) {
        if (is_object($this->value)) {
            return isset($this->value->{$key}) ? $this->value->{$key} : null;
        }
    }

    /**
     * Dynamically check a property exists on the underlying object.
     *
     * @param mixed $name
     *
     * @return bool
     */
    public function __isset($name) {
        if (is_object($this->value)) {
            return isset($this->value->{$name});
        }

        if (is_array($this->value) || $this->value instanceof ArrayObject) {
            return isset($this->value[$name]);
        }

        return false;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key) {
        return carr::accessible($this->value) && carr::exists($this->value, $key);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key) {
        return carr::get($this->value, $key);
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($key, $value) {
        if (carr::accessible($this->value)) {
            $this->value[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $key
     *
     * @return void
     */
    public function offsetUnset($key) {
        if (carr::accessible($this->value)) {
            unset($this->value[$key]);
        }
    }

    /**
     * @param mixed $value
     *
     * @return COptional
     */
    public static function create($value) {
        return new static($value);
    }

    /**
     * Dynamically pass a method to the underlying object.
     *
     * @param string $method
     * @param array  $parameters
     *
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
}
