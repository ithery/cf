<?php

trait CPeriod_Trait_IterableImplementationTrait {
    protected $position = 0;

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset) {
        return carr::get($this->periods, $offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->periods[] = $value;

            return;
        }

        $this->periods[$offset] = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->periods);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset) {
        unset($this->periods[$offset]);
    }

    /**
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next() {
        $this->position++;
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key() {
        return $this->position;
    }

    /**
     * @return boolean
     */
    #[\ReturnTypeWillChange]
    public function valid() {
        return array_key_exists($this->position, $this->periods);
    }

    /**
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind() {
        $this->position = 0;
    }

    /**
     * @return integer
     */
    #[\ReturnTypeWillChange]
    public function count() {
        return count($this->periods);
    }
}
