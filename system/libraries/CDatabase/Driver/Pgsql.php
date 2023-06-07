<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * PostgreSQL 8.1+ Database Driver
 */
class CDatabase_Driver_Pgsql extends CDatabase_Driver {
    use CTrait_Compat_Database_Driver_Pgsql;

    // Database connection link
    protected $link;

    protected $db_config;

    /**
     * Sets the config for the class.
     *
     * @param array $config database configuration
     */
    public function __construct($config) {
        $this->db_config = $config;

        CF::log(CLogger::DEBUG, 'PgSQL Database Driver Initialized');
    }

    public function connect() {
        // Check if link already exists
        if (is_resource($this->link)) {
            return $this->link;
        }

        // Import the connect variables
        extract($this->db_config['connection']);

        // Persistent connections enabled?
        $connect = ($this->db_config['persistent'] == true) ? 'pg_pconnect' : 'pg_connect';

        // Build the connection info
        $port = isset($port) ? 'port=\'' . $port . '\'' : '';
        $host = isset($host) ? 'host=\'' . $host . '\' ' . $port : ''; // if no host, connect with the socket

        $connection_string = $host . ' dbname=\'' . $database . '\' user=\'' . $user . '\' password=\'' . $pass . '\'';
        // Make the connection and select the database
        if ($this->link = $connect($connection_string)) {
            if ($charset = $this->db_config['character_set']) {
                echo $this->setCharset($charset);
            }

            // Clear password after successful connect
            $this->db_config['connection']['pass'] = null;

            return $this->link;
        }

        return false;
    }

    public function query($sql) {
        // Only cache if it's turned on, and only cache if it's not a write statement
        if ($this->db_config['cache'] and !preg_match('#\b(?:INSERT|UPDATE|SET)\b#i', $sql)) {
            $hash = $this->queryHash($sql);

            if (!isset($this->query_cache[$hash])) {
                // Set the cached object
                $this->query_cache[$hash] = new CDatabase_Driver_Pgsql_Result(pg_query($this->link, $sql), $this->link, $this->db_config['object'], $sql);
            } else {
                // Rewind cached result
                $this->query_cache[$hash]->rewind();
            }

            return $this->query_cache[$hash];
        }

        // Suppress warning triggered when a database error occurs (e.g., a constraint violation)
        return new CDatabase_Driver_Pgsql_Result(@pg_query($this->link, $sql), $this->link, $this->db_config['object'], $sql);
    }

    public function setCharset($charset) {
        $this->query('SET client_encoding TO ' . pg_escape_string($this->link, $charset));
    }

    public function escapeTable($table) {
        if (!$this->db_config['escape']) {
            return $table;
        }

        return '"' . str_replace('.', '"."', $table) . '"';
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
        if (!preg_match('/\b(?:all|distinct)\s/i', $column)) {
            if (stripos($column, ' AS ') !== false) {
                // Force 'AS' to uppercase
                $column = str_ireplace(' AS ', ' AS ', $column);

                // Runs escape_column on both sides of an AS statement
                $column = array_map([$this, __FUNCTION__], explode(' AS ', $column));

                // Re-create the AS statement
                return implode(' AS ', $column);
            }

            return preg_replace('/[^.*]+/', '"$0"', $column);
        }

        $parts = explode(' ', $column);
        $column = '';

        for ($i = 0, $c = count($parts); $i < $c; $i++) {
            // The column is always last
            if ($i == ($c - 1)) {
                $column .= preg_replace('/[^.*]+/', '"$0"', $parts[$i]);
            } else { // otherwise, it's a modifier
                $column .= $parts[$i] . ' ';
            }
        }
        return $column;
    }

    public function regex($field, $match, $type, $num_regexs) {
        $prefix = ($num_regexs == 0) ? '' : $type;

        return $prefix . ' ' . $this->escapeColumn($field) . ' ~* \'' . $this->escapeStr($match) . '\'';
    }

    public function notregex($field, $match, $type, $num_regexs) {
        $prefix = $num_regexs == 0 ? '' : $type;

        return $prefix . ' ' . $this->escapeColumn($field) . ' !~* \'' . $this->escapeStr($match) . '\'';
    }

    public function limit($limit, $offset = 0) {
        return 'LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    public function escapeStr($str) {
        if (!$this->db_config['escape']) {
            return $str;
        }

        is_resource($this->link) or $this->connect();

        return pg_escape_string($this->link, $str);
    }

    public function listTables() {
        $sql = 'SELECT table_schema || \'.\' || table_name FROM information_schema.tables WHERE table_schema NOT IN (\'pg_catalog\', \'information_schema\')';
        $result = $this->query($sql)->result(false, PGSQL_ASSOC);

        $retval = [];
        foreach ($result as $row) {
            $retval[] = current($row);
        }

        return $retval;
    }

    public function showError() {
        return pg_last_error($this->link);
    }

    public function listFields($table) {
        $result = null;

        foreach ($this->fieldData($table) as $row) {
            // Make an associative array
            $result[$row->column_name] = $this->sqlType($row->data_type);

            if (!strncmp($row->column_default, 'nextval(', 8)) {
                $result[$row->column_name]['sequenced'] = true;
            }

            if ($row->is_nullable === 'YES') {
                $result[$row->column_name]['null'] = true;
            }
        }

        if (!isset($result)) {
            throw CDatabase_Exception::tableNotFound($table);
        }

        return $result;
    }

    public function fieldData($table) {
        // http://www.postgresql.org/docs/8.3/static/infoschema-columns.html
        $result = $this->query('
			SELECT column_name, column_default, is_nullable, data_type, udt_name,
				character_maximum_length, numeric_precision, numeric_precision_radix, numeric_scale
			FROM information_schema.columns
			WHERE table_name = \'' . $this->escapeStr($table) . '\'
			ORDER BY ordinal_position
		');

        return $result->resultArray(true);
    }

    public function close() {
    }
}
