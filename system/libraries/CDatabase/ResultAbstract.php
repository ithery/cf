<?php

/**
 * CDatabase_Result.
 */
abstract class CDatabase_ResultAbstract implements CDatabase_ResultInterface, ArrayAccess, Iterator, Countable {
    use CTrait_Compat_Database_Result;

    // Result resource, insert id, and SQL
    protected $result;

    public function __construct($result) {
        $this->result = $result;
    }

    /**
     * Returns the insert id from the result.
     *
     * @return mixed
     */
    abstract public function insertId();

    /**
     * Builds an array of query results.
     *
     * @param null|bool $object
     * @param mixed     $type
     *
     * @return array
     */
    abstract public function resultArray($object = null, $type = false);

    /**
     * Gets the fields of an already run query.
     *
     * @return array
     */
    abstract public function listFields();

    abstract public function count();

    abstract public function fetch();

    abstract public function seek($offset);

    /**
     * ArrayAccess: offsetExists.
     *
     * @param mixed $offset
     */
    public function offsetExists($offset) {
        if ($this->count() > 0) {
            $min = 0;
            $max = $this->count() - 1;

            return !($offset < $min or $offset > $max);
        }

        return false;
    }

    /**
     * ArrayAccess: offsetGet.
     *
     * @param mixed $offset
     */
    public function offsetGet($offset) {
        if (!$this->seek($offset)) {
            return false;
        }

        // Return the row by calling the defined fetching callback
        return $this->fetch();
    }

    /**
     * ArrayAccess: offsetSet.
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
     * ArrayAccess: offsetUnset.
     *
     * @param mixed $offset
     *
     * @throws CDatabase_Exception
     */
    public function offsetUnset($offset) {
        throw new CDatabase_Exception('Query results are read only');
    }

    public function result() {
        return $this;
    }
}
