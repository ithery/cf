<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * MySQLi Database Driver
 */
class CDatabase_Driver_Mysqli extends CDatabase_Driver_AbstractMysql {

    // Database connection link
    protected $link;
    protected $dbConfig;
    protected $statements = array();

    /**
     * Sets the config for the class.
     *
     * @param  array  database configuration
     */
    public function __construct(CDatabase $db,$config) {
        $this->db = $db;
        $this->dbConfig = $config;

        CF::log(CLogger::DEBUG, 'MySQLi Database Driver Initialized');
    }

    public function close() {
        is_object($this->link) && @$this->link->close();
    }

    /**
     * Closes the database connection.
     */
    public function __destruct() {
//        try {
//            is_object($this->link) && @$this->link->close();
//        } catch (Exception $ex) {
//            //do nothing
//        }
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
            // Make the connection and select the database
            if ($this->link = new mysqli($host, $user, $pass, $database, $port)) {
                if ($charset = $this->dbConfig['character_set']) {
                    $this->set_charset($charset);
                }

                // Clear password after successful connect
                $this->dbConfig['connection']['pass'] = NULL;

                return $this->link;
            }
        } catch (Exception $ex) {
            throw new CDatabase_Exception($ex->getMessage() . ', Host:' . $host);
        }

        return FALSE;
    }

    public function reconnect() {
        if(!$this->link) {
            return $this->connect();
        }
        if(!mysqli_ping($this->link)) {
            $this->close();
            $This->connect();
        }
    }
    
    public function query($sql) {
        $this->link or $this->reconnect();
        // Only cache if it's turned on, and only cache if it's not a write statement
        if ($this->dbConfig['cache'] AND ! preg_match('#\b(?:INSERT|UPDATE|REPLACE|SET|DELETE|TRUNCATE)\b#i', $sql)) {
            $hash = $this->query_hash($sql);

            if (!isset($this->query_cache[$hash])) {
                // Set the cached object
                $this->query_cache[$hash] = new CDatabase_Driver_Mysqli_Result($this->link, $this->dbConfig['object'], $sql);
            } else {
                // Rewind cached result
                $this->query_cache[$hash]->rewind();
            }

            // Return the cached query
            return $this->query_cache[$hash];
        }

        return new CDatabase_Driver_Mysqli_Result($this->link, $this->dbConfig['object'], $sql);
    }

    public function set_charset($charset) {
        if ($this->link->set_charset($charset) === FALSE) {
            throw new CDatabase_Exception('There was an SQL error: :error', array(':error' => $this->show_error()));
        }
    }

    public function escape_table($table) {
        if (!$this->dbConfig['escape'])
            return $table;

        if (stripos($table, ' AS ') !== FALSE) {
            // Force 'AS' to uppercase
            $table = str_ireplace(' AS ', ' AS ', $table);

            // Runs escape_table on both sides of an AS statement
            $table = array_map(array($this, __FUNCTION__), explode(' AS ', $table));

            // Re-create the AS statement
            return implode(' AS ', $table);
        }
        return '`' . str_replace('.', '`.`', $table) . '`';
    }

    public function escape_column($column) {
        if (!$this->dbConfig['escape'])
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

        return $prefix . ' ' . $this->escape_column($field) . ' REGEXP \'' . $this->escape_str($match) . '\'';
    }

    public function notregex($field, $match, $type, $num_regexs) {
        $prefix = $num_regexs == 0 ? '' : $type;

        return $prefix . ' ' . $this->escape_column($field) . ' NOT REGEXP \'' . $this->escape_str($match) . '\'';
    }

    public function merge($table, $keys, $values) {
        // Escape the column names
        foreach ($keys as $key => $value) {
            $keys[$key] = $this->escape_column($value);
        }
        return 'REPLACE INTO ' . $this->escape_table($table) . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
    }

    public function limit($limit, $offset = 0) {
        return 'LIMIT ' . $offset . ', ' . $limit;
    }

    public function compile_select($database) {
        $sql = ($database['distinct'] == TRUE) ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= (count($database['select']) > 0) ? implode(', ', $database['select']) : '*';

        if (count($database['from']) > 0) {
            // Escape the tables
            $froms = array();
            foreach ($database['from'] as $from) {
                $froms[] = $this->escape_column($from);
            }
            $sql .= "\nFROM (";
            $sql .= implode(', ', $froms) . ")";
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
            $sql .= $this->limit($database['limit'], $database['offset']);
        }

        return $sql;
    }

    public function escape_str($str) {
        if (!$this->dbConfig['escape'])
            return $str;

        is_object($this->link) or $this->connect();

        return $this->link->real_escape_string($str);
    }

    public function list_tables() {
        $tables = array();

        if ($query = $this->query('SHOW TABLES FROM ' . $this->escape_table($this->dbConfig['connection']['database']))) {
            foreach ($query->result(FALSE) as $row) {
                $tables[] = current($row);
            }
        }

        return $tables;
    }

    public function show_error() {
        return $this->link->error;
    }

    public function list_fields($table) {
        $result = NULL;

        foreach ($this->field_data($table) as $row) {
            // Make an associative array
            $result[$row->Field] = $this->sql_type($row->Type);

            if ($row->Key === 'PRI' AND $row->Extra === 'auto_increment') {
                // For sequenced (AUTO_INCREMENT) tables
                $result[$row->Field]['sequenced'] = TRUE;
            }

            if ($row->Null === 'YES') {
                // Set NULL status
                $result[$row->Field]['null'] = TRUE;
            }
        }

        if (!isset($result))
            throw new CDatabase_Exception('Table :table does not exist in your database', array(':table' => $table));

        return $result;
    }

    public function field_data($table) {
        $result = $this->query('SHOW COLUMNS FROM ' . $this->escape_table($table));

        return $result->result_array(TRUE);
    }

    /**
     * {@inheritdoc}
     * @return CDatabase_Driver_Mysqli_MySqlSchemaManager
     */
    public function getSchemaManager(CDatabase $db) {
        return new CDatabase_Schema_Manager_Mysql($db);
    }

    /**
     * {@inheritdoc}
     *
     * The server version detection includes a special case for MariaDB
     * to support '5.5.5-' prefixed versions introduced in Maria 10+
     * @link https://jira.mariadb.org/browse/MDEV-4088
     */
    public function getServerVersion() {
        if (!$this->link) {
            $this->connect();
        }
        $serverInfos = $this->link->get_server_info();
        if (false !== stripos($serverInfos, 'mariadb')) {
            return $serverInfos;
        }

        $majorVersion = floor($this->link->server_version / 10000);
        $minorVersion = floor(($this->link->server_version - $majorVersion * 10000) / 100);
        $patchVersion = floor($this->link->server_version - $majorVersion * 10000 - $minorVersion * 100);

        return $majorVersion . '.' . $minorVersion . '.' . $patchVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresQueryForServerVersion() {
        return false;
    }

}

// End Database_Mysqli_Driver Class
