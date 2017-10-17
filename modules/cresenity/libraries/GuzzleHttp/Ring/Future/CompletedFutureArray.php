<?php
/*
namespace GuzzleHttp\Ring\Future;
*/
/**
 * Represents a future array that has been completed successfully.
 */
class GuzzleHttp_Ring_Future_CompletedFutureArray extends GuzzleHttp_Ring_Future_CompletedFutureValue implements GuzzleHttp_Ring_Future_FutureArrayInterface
{
    public function __construct(array $result)
    {
        parent::__construct($result);
    }

    public function offsetExists($offset)
    {
        return isset($this->result[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->result[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->result[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->result[$offset]);
    }

    public function count()
    {
        return count($this->result);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->result);
    }
}
