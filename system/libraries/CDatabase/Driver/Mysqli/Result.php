<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:31:26 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDatabase_Driver_Mysqli_Result extends CDatabase_Result {

    // Database connection
    protected $link;
    // Data fetching types
    protected $fetch_type = 'mysqli_fetch_object';
    protected $return_type = MYSQLI_ASSOC;

    /**
     * Sets up the result variables.
     *
     * @param  object    database link
     * @param  boolean   return objects or arrays
     * @param  string    SQL query that was run
     */
    public function __construct($link, $object = TRUE, $sql) {
        $this->link = $link;

        if (!$this->link->multi_query($sql)) {
            // SQL error
            throw new CDatabase_Exception('There was an SQL error: :error', array(':error' => $this->link->error . ' - ' . $sql));
        } else {
            $this->result = $this->link->store_result();

            // If the query is an object, it was a SELECT, SHOW, DESCRIBE, EXPLAIN query
            if (is_object($this->result)) {
                $this->current_row = 0;
                $this->total_rows = $this->result->num_rows;
                $this->fetch_type = ($object === TRUE) ? 'fetch_object' : 'fetch_array';
            } elseif ($this->link->error) {
                // SQL error
                throw new CDatabase_Exception('There was an SQL error: :error', array(':error' => $this->link->error . ' - ' . $sql));
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
            if (is_resource($this->link) AND $this->link->more_results()) {
                do {
                    if ($result = $this->link->store_result()) {
                        $result->free_result();
                    }
                } while ($this->link->next_result());
            }
        }
    }

    public function result($object = TRUE, $type = MYSQLI_ASSOC) {
        $this->fetch_type = ((bool) $object) ? 'fetch_object' : 'fetch_array';

        // This check has to be outside the previous statement, because we do not
        // know the state of fetch_type when $object = NULL
        // NOTE - The class set by $type must be defined before fetching the result,
        // autoloading is disabled to save a lot of stupid overhead.
        if ($this->fetch_type == 'fetch_object') {
            $this->return_type = (is_string($type) AND CF::auto_load($type)) ? $type : 'stdClass';
        } else {
            $this->return_type = $type;
        }

        return $this;
    }

    public function as_array($object = NULL, $type = MYSQLI_ASSOC) {
        return $this->result_array($object, $type);
    }

    public function result_array($object = NULL, $type = MYSQLI_ASSOC) {
        $rows = array();

        if (is_string($object)) {
            $fetch = $object;
        } elseif (is_bool($object)) {
            if ($object === TRUE) {
                $fetch = 'fetch_object';

                // NOTE - The class set by $type must be defined before fetching the result,
                // autoloading is disabled to save a lot of stupid overhead.
                $type = (is_string($type) AND CF::auto_load($type)) ? $type : 'stdClass';
            } else {
                $fetch = 'fetch_array';
            }
        } else {
            // Use the default config values
            $fetch = $this->fetch_type;

            if ($fetch == 'fetch_object') {
                $type = (is_string($type) AND CF::auto_load($type)) ? $type : 'stdClass';
            }
        }

        if ($this->result->num_rows) {
            // Reset the pointer location to make sure things work properly
            $this->result->data_seek(0);

            while ($row = $this->result->$fetch($type)) {
                $rows[] = $row;
            }
        }

        return isset($rows) ? $rows : array();
    }

    public function list_fields() {
        $field_names = array();
        while ($field = $this->result->fetch_field()) {
            $field_names[] = $field->name;
        }

        return $field_names;
    }

    public function seek($offset) {
        if ($this->offsetExists($offset) AND $this->result->data_seek($offset)) {
            // Set the current row to the offset
            $this->current_row = $offset;

            return TRUE;
        }

        return FALSE;
    }

    public function offsetGet($offset) {
        if (!$this->seek($offset))
            return FALSE;

        // Return the row
        $fetch = $this->fetch_type;
        return $this->result->$fetch($this->return_type);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null) {
        return $this->result_array();
    }

}
