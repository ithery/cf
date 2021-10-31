<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * ODBC Database Driver
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class CDatabase_Driver_Odbc extends CDatabase_Driver {

    /**
     * Database connection link
     */
    protected $link;

    /**
     * Database configuration
     */
    protected $db_config;

    /**
     * Sets the config for the class.
     *
     * @param  array  database configuration
     */
    public function __construct($config) {
        $this->db_config = $config;

        CF::log(CLogger::DEBUG, 'ODBC Database Driver Initialized');
    }

    /**
     * Closes the database connection.
     */
    public function __destruct() {
        is_resource($this->link) and odbc_close($this->link);
    }

    /**
     * Make the connection
     *
     * @return return connection
     */
    public function connect() {
        // Check if link already exists
        if (is_resource($this->link))
            return $this->link;

        // Import the connect variables
        extract($this->db_config['connection']);

        // Persistent connections enabled?
        $connect = ($this->db_config['persistent'] == TRUE) ? 'odbc_pconnect' : 'odbc_connect';

        // Build the connection info
        $host = isset($host) ? $host : $socket;

        // Windows uses a comma instead of a colon
        $port = (isset($port) AND is_string($port)) ? (KOHANA_IS_WIN ? ',' : ':') . $port : '';

        // Make the connection and select the database
        if (($this->link = $connect($database, $user, $pass))) {
            if ($charset = $this->db_config['character_set']) {
                $this->set_charset($charset);
            }

            // Clear password after successful connect
            $this->config['connection']['pass'] = NULL;

            return $this->link;
        }
        return FALSE;
    }

    public function query($sql) {
        if ($this->db_config['cache']
                AND ! preg_match('#\b(?:INSERT|UPDATE|REPLACE|SET)\b#i', $sql)) {
            $hash = $this->query_hash($sql);

            if (!isset(self::$query_cache[$hash])) {
                // Set the cached object
                self::$query_cache[$hash] = new Odbc_Result(odbc_exec($this->link, $sql), $this->link, $this->db_config['object'], $sql);
            }

            // Return the cached query
            return self::$query_cache[$hash];
        }

        return new COdbc_Result(odbc_exec($this->link, $sql), $this->link, $this->db_config['object'], $sql);
    }

    public function set_charset($charset) {
        # TODO: Add this functionality.
        #$this->query('SET NAMES '.$this->escape_str($charset));
    }

    public function escape_table($table) {
        if (stripos($table, ' AS ') !== FALSE) {
            // Force 'AS' to uppercase
            $table = str_ireplace(' AS ', ' AS ', $table);

            // Runs escape_table on both sides of an AS statement
            $table = array_map(array($this, __FUNCTION__), explode(' AS ', $table));

            // Re-create the AS statement
            return implode(' AS ', $table);
        }
        return '[' . str_replace('.', '[.]', $table) . ']';
    }

    public function escape_column($column) {
        if (!$this->db_config['escape'])
            return $column;

        if ($column == '*')
            return $column;

        // This matches any functions we support to SELECT.
        if (preg_match('/(avg|count|sum|max|min)\(\s*(.*)\s*\)(\s*as\s*(.+)?)?/i', $column, $matches)) {
            if (count($matches) == 3) {
                return $matches[1] . '(' . $this->escape_column($matches[2]) . ')';
            } else if (count($matches) == 5) {
                return $matches[1] . '(' . $this->escape_column($matches[2]) . ') AS ' . $this->escape_column($matches[2]);
            }
        }

        // This matches any modifiers we support to SELECT.
        if (!preg_match('/\b(?:rand|all|distinct(?:row)?|high_priority|sql_(?:small_result|b(?:ig_result|uffer_result)|no_cache|ca(?:che|lc_found_rows)))\s/i', $column)) {
            if (stripos($column, ' AS ') !== FALSE) {
                // Force 'AS' to uppercase
                $column = str_ireplace(' AS ', ' AS ', $column);

                // Runs escape_column on both sides of an AS statement
                $column = array_map(array($this, __FUNCTION__), explode(' AS ', $column));

                // Re-create the AS statement
                return implode(' AS ', $column);
            }

            return preg_replace('/[^.*]+/', '[$0]', $column);
        }

        $parts = explode(' ', $column);
        $column = '';

        for ($i = 0, $c = count($parts); $i < $c; $i++) {
            // The column is always last
            if ($i == ($c - 1)) {
                $column .= preg_replace('/[^.*]+/', '[$0]', $parts[$i]);
            } else { // otherwise, it's a modifier
                $column .= $parts[$i] . ' ';
            }
        }
        return $column;
    }

    /**
     * Limit in SQL Server 2000 only uses the keyword
     * 'TOP'; 2007 may have an offset keyword, but
     * I am unsure - for pagination style limit,offset
     * functionality, a fancy query needs to be built.
     *
     * @param unknown_type $limit
     * @return unknown
     */
    public function limit($limit, $offset = null) {
        return 'TOP ' . $limit;
    }

    public function compile_select($database) {
        $sql = ($database['distinct'] == TRUE) ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= (count($database['select']) > 0) ? implode(', ', $database['select']) : '*';

        if (count($database['from']) > 0) {
            // Escape the tables
            $froms = array();
            foreach ($database['from'] as $from)
                $froms[] = $this->escape_column($from);
            $sql .= "\nFROM ";
            $sql .= implode(', ', $froms);
        }

        if (count($database['join']) > 0) {
            foreach ($database['join'] AS $join) {
                $sql .= "\n" . $join['type'] . 'JOIN ' . implode(', ', $join['tables']) . ' ON ' . $join['conditions'];
            }
        }

        if (count($database['where']) > 0) {
            $sql .= "\nWHERE ";
        }

        $sql .= implode("\n", $database['where']);

        if (count($database['groupby']) > 0) {
            $sql .= "\nGROUP BY ";
            $sql .= implode(', ', $database['groupby']);
        }

        if (count($database['having']) > 0) {
            $sql .= "\nHAVING ";
            $sql .= implode("\n", $database['having']);
        }

        if (count($database['orderby']) > 0) {
            $sql .= "\nORDER BY ";
            $sql .= implode(', ', $database['orderby']);
        }

        if (is_numeric($database['limit'])) {
            $sql .= "\n";
            $sql .= $this->limit($database['limit']);
        }

        return $sql;
    }

    public function escape_str($str) {
        if (!$this->db_config['escape'])
            return $str;

        is_resource($this->link) or $this->connect();
        //odbc_real_escape_string($str, $this->link); <-- this function doesn't exist

        $characters = array('/\x00/', '/\x1a/', '/\n/', '/\r/', '/\\\/', '/\'/');
        $replace = array('\\\x00', '\\x1a', '\\n', '\\r', '\\\\', "''");
        return preg_replace($characters, $replace, $str);
    }

    public function list_tables() {
        $sql = 'SHOW TABLES FROM `' . $this->db_config['connection']['database'] . '`';
        $result = $this->query($sql)->result(FALSE, false);

        $retval = array();
        foreach ($result as $row) {
            $retval[] = current($row);
        }

        return $retval;
    }

    public function show_error() {
        return odbc_get_last_message($this->link);
    }

    public function list_fields($table) {
        $result = array();

        foreach ($this->field_data($table) as $row) {
            // Make an associative array
            $result[$row->Field] = $this->sql_type($row->Type);
        }

        return $result;
    }

    public function field_data($table) {
        $query = $this->query("SELECT COLUMN_NAME AS Field, DATA_TYPE as Type  FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '" . $this->escape_table($table) . "'", $this->link);

        return $query->result_array(TRUE);
    }

}

