<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * MySQLi Database Driver.
 */
class CDatabase_Driver_Sqlsrv extends CDatabase_Driver {
    /**
     * Database connection link.
     *
     * @var resource
     */
    protected $link;

    protected $dbConfig;

    protected $statements = [];

    /**
     * Sets the config for the class.
     *
     * @param  array  database configuration
     * @param mixed $config
     */
    public function __construct(CDatabase $db, $config) {
        $this->db = $db;
        $this->dbConfig = $config;

        CF::log(CLogger::DEBUG, 'MySQLi Database Driver Initialized');
    }

    public function close() {
        if ($this->link) {
            sqlsrv_close($this->link);
        }
        $this->link = null;
    }

    /**
     * Closes the database connection.
     */
    public function __destruct() {
        // try {
        //     is_object($this->link) && @$this->link->close();
        // } catch (Exception $ex) {
        //     //do nothing
        // }
    }

    public function connect() {
        // Check if link already exists

        if (is_object($this->link)) {
            return $this->link;
        }

        // Import the connect variables
        extract($this->dbConfig['connection']);

        // Build the connection info
        $host = isset($host) ? $host : $socket;

        try {
            $connectionInfo = [
                'Database' => $database,
                'UID' => $user,
                'PWD' => $pass
            ];

            if ($charset = $this->dbConfig['character_set']) {
                $connectionInfo['CharacterSet'] = $charset;
            }

            $hostPort = $host . ',' . $port;
            if ($this->link = sqlsrv_connect($hostPort, $connectionInfo)) {
                //if ($this->link = new PDO('sqlsrv:Server=' . $hostPort . ';Database=' . $database, $user, $pass)) {
                // Clear password after successful connect
                $this->dbConfig['connection']['pass'] = null;

                return $this->link;
            }
        } catch (Exception $ex) {
            throw new CDatabase_Exception($ex->getMessage() . ', Host:' . $host);
        }

        return false;
    }

    public function reconnect() {
        if (!$this->link) {
            return $this->connect();
        }
    }

    public function query($sql) {
        $this->link or $this->reconnect();
        // Only cache if it's turned on, and only cache if it's not a write statement
        if ($this->dbConfig['cache'] and !preg_match('#\b(?:INSERT|UPDATE|REPLACE|SET|DELETE|TRUNCATE)\b#i', $sql)) {
            $hash = $this->queryHash($sql);

            if (!isset($this->query_cache[$hash])) {
                // Set the cached object
                $this->query_cache[$hash] = new CDatabase_Driver_Sqlsrv_Result($this->link, $this->dbConfig['object'], $sql);
            } else {
                // Rewind cached result
                $this->query_cache[$hash]->rewind();
            }

            // Return the cached query
            return $this->query_cache[$hash];
        }

        return new CDatabase_Driver_Sqlsrv_Result($this->link, $this->dbConfig['object'], $sql);
    }

    public function escapeTable($table) {
        if (!$this->dbConfig['escape']) {
            return $table;
        }

        if (stripos($table, ' AS ') !== false) {
            // Force 'AS' to uppercase
            $table = str_ireplace(' AS ', ' AS ', $table);

            // Runs escape_table on both sides of an AS statement
            $table = array_map([$this, __FUNCTION__], explode(' AS ', $table));

            // Re-create the AS statement
            return implode(' AS ', $table);
        }

        return '`' . str_replace('.', '`.`', $table) . '`';
    }

    public function escapeColumn($column) {
        if (!$this->dbConfig['escape']) {
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

            return preg_replace('/[^.*]+/', '`$0`', $column);
        }

        $parts = explode(' ', $column);
        $column = '';

        for ($i = 0, $c = count($parts); $i < $c; $i++) {
            // The column is always last
            if ($i == ($c - 1)) {
                $column .= preg_replace('/[^.*]+/', '`$0`', $parts[$i]);
            } else { // otherwise, it's a modifier
                $column .= $parts[$i] . ' ';
            }
        }

        return $column;
    }

    public function regex($field, $match, $type, $num_regexs) {
        $prefix = ($num_regexs == 0) ? '' : $type;

        return $prefix . ' ' . $this->escapeColumn($field) . ' REGEXP \'' . $this->escapeStr($match) . '\'';
    }

    public function notregex($field, $match, $type, $num_regexs) {
        $prefix = $num_regexs == 0 ? '' : $type;

        return $prefix . ' ' . $this->escapeColumn($field) . ' NOT REGEXP \'' . $this->escapeStr($match) . '\'';
    }

    public function merge($table, $keys, $values) {
        // Escape the column names
        foreach ($keys as $key => $value) {
            $keys[$key] = $this->escapeColumn($value);
        }

        return 'REPLACE INTO ' . $this->escapeTable($table) . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
    }

    public function limit($limit, $offset = 0) {
        return 'LIMIT ' . $offset . ', ' . $limit;
    }

    public function escapeStr($str) {
        if (!$this->dbConfig['escape']) {
            return $str;
        }
        if (is_numeric($str)) {
            return $str;
        }
        $unpacked = unpack('H*hex', $str);

        return '0x' . $unpacked['hex'];
    }

    public function listTables() {
        $tables = [];
        $sql = "SELECT * FROM [SYSOBJECTS] WHERE xtype = 'U' order by name asc";
        if ($r = $this->query($sql)) {
            foreach ($r as $row) {
                $tables = $row->name;
            }
        }

        return $tables;
    }

    public function showError() {
        $errors = sqlsrv_errors();

        return carr::get($errors, '0.message');
    }

    public function listFields($table) {
        $result = null;

        foreach ($this->fieldData($table) as $row) {
            // Make an associative array
            $result[$row->Field] = $this->sqlType($row->Type);

            if ($row->Key === 'PRI' and $row->Extra === 'auto_increment') {
                // For sequenced (AUTO_INCREMENT) tables
                $result[$row->Field]['sequenced'] = true;
            }

            if ($row->Null === 'YES') {
                // Set NULL status
                $result[$row->Field]['null'] = true;
            }
        }

        if (!isset($result)) {
            throw new CDatabase_Exception('Table :table does not exist in your database', [':table' => $table]);
        }

        return $result;
    }

    public function fieldData($table) {
        $result = $this->query('SHOW COLUMNS FROM ' . $this->escapeTable($table));

        return $result->resultArray(true);
    }

    /**
     * @inheritdoc
     *
     * @return CDatabase_Driver_Mysqli_MySqlSchemaManager
     */
    public function getSchemaManager(CDatabase $db) {
        return new CDatabase_Schema_Manager_Mysql($db);
    }

    public function getServerVersion() {
        if (!$this->link) {
            $this->connect();
        }
        $serverInfo = sqlsrv_server_info($this->link);

        return $serverInfo['SQLServerVersion'];
    }

    public function requiresQueryForServerVersion() {
        return false;
    }

    public function ping() {
        if (!$this->link) {
            $this->connect();
        }

        return mysqli_ping($this->link);
    }
}

// End Database_Mysqli_Driver Class
