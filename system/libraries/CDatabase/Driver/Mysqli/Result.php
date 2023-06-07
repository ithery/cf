<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:31:26 AM
 */
class CDatabase_Driver_Mysqli_Result extends CDatabase_Result {
    use CTrait_Compat_Database_Driver_Mysqli_Result;
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

        if (!$this->link->multi_query($sql)) {
            // SQL error
            throw CDatabase_Exception::queryException($this->link->error . ' - ' . $sql);
        } else {
            $this->result = $this->link->store_result();

            // If the query is an object, it was a SELECT, SHOW, DESCRIBE, EXPLAIN query
            if (is_object($this->result)) {
                $this->current_row = 0;
                $this->total_rows = $this->result->num_rows;
                $this->fetch_type = ($object === true) ? 'fetch_object' : 'fetch_array';
            } elseif ($this->link->error) {
                // SQL error
                throw CDatabase_Exception::queryException($this->link->error . ' - ' . $sql);
            } else {
                // Its an DELETE, INSERT, REPLACE, or UPDATE query
                $this->insert_id = $this->link->insert_id;
                $this->total_rows = $this->link->affected_rows;
            }
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

        if ($this->result->num_rows) {
            // Reset the pointer location to make sure things work properly
            $this->result->data_seek(0);

            while ($row = $this->result->$fetch($type)) {
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
        if ($this->offsetExists($offset) and $this->result->data_seek($offset)) {
            // Set the current row to the offset
            $this->current_row = $offset;

            return true;
        }

        return false;
    }

    public function offsetGet($offset) {
        if (!$this->seek($offset)) {
            return false;
        }

        // Return the row
        $fetch = $this->fetch_type;

        return $this->result->$fetch($this->return_type);
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null) {
        return $this->resultArray(false);
    }
}