/**
 * ODBC Result
 */
class COdbc_Result extends CDatabase_Result {

    // Fetch function and return type
    protected $fetch_type = 'odbc_fetch_object';
    protected $return_type = false;
    protected $link = null;
    protected $sql = null;

    /**
     * Sets up the result variables.
     *
     * @param  resource  query result
     * @param  resource  database link
     * @param  boolean   return objects or arrays
     * @param  string    SQL query that was run
     */
    public function __construct($result, $link, $object = TRUE, $sql) {
        $this->result = $result;
        $this->link = $link;
        $this->sql = $sql;
        // If the query is a resource, it was a SELECT, SHOW, DESCRIBE, EXPLAIN query
        if (is_resource($result)) {
            $this->current_row = 0;
            $this->total_rows = $this->codbc_num_rows($this->result);

            $this->fetch_type = ($object === TRUE) ? 'odbc_fetch_object' : 'odbc_fetch_array';
        } elseif (is_bool($result)) {
            if ($result == FALSE) {
                // SQL error
                throw new CDatabase_Exception('There was an SQL error: :error', array(':error'=>odbc_get_last_message($link) . ' - ' . $sql));
            } else {
                // Its an DELETE, INSERT, REPLACE, or UPDATE query
                # NOTE: Cannot retrieve these in ODBC.
                #$this->insert_id  = mysql_insert_id($link);
                #$this->total_rows = mysql_affected_rows($link);
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

    public function result($object = TRUE, $type = false) {
        $this->fetch_type = ((bool) $object) ? 'odbc_fetch_object' : 'odbc_fetch_array';

        // This check has to be outside the previous statement, because we do not
        // know the state of fetch_type when $object = NULL
        // NOTE - The class set by $type must be defined before fetching the result,
        // autoloading is disabled to save a lot of stupid overhead.
        if ($this->fetch_type == 'odbc_fetch_object') {
            $this->return_type = (is_string($type) AND CF::auto_load($type)) ? $type : 'stdClass';
        } else {
            $this->return_type = $type;
        }

        return $this;
    }

    public function as_array($object = NULL, $type = false) {
        return $this->result_array($object, $type);
    }

    public function result_array($object = NULL, $type = false) {
        $rows = array();

        if (is_string($object)) {
            $fetch = $object;
        } elseif (is_bool($object)) {
            if ($object === TRUE) {
                $fetch = 'odbc_fetch_object';

                // NOTE - The class set by $type must be defined before fetching the result,
                // autoloading is disabled to save a lot of stupid overhead.
                $type = class_exists($type, FALSE) ? $type : 'stdClass';
            } else {
                $fetch = 'odbc_fetch_array';
            }
        } else {
            // Use the default config values
            $fetch = $this->fetch_type;

            if ($fetch == 'odbc_fetch_object') {
                $type = class_exists($type, FALSE) ? $type : 'stdClass';
            }
        }

        if ($this->codbc_num_rows($this->result)) {
            // Reset the pointer location to make sure things work properly
            #mysql_data_seek($this->result, 0);

            while ($row = $fetch($this->result, $type)) {

                $rows[] = $row;
            }
        }

        return isset($rows) ? $rows : array();
    }

    public function list_fields() {
        $field_names = array();
        while ($field = odbc_fetch_field($this->result)) {
            $field_names[] = $field->name;
        }

        return $field_names;
    }

    public function seek($offset) {
        return false;
        if (!$this->offsetExists($offset))
            return FALSE;

        return odbc_data_seek($this->result, $offset);
    }

    function codbc_num_rows($r1) {

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
     * @return  integer
     */
    public function count() {
        return $this->total_rows;
    }

    // End Interface
    // Interface: ArrayAccess
    /**
     * Determines if the requested offset of the result set exists.
     *
     * @param   integer  offset id
     * @return  boolean
     */
    public function offsetExists($offset) {
        if ($this->total_rows > 0) {
            $min = 0;
            $max = $this->total_rows - 1;

            return ($offset < $min OR $offset > $max) ? FALSE : TRUE;
        }

        return FALSE;
    }

    /**
     * Retreives the requested query result offset.
     *
     * @param   integer  offset id
     * @return  mixed
     */
    public function offsetGet($offset) {
        return false;
    }

    /**
     * Sets the offset with the provided value. Since you can't modify query result sets, this function just throws an exception.
     *
     * @param   integer  offset id
     * @param   integer  value
     * @throws  Kohana_Database_Exception
     */
    public function offsetSet($offset, $value) {
        throw new Kohana_Database_Exception('Query results are read only');
    }

    /**
     * Unsets the offset. Since you can't modify query result sets, this function just throws an exception.
     *
     * @param   integer  offset id
     * @throws  Kohana_Database_Exception
     */
    public function offsetUnset($offset) {
        throw new Kohana_Database_Exception('Query results are read only');
    }

    // End Interface
    // Interface: Iterator
    /**
     * Retrieves the current result set row.
     *
     * @return  mixed
     */
    public function current() {
        return $this->offsetGet($this->current_row);
    }

    /**
     * Retreives the current row id.
     *
     * @return  integer
     */
    public function key() {
        return $this->current_row;
    }

    /**
     * Moves the result pointer ahead one step.
     *
     * @return  integer
     */
    public function next() {
        return ++$this->current_row;
    }

    /**
     * Moves the result pointer back one step.
     *
     * @return  integer
     */
    public function prev() {
        return --$this->current_row;
    }

    /**
     * Moves the result pointer to the beginning of the result set.
     *
     * @return  integer
     */
    public function rewind() {
        return $this->current_row = 0;
    }

    /**
     * Determines if the current result pointer is valid.
     *
     * @return  boolean
     */
    public function valid() {
        return $this->offsetExists($this->current_row);
    }

    // End Interface
}

// End odbc_Result Class
