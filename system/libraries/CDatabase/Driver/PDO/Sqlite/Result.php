<?php

/**
 * PostgreSQL Result.
 */
class CDatabase_Driver_PDO_Sqlite_Result extends CDatabase_Result {
    // Data fetching types
    protected $fetch_type = PDO::FETCH_OBJ;

    protected $return_type = PDO::FETCH_ASSOC;

    /**
     * Sets up the result variables.
     *
     * @param mixed  $result query result
     * @param mixed  $link   database link
     * @param bool   $object return objects or arrays
     * @param string $sql    SQL query that was run
     */
    public function __construct($result, $link, $object = true, $sql = null) {
        if (is_object($result) or $result = $link->prepare($sql)) {
            // run the query. Return true if success, false otherwise
            if (!$result->execute()) {
                // Throw Kohana Exception with error message. See PDOStatement errorInfo() method
                $arr_infos = $result->errorInfo();

                throw new CDatabase_Exception('There was an SQL error: :error', [':error' => $arr_infos[2]]);
            }

            if (preg_match('/^SELECT|PRAGMA|EXPLAIN/i', $sql)) {
                $this->result = $result;
                $this->current_row = 0;

                $this->total_rows = $this->sqliteRowCount();

                $this->fetch_type = ($object === true) ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
            } elseif (preg_match('/^DELETE|INSERT|UPDATE/i', $sql)) {
                $this->insert_id = $link->lastInsertId();

                $this->total_rows = $result->rowCount();
            }
        } else {
            // SQL error
            throw new CDatabase_Exception('There was an SQL error: :error', [':error' => $link->errorInfo() . ' - ' . $sql]);
        }

        // Set result type
        $this->result($object);

        // Store the SQL
        $this->sql = $sql;
    }

    private function sqliteRowCount() {
        $count = 0;
        while ($this->result->fetch()) {
            $count++;
        }

        // The query must be re-fetched now.
        $this->result->execute();

        return $count;
    }

    /**
     * Destructor: __destruct
     *  Magic __destruct function, frees the result.
     */
    public function __destruct() {
        if (is_object($this->result)) {
            $this->result->closeCursor();
            $this->result = null;
        }
    }

    public function result($object = true, $type = PDO::FETCH_BOTH) {
        $this->fetch_type = (bool) $object ? PDO::FETCH_OBJ : PDO::FETCH_BOTH;

        if ($this->fetch_type == PDO::FETCH_OBJ) {
            $this->return_type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
        } else {
            $this->return_type = $type;
        }

        return $this;
    }

    public function asArray($object = null, $type = PDO::FETCH_ASSOC) {
        return $this->resultArray($object, $type);
    }

    public function resultArray($object = null, $type = PDO::FETCH_ASSOC) {
        $rows = [];

        if (is_string($object)) {
            $fetch = $object;
        } elseif (is_bool($object)) {
            if ($object === true) {
                $fetch = PDO::FETCH_OBJ;

                // NOTE - The class set by $type must be defined before fetching the result,
                // autoloading is disabled to save a lot of stupid overhead.
                $type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
            } else {
                $fetch = PDO::FETCH_OBJ;
            }
        } else {
            // Use the default config values
            $fetch = $this->fetch_type;

            if ($fetch == PDO::FETCH_OBJ) {
                $type = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
            }
        }

        try {
            while ($row = $this->result->fetch($fetch)) {
                $rows[] = $row;
            }
        } catch (PDOException $e) {
            throw new CDatabase_Exception('There was an SQL error: :error', [':error' => $e->getMessage()]);

            return false;
        }

        return $rows;
    }

    public function listFields() {
        $field_names = [];
        for ($i = 0, $max = $this->result->columnCount(); $i < $max; $i++) {
            $info = $this->result->getColumnMeta($i);
            $field_names[] = $info['name'];
        }

        return $field_names;
    }

    public function seek($offset) {
        // To request a scrollable cursor for your PDOStatement object, you must
        // set the PDO::ATTR_CURSOR attribute to PDO::CURSOR_SCROLL when you
        // prepare the statement.
        CF::log('error', get_class($this) . ' does not support scrollable cursors, ' . __FUNCTION__ . ' call ignored');

        return false;
    }

    public function offsetGet($offset) {
        try {
            return $this->result->fetch($this->fetch_type, PDO::FETCH_ORI_ABS, $offset);
        } catch (PDOException $e) {
            throw new CDatabase_Exception('There was an SQL error: :error', [':error' => $e->getMessage()]);
        }
    }

    public function rewind() {
        // Same problem that seek() has, see above.
        return $this->seek(0);
    }
}
