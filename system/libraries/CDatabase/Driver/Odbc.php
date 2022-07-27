<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * ODBC Database Driver.
 */
class CDatabase_Driver_Odbc extends CDatabase_Driver {
    /**
     * Database connection link.
     */
    protected $link;

    /**
     * Database configuration.
     */
    protected $db_config;

    /**
     * Sets the config for the class.
     *
     * @param array $config database configuration
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
     * Make the connection.
     *
     * @return return connection
     */
    public function connect() {
        // Check if link already exists
        if (is_resource($this->link)) {
            return $this->link;
        }

        // Import the connect variables
        extract($this->db_config['connection']);

        // Persistent connections enabled?
        $connect = ($this->db_config['persistent'] == true) ? 'odbc_pconnect' : 'odbc_connect';

        // Build the connection info
        $host = isset($host) ? $host : $socket;

        // Windows uses a comma instead of a colon
        $port = (isset($port) and is_string($port)) ? (CServer::getOS() == CServer::OS_WINNT ? ',' : ':') . $port : '';

        // Make the connection and select the database
        if (($this->link = $connect($database, $user, $pass))) {
            if ($charset = $this->db_config['character_set']) {
                $this->setCharset($charset);
            }

            // Clear password after successful connect
            $this->config['connection']['pass'] = null;

            return $this->link;
        }

        return false;
    }

    public function query($sql) {
        if ($this->db_config['cache']
            && !preg_match('#\b(?:INSERT|UPDATE|REPLACE|SET)\b#i', $sql)
        ) {
            $hash = $this->queryHash($sql);

            if (!isset(self::$query_cache[$hash])) {
                // Set the cached object
                self::$query_cache[$hash] = new CDatabase_Driver_Odbc_Result(odbc_exec($this->link, $sql), $this->link, $this->db_config['object'], $sql);
            }

            // Return the cached query
            return self::$query_cache[$hash];
        }

        return new CDatabase_Driver_Odbc_Result(odbc_exec($this->link, $sql), $this->link, $this->db_config['object'], $sql);
    }

    public function setCharset($charset) {
        // TODO: Add this functionality.
        //$this->query('SET NAMES '.$this->escape_str($charset));
    }

    public function escapeTable($table) {
        if (stripos($table, ' AS ') !== false) {
            // Force 'AS' to uppercase
            $table = str_ireplace(' AS ', ' AS ', $table);

            // Runs escape_table on both sides of an AS statement
            $table = array_map([$this, __FUNCTION__], explode(' AS ', $table));

            // Re-create the AS statement
            return implode(' AS ', $table);
        }

        return '[' . str_replace('.', '[.]', $table) . ']';
    }

    public function escapeColumn($column) {
        if (!$this->db_config['escape']) {
            return $column;
        }

        if ($column == '*') {
            return $column;
        }

        // This matches any functions we support to SELECT.
        if (preg_match('/(avg|count|sum|max|min)\(\s*(.*)\s*\)(\s*as\s*(.+)?)?/i', $column, $matches)) {
            if (count($matches) == 3) {
                return $matches[1] . '(' . $this->escapeColumn($matches[2]) . ')';
            } elseif (count($matches) == 5) {
                return $matches[1] . '(' . $this->escapeColumn($matches[2]) . ') AS ' . $this->escapeColumn($matches[2]);
            }
        }

        // This matches any modifiers we support to SELECT.
        if (!preg_match('/\b(?:rand|all|distinct(?:row)?|high_priority|sql_(?:small_result|b(?:ig_result|uffer_result)|no_cache|ca(?:che|lc_found_rows)))\s/i', $column)) {
            if (stripos($column, ' AS ') !== false) {
                // Force 'AS' to uppercase
                $column = str_ireplace(' AS ', ' AS ', $column);

                // Runs escape_column on both sides of an AS statement
                $column = array_map([$this, __FUNCTION__], explode(' AS ', $column));

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
     * @param null|mixed   $offset
     *
     * @return unknown
     */
    public function limit($limit, $offset = null) {
        return 'TOP ' . $limit;
    }

    public function escapeStr($str) {
        if (!$this->db_config['escape']) {
            return $str;
        }

        is_resource($this->link) or $this->connect();
        //odbc_real_escape_string($str, $this->link); <-- this function doesn't exist

        $characters = ['/\x00/', '/\x1a/', '/\n/', '/\r/', '/\\\/', '/\'/'];
        $replace = ['\\\x00', '\\x1a', '\\n', '\\r', '\\\\', "''"];

        return preg_replace($characters, $replace, $str);
    }

    public function listTables() {
        $sql = 'SHOW TABLES FROM `' . $this->db_config['connection']['database'] . '`';
        $result = $this->query($sql)->result(false, false);

        $retval = [];
        foreach ($result as $row) {
            $retval[] = current($row);
        }

        return $retval;
    }

    public function showError() {
        return odbc_errormsg($this->link);
    }

    public function close() {
        return odbc_close($this->link);
    }

    public function listFields($table) {
        $result = [];

        foreach ($this->fieldData($table) as $row) {
            // Make an associative array
            $result[$row->Field] = $this->sqlType($row->Type);
        }

        return $result;
    }

    public function fieldData($table) {
        $query = $this->query("SELECT COLUMN_NAME AS Field, DATA_TYPE as Type  FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '" . $this->escapeTable($table) . "'", $this->link);

        return $query->resultArray(true);
    }
}
