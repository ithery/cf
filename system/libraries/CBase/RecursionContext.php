<?php

final class CBase_RecursionContext {
    /**
     * @var array[]
     */
    private $arrays;

    /**
     * @var \SplObjectStorage
     */
    private $objects;

    /**
     * Initialises the context.
     */
    public function __construct() {
        $this->arrays = [];
        $this->objects = new \SplObjectStorage();
    }

    /**
     * Adds a value to the context.
     *
     * @param array|object $value the value to add
     *
     * @throws InvalidArgumentException Thrown if $value is not an array or object
     *
     * @return int|string the ID of the stored value, either as a string or integer
     */
    public function add(&$value) {
        if (is_array($value)) {
            return $this->addArray($value);
        } elseif (is_object($value)) {
            return $this->addObject($value);
        }

        throw new InvalidArgumentException(
            'Only arrays and objects are supported'
        );
    }

    /**
     * Checks if the given value exists within the context.
     *
     * @param array|object $value the value to check
     *
     * @throws InvalidArgumentException Thrown if $value is not an array or object
     *
     * @return int|string|false the string or integer ID of the stored value if it has already been seen, or false if the value is not stored
     */
    public function contains(&$value) {
        if (is_array($value)) {
            return $this->containsArray($value);
        } elseif (is_object($value)) {
            return $this->containsObject($value);
        }

        throw new InvalidArgumentException(
            'Only arrays and objects are supported'
        );
    }

    /**
     * @param array $array
     *
     * @return bool|int
     */
    private function addArray(array &$array) {
        $key = $this->containsArray($array);
        if ($key !== false) {
            return $key;
        }
        $key = count($this->arrays);
        $this->arrays[] = &$array;
        if (!isset($array[PHP_INT_MAX]) && !isset($array[PHP_INT_MAX - 1])) {
            $array[] = $key;
            $array[] = $this->objects;
        } else { /* cover the improbable case too */
            do {
                $key = random_int(PHP_INT_MIN, PHP_INT_MAX);
            } while (isset($array[$key]));
            $array[$key] = $key;
            do {
                $key = random_int(PHP_INT_MIN, PHP_INT_MAX);
            } while (isset($array[$key]));
            $array[$key] = $this->objects;
        }

        return $key;
    }

    /**
     * @param object $object
     *
     * @return string
     */
    private function addObject($object) {
        if (!$this->objects->contains($object)) {
            $this->objects->attach($object);
        }

        return spl_object_hash($object);
    }

    /**
     * @param array $array
     *
     * @return int|false
     */
    private function containsArray(array &$array) {
        $end = array_slice($array, -2);

        return isset($end[1]) && $end[1] === $this->objects ? $end[0] : false;
    }

    /**
     * @param object $value
     *
     * @return string|false
     */
    private function containsObject($value) {
        if ($this->objects->contains($value)) {
            return spl_object_hash($value);
        }

        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function __destruct() {
        foreach ($this->arrays as &$array) {
            if (is_array($array)) {
                array_pop($array);
                array_pop($array);
            }
        }
    }
}
