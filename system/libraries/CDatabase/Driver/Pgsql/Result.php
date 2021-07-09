<?php

/**
 * PostgreSQL Result
 */
class CDatabase_Driver_Pgsql_Result extends CDatabase_Result {
    // Data fetching types
    protected $fetch_type = 'pgsql_fetch_object';

    protected $return_type = PGSQL_ASSOC;

    /**
     * Sets up the result variables.
     *
     * @param resource $result query result
     * @param resource $link   database link
     * @param bool     $object return objects or arrays
     * @param string   $sql    SQL query that was run
     */
    public function __construct($result, $link, $object = true, $sql = '') {
        $this->link = $link;
        $this->result = $result;

        // If the query is a resource, it was a SELECT, SHOW, DESCRIBE, EXPLAIN query
        if (is_resource($result)) {
            // Its an DELETE, INSERT, REPLACE, or UPDATE query
            if (preg_match('/^(?:delete|insert|replace|update)\b/iD', trim($sql), $matches)) {
                $this->insert_id = (strtolower($matches[0]) == 'insert') ? $this->insertId() : false;
                $this->total_rows = pg_affected_rows($this->result);
            } else {
                $this->current_row = 0;
                $this->total_rows = pg_num_rows($this->result);
                $this->fetch_type = ($object === true) ? 'pg_fetch_object' : 'pg_fetch_array';
            }
        } else {
            throw new CDatabase_Exception('There was an SQL error: :error', [':error' => pg_last_error() . ' - ' . $sql]);
        }

        // Set result type
        $this->result($object);

        // Store the SQL
        $this->sql = $sql;
    }

    /**
     * Magic __destruct function, frees the result.
     */
    public function __destruct() {
        if (is_resource($this->result)) {
            pg_free_result($this->result);
        }
    }

    public function result($object = true, $type = PGSQL_ASSOC) {
        $this->fetch_type = ((bool) $object) ? 'pg_fetch_object' : 'pg_fetch_array';

        // This check has to be outside the previous statement, because we do not
        // know the state of fetch_type when $object = NULL
        // NOTE - The class set by $type must be defined before fetching the result,
        // autoloading is disabled to save a lot of stupid overhead.
        if ($this->fetch_type == 'pg_fetch_object') {
            $this->return_type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
        } else {
            $this->return_type = $type;
        }

        return $this;
    }

    public function resultArray($object = null, $type = PGSQL_ASSOC) {
        $rows = [];

        if (is_string($object)) {
            $fetch = $object;
        } elseif (is_bool($object)) {
            if ($object === true) {
                $fetch = 'pg_fetch_object';

                // NOTE - The class set by $type must be defined before fetching the result,
                // autoloading is disabled to save a lot of stupid overhead.
                $type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
            } else {
                $fetch = 'pg_fetch_array';
            }
        } else {
            // Use the default config values
            $fetch = $this->fetch_type;

            if ($fetch == 'pg_fetch_object') {
                $type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
            }
        }

        if ($this->total_rows) {
            pg_result_seek($this->result, 0);

            while ($row = $fetch($this->result, null, $type)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    public function insertId() {
        if ($this->insert_id === null) {
            $query = 'SELECT LASTVAL() AS insert_id';

            // Disable error reporting for this, just to silence errors on
            // tables that have no serial column.
            $ER = error_reporting(0);

            $result = pg_query($this->link, $query);
            $insert_id = pg_fetch_array($result, null, PGSQL_ASSOC);

            $this->insert_id = $insert_id['insert_id'];

            // Reset error reporting
            error_reporting($ER);
        }

        return $this->insert_id;
    }

    public function seek($offset) {
        if ($this->offsetExists($offset) and pg_result_seek($this->result, $offset)) {
            // Set the current row to the offset
            $this->current_row = $offset;

            return true;
        }

        return false;
    }

    public function listFields() {
        $field_names = [];

        $fields = pg_num_fields($this->result);
        for ($i = 0; $i < $fields; ++$i) {
            $field_names[] = pg_field_name($this->result, $i);
        }

        return $field_names;
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
        $fetch = $this->fetch_type;
        return $fetch($this->result, null, $this->return_type);
    }
}
