<?php
/**
 * ODBC Result.
 */
class CDatabase_Driver_Odbc_Result extends CDatabase_Result {
    // Fetch function and return type
    protected $fetch_type = 'odbc_fetch_object';

    protected $return_type = false;

    protected $link = null;

    protected $sql = null;

    /**
     * Sets up the result variables.
     *
     * @param resource $result query result
     * @param resource $link   database link
     * @param bool     $object return objects or arrays
     * @param string   $sql    SQL query that was run
     */
    public function __construct($result, $link, $object = true, $sql = '') {
        $this->result = $result;
        $this->link = $link;
        $this->sql = $sql;
        // If the query is a resource, it was a SELECT, SHOW, DESCRIBE, EXPLAIN query
        if (is_resource($result)) {
            $this->current_row = 0;
            $this->total_rows = $this->odbcNumRows($this->result);

            $this->fetch_type = ($object === true) ? 'odbc_fetch_object' : 'odbc_fetch_array';
        } elseif (is_bool($result)) {
            if ($result == false) {
                // SQL error
                throw CDatabase_Exception::queryException(odbc_errormsg($link) . ' - ' . $sql);
            } else {
                // Its an DELETE, INSERT, REPLACE, or UPDATE query
                // NOTE: Cannot retrieve these in ODBC.
                //$this->insert_id  = mysql_insert_id($link);
                //$this->total_rows = mysql_affected_rows($link);
                // Its an DELETE, INSERT, REPLACE, or UPDATE querys
                //$last_id          = odbc_query('SELECT @@IDENTITY AS last_id', $link);
                //$result           = odbc_fetch_assoc($last_id);
                //$this->insert_id  = $result['last_id'];
                $this->total_rows = odbc_num_rows($link);
            }
        }

        // Set result type
        $this->result($object);

        // Store the SQL
        $this->sql = $sql;
    }

    /**
     * Destruct, the cleanup crew!
     */
    public function __destruct() {
        if (is_resource($this->result)) {
            odbc_free_result($this->result);
        }
    }

    public function result($object = true, $type = false) {
        $this->fetch_type = ((bool) $object) ? 'odbc_fetch_object' : 'odbc_fetch_array';

        // This check has to be outside the previous statement, because we do not
        // know the state of fetch_type when $object = NULL
        // NOTE - The class set by $type must be defined before fetching the result,
        // autoloading is disabled to save a lot of stupid overhead.
        if ($this->fetch_type == 'odbc_fetch_object') {
            $this->return_type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
        } else {
            $this->return_type = $type;
        }

        return $this;
    }

    public function asArray($object = null, $type = false) {
        return $this->resultArray($object, $type);
    }

    public function resultArray($object = null, $type = false) {
        $rows = [];

        if (is_string($object)) {
            $fetch = $object;
        } elseif (is_bool($object)) {
            if ($object === true) {
                $fetch = 'odbc_fetch_object';

                // NOTE - The class set by $type must be defined before fetching the result,
                // autoloading is disabled to save a lot of stupid overhead.
                $type = class_exists($type, false) ? $type : 'stdClass';
            } else {
                $fetch = 'odbc_fetch_array';
            }
        } else {
            // Use the default config values
            $fetch = $this->fetch_type;

            if ($fetch == 'odbc_fetch_object') {
                $type = class_exists($type, false) ? $type : 'stdClass';
            }
        }

        if ($this->odbcNumRows($this->result)) {
            // Reset the pointer location to make sure things work properly
            // mysql_data_seek($this->result, 0);

            while ($row = $fetch($this->result, $type)) {
                $rows[] = $row;
            }
        }

        return isset($rows) ? $rows : [];
    }

    public function odbcFieldNames($result) {
        $returnArray = [];
        for ($i = 1; $i <= odbc_num_fields($result); $i++) {
            $returnArray[$i - 1] = odbc_field_name($result, $i);
        }

        return $returnArray;
    }

    public function listFields() {
        return $this->odbcFieldNames($this->result);
    }

    public function seek($offset) {
        throw new CDatabase_Exception('ODBC not supported Seek');

        return false;
    }

    public function odbcNumRows($r1) {
        $res = odbc_exec($this->link, 'SELECT @@ROWCOUNT AS cnt');

        if (@odbc_fetch_into($res, $row)) {
            $count = trim($row[0]);
        } else {
            $count = 0;
        }

        odbc_free_result($res);

        return $count;
    }

    // Interface: Countable

    /**
     * Counts the number of rows in the result set.
     *
     * @return int
     */
    public function count() {
        return $this->total_rows;
    }

    /**
     * Determines if the requested offset of the result set exists.
     *
     * @param int $offset offset id
     *
     * @return bool
     */
    public function offsetExists($offset) {
        if ($this->total_rows > 0) {
            $min = 0;
            $max = $this->total_rows - 1;

            return ($offset < $min or $offset > $max) ? false : true;
        }

        return false;
    }

    /**
     * Retreives the requested query result offset.
     *
     * @param int $offset offset id
     *
     * @return mixed
     */
    public function offsetGet($offset) {
        return false;
    }

    /**
     * Sets the offset with the provided value. Since you can't modify query result sets, this function just throws an exception.
     *
     * @param int $offset offset id
     * @param int $value  value
     *
     * @throws CDatabase_Exception
     */
    public function offsetSet($offset, $value) {
        throw new CDatabase_Exception('Query results are read only');
    }

    /**
     * Unsets the offset. Since you can't modify query result sets, this function just throws an exception.
     *
     * @param int $offset offset id
     *
     * @throws CDatabase_Exception
     */
    public function offsetUnset($offset) {
        throw new CDatabase_Exception('Query results are read only');
    }

    // End Interface
    // Interface: Iterator

    /**
     * Retrieves the current result set row.
     *
     * @return mixed
     */
    public function current() {
        return $this->offsetGet($this->current_row);
    }

    /**
     * Retreives the current row id.
     *
     * @return int
     */
    public function key() {
        return $this->current_row;
    }

    /**
     * Moves the result pointer ahead one step.
     *
     * @return int
     */
    public function next() {
        return ++$this->current_row;
    }

    /**
     * Moves the result pointer back one step.
     *
     * @return int
     */
    public function prev() {
        return --$this->current_row;
    }

    /**
     * Moves the result pointer to the beginning of the result set.
     *
     * @return int
     */
    public function rewind() {
        return $this->current_row = 0;
    }

    /**
     * Determines if the current result pointer is valid.
     *
     * @return bool
     */
    public function valid() {
        return $this->offsetExists($this->current_row);
    }
}
