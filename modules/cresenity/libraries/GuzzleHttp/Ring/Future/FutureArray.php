<?php

/**
 * Represents a future array value that when dereferenced returns an array.
 */
class GuzzleHttp_Ring_Future_FutureArray implements GuzzleHttp_Ring_Future_FutureArrayInterface
{
    use GuzzleHttp_Ring_Future_MagicFutureTrait;

    public function offsetExists($offset)
    {
        return isset($this->_value[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->_value[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->_value[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->_value[$offset]);
    }

    public function count()
    {
        return count($this->_value);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->_value);
    }

    
}
