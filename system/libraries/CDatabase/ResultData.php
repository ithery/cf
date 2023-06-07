<?php

class CDatabase_ResultData implements ArrayAccess, Iterator, Countable {
    /**
     * @var PDOStatement
     */
    protected $data;

    protected $currentRow = 0;

    protected $totalRows = 0;

    protected $fetchType = PDO::FETCH_OBJ;

    public function __construct(PDOStatement $data) {
        $this->data = $data;

        $this->currentRow = 0;
        $this->totalRows = $data->rowCount();
    }

    public function resultArray($object = null, $type = 'stdClass') {
        if (is_string($object)) {
            $fetch = $object;
        } elseif (is_bool($object)) {
            if ($object === true) {
                $fetch = PDO::FETCH_OBJ;

                // NOTE - The class set by $type must be defined before fetching the result,
                // autoloading is disabled to save a lot of stupid overhead.
                $type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
            } else {
                $fetch = PDO::FETCH_ASSOC;
            }
        } else {
            // Use the default config values
            $fetch = $this->fetchType;

            if ($fetch == PDO::FETCH_CLASS) {
                $type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
            }
        }

        if ($this->data->rowCount()) {
            // Reset the pointer location to make sure things work properly
            $this->seek(0);
            if ($fetch == PDO::FETCH_CLASS) {
                $this->data->fetchAll($fetch, $type);
            }

            $all = $this->data->fetchAll($fetch);

            return $all;
        }

        return [];
    }

    public function result($object = true) {
        $this->fetchType = $object ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function count() {
        return $this->totalRows;
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
        if (!$this->seek($offset)) {
            return false;
        }

        // Return the row by calling the defined fetching callback
        return $this->data->fetch($this->fetchType, PDO::FETCH_ORI_ABS, $offset);
        //return carr::get($this->data, $offset);
    }

    public function seek($offset) {
        if ($this->offsetExists($offset)) {
            $this->currentRow = $offset;

            return true;
        }

        return false;
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

    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null) {
        return $this->resultArray(false);
    }

    public function listFields() {
        $field_names = [];
        cdbg::dd($this->data->fetchColumn());
        while ($field = $this->data->fetchColumn()) {
            $field_names[] = $field->name;
        }

        return $field_names;
    }
}
