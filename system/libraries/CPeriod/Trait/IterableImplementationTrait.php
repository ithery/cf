<?php

trait CPeriod_Trait_IterableImplementationTrait {
    protected $position = 0;

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset) {
        return carr::get($this->periods, $offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->periods[] = $value;

            return;
        }

        $this->periods[$offset] = $value;
    }

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->periods);
    }

    public function offsetUnset($offset) {
        unset($this->periods[$offset]);
    }

    /**
     * @return void
     */
    public function next() {
        $this->position++;
    }

    /**
     * @return mixed
     */
    public function key() {
        return $this->position;
    }

    /**
     * @return boolean
     */
    public function valid() {
        return array_key_exists($this->position, $this->periods);
    }

    /**
     * @return void
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * @return integer
     */
    public function count() {
        return count($this->periods);
    }
}
