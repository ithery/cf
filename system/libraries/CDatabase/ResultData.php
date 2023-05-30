<?php

class CDatabase_ResultData implements ArrayAccess, Iterator, Countable {
    /**
     * @var array
     */
    protected $data;

    protected $currentRow = 0;

    protected $totalRows = 0;

    public function __construct(array $data) {
        $this->data = $data;
        $this->currentRow = 0;
        $this->totalRows = count($data);
    }

    public function resultArray() {
        return $this->result(false);
    }

    public function result($object = true) {
        if (!$object) {
            return c::collect($this->data)->map(function ($item) {
                return (array) $item;
            })->all();
        }

        return $this->data;
    }

    #[\ReturnTypeWillChange]
    public function count() {
        return count($this->data);
    }

    /**
     * ArrayAccess: offsetExists.
     *
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset) {
        if ($this->totalRows > 0) {
            $min = 0;
            $max = $this->totalRows - 1;

            return !($offset < $min or $offset > $max);
        }

        return false;
    }

    /**
     * ArrayAccess: offsetGet.
     *
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset) {
        return carr::get($this->data, $offset);
    }

    /**
     * ArrayAccess: offsetSet.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws CDatabase_Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value) {
        throw new CDatabase_Exception('Query results are read only');
    }

    /**
     * ArrayAccess: offsetUnset.
     *
     * @param mixed $offset
     *
     * @throws CDatabase_Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset) {
        throw new CDatabase_Exception('Query results are read only');
    }

    /**
     * Iterator: current.
     */
    public function current() {
        return $this->offsetGet($this->currentRow);
    }

    /**
     * Iterator: key.
     */
    public function key() {
        return $this->currentRow;
    }

    /**
     * Iterator: next.
     */
    #[\ReturnTypeWillChange]
    public function next() {
        ++$this->currentRow;

        return $this;
    }

    /**
     * Iterator: prev.
     */
    public function prev() {
        --$this->currentRow;

        return $this;
    }

    /**
     * Iterator: rewind.
     */
    #[\ReturnTypeWillChange]
    public function rewind() {
        $this->currentRow = 0;

        return $this;
    }

    /**
     * Iterator: valid.
     */
    #[\ReturnTypeWillChange]
    public function valid() {
        return $this->offsetExists($this->currentRow);
    }
}
