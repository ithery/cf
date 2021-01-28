<?php

/**
 * CDatabase_Result
 */
abstract class CDatabase_Result implements CDatabase_ResultInterface, ArrayAccess, Iterator, Countable {
    // Result resource, insert id, and SQL
    protected $result;

    protected $insert_id;

    protected $sql;

    // Current and total rows
    protected $current_row = 0;

    protected $total_rows = 0;

    // Fetch function and return type
    protected $fetch_type;

    protected $return_type;

    /**
     * Returns the SQL used to fetch the result.
     *
     * @return string
     */
    public function sql() {
        return $this->sql;
    }

    /**
     * Returns the insert id from the result.
     *
     * @return mixed
     */
    public function insert_id() {
        return $this->insert_id;
    }

    /**
     * Prepares the query result.
     *
     * @param mixed $object
     * @param mixed $type
     *
     * @return CDatabase_Result
     */
    abstract public function result($object = true, $type = false);

    /**
     * Builds an array of query results.
     *
     * @param null|bool $object
     * @param mixed     $type
     *
     * @return array
     */
    abstract public function result_array($object = null, $type = false);

    /**
     * Gets the fields of an already run query.
     *
     * @return array
     */
    abstract public function list_fields();

    /**
     * Seek to an offset in the results.
     *
     * @param int $offset
     *
     * @return bool
     */
    abstract public function seek($offset);

    /**
     * Countable: count
     */
    public function count() {
        return $this->total_rows;
    }

    /**
     * ArrayAccess: offsetExists
     *
     * @param mixed $offset
     */
    public function offsetExists($offset) {
        if ($this->total_rows > 0) {
            $min = 0;
            $max = $this->total_rows - 1;

            return !($offset < $min or $offset > $max);
        }

        return false;
    }

    /**
     * ArrayAccess: offsetGet
     *
     * @param mixed $offset
     */
    public function offsetGet($offset) {
        if (!$this->seek($offset)) {
            return false;
        }

        // Return the row by calling the defined fetching callback
        return call_user_func($this->fetch_type, $this->result, $this->return_type);
    }

    /**
     * ArrayAccess: offsetSet
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws CDatabase_Exception
     */
    public function offsetSet($offset, $value) {
        throw new CDatabase_Exception('Query results are read only');
    }

    /**
     * ArrayAccess: offsetUnset
     *
     * @param mixed $offset
     *
     * @throws CDatabase_Exception
     */
    public function offsetUnset($offset) {
        throw new CDatabase_Exception('Query results are read only');
    }

    /**
     * Iterator: current
     */
    public function current() {
        return $this->offsetGet($this->current_row);
    }

    /**
     * Iterator: key
     */
    public function key() {
        return $this->current_row;
    }

    /**
     * Iterator: next
     */
    public function next() {
        ++$this->current_row;
        return $this;
    }

    /**
     * Iterator: prev
     */
    public function prev() {
        --$this->current_row;
        return $this;
    }

    /**
     * Iterator: rewind
     */
    public function rewind() {
        $this->current_row = 0;
        return $this;
    }

    /**
     * Iterator: valid
     */
    public function valid() {
        return $this->offsetExists($this->current_row);
    }
}
