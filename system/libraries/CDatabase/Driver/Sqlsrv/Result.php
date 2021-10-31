<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:31:26 AM
 */
class CDatabase_Driver_Sqlsrv_Result extends CDatabase_Result {
    /**
     * @var \mysqli
     */
    protected $link;

    // Data fetching types
    protected $fetch_type = 'fetch_object';

    protected $return_type = MYSQLI_ASSOC;

    /**
     * Sets up the result variables.
     *
     * @param object $link   database link
     * @param bool   $object return objects or arrays
     * @param string $sql    SQL query that was run
     */
    public function __construct($link, $object = true, $sql = null) {
        $this->link = $link;
        $stmt = sqlsrv_query($this->link, $sql, [], ['Scrollable' => 'buffered']);
        $errors = sqlsrv_errors();

        if ($stmt === false) {
            // SQL error
            throw new CDatabase_Exception('There was an SQL error: :error', [':error' => $this->getErrorMessage() . ' - ' . $sql]);
        } else {
            $this->result = $stmt;
            $error = sqlsrv_errors();

            // If the query is an object, it was a SELECT, SHOW, DESCRIBE, EXPLAIN query
            if (sqlsrv_has_rows($this->result)) {
                $this->current_row = 0;
                $this->total_rows = sqlsrv_num_rows($this->result);
                $this->fetch_type = ($object === true) ? 'fetch_object' : 'fetch_array';
            } elseif ($error) {
                // SQL error
                throw new CDatabase_Exception('There was an SQL error: :error', [':error' => $error . ' - ' . $sql]);
            } else {
                // Its an DELETE, INSERT, REPLACE, or UPDATE query
                sqlsrv_next_result($this->result);
                sqlsrv_fetch($this->result);

                $this->insert_id = sqlsrv_get_field($this->result, 0);
                $this->total_rows = sqlsrv_rows_affected($this->result);
            }
        }

        // Set result type
        $this->result($object);

        // Store the SQL
        $this->sql = $sql;
    }

    public function getErrorMessage() {
        if ($this->isError()) {
            $errors = sqlsrv_errors();
            return carr::get($errors, 'message');
        }
        return 'unknown error sqlsrv';
    }

    public function isError() {
        $errors = sqlsrv_errors();
        return is_array($errors);
    }

    /**
     * Magic __destruct function, frees the result.
     */
    public function __destruct() {
        if (is_object($this->result)) {
            @$this->result->free_result();
            // this is kinda useless, but needs to be done to avoid the "Commands out of sync; you
            // can't run this command now" error. Basically, we get all results after the first one
            // (the one we actually need) and free them.
            if (is_resource($this->link) && $this->link->more_results()) {
                do {
                    if ($result = $this->link->store_result()) {
                        $result->free_result();
                    }
                } while ($this->link->next_result());
            }
        }
    }

    public function result($object = true, $type = MYSQLI_ASSOC) {
        $this->fetch_type = ((bool) $object) ? 'fetch_object' : 'fetch_array';

        // This check has to be outside the previous statement, because we do not
        // know the state of fetch_type when $object = NULL
        // NOTE - The class set by $type must be defined before fetching the result,
        // autoloading is disabled to save a lot of stupid overhead.
        if ($this->fetch_type == 'fetch_object') {
            $this->return_type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
        } else {
            $this->return_type = $type;
        }

        return $this;
    }

    public function resultArray($object = null, $type = MYSQLI_ASSOC) {
        $rows = [];

        if (is_string($object)) {
            $fetch = $object;
        } elseif (is_bool($object)) {
            if ($object === true) {
                $fetch = 'fetch_object';

                // NOTE - The class set by $type must be defined before fetching the result,
                // autoloading is disabled to save a lot of stupid overhead.
                $type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
            } else {
                $fetch = 'fetch_array';
            }
        } else {
            // Use the default config values
            $fetch = $this->fetch_type;

            if ($fetch == 'fetch_object') {
                $type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
            }
        }

        if (sqlsrv_has_rows($this->result)) {
            // Reset the pointer location to make sure things work properly
            sqlsrv_fetch($this->result, SQLSRV_SCROLL_FIRST);
            //$this->result->data_seek(0);
            $func = 'sqlsrv_' . $fetch;
            while ($row = $func($this->result)) {
                $rows[] = $row;
            }
        }

        return isset($rows) ? $rows : [];
    }

    public function listFields() {
        $field_names = [];
        while ($field = $this->result->fetch_field()) {
            $field_names[] = $field->name;
        }

        return $field_names;
    }

    public function seek($offset) {
        $func = 'sqlsrv_' . $this->fetch_type;

        if ($this->offsetExists($offset) and sqlsrv_fetch($this->result, SQLSRV_SCROLL_ABSOLUTE, $offset)) {
            // Set the current row to the offset
            $this->current_row = $offset;

            return true;
        }

        return false;
    }

    public function offsetGet($offset) {
        if ($this->total_rows <= $offset) {
            return false;
        }

        // Return the row
        $fetch = $this->fetch_type;
        $func = 'sqlsrv_' . $fetch;
        return $func($this->result);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null) {
        return $this->resultArray(false);
    }
}
