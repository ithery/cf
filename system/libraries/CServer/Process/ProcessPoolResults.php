<?php

class CServer_Process_ProcessPoolResults implements ArrayAccess {
    /**
     * The results of the processes.
     *
     * @var array
     */
    protected $results = [];

    /**
     * Create a new process pool result set.
     *
     * @param array $results
     *
     * @return void
     */
    public function __construct(array $results) {
        $this->results = $results;
    }

    /**
     * Get the results as a collection.
     *
     * @return \CCollection
     */
    public function collect() {
        return new CCollection($this->results);
    }

    /**
     * Determine if the given array offset exists.
     *
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool {
        return isset($this->results[$offset]);
    }

    /**
     * Get the result at the given offset.
     *
     * @param int $offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed {
        return $this->results[$offset];
    }

    /**
     * Set the result at the given offset.
     *
     * @param int   $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void {
        $this->results[$offset] = $value;
    }

    /**
     * Unset the result at the given offset.
     *
     * @param int $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void {
        unset($this->results[$offset]);
    }
}
