<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Database API driver.
 */
abstract class CDatabase_Driver {
    use CTrait_Compat_Database_Driver;

    protected $query_cache;

    /**
     * @var CDatabase
     */
    protected $db;

    /**
     * Connect to our database.
     * Returns FALSE on failure or a MySQL resource.
     *
     * @return mixed
     */
    abstract public function connect();

    /**
     * Perform a query based on a manually written query.
     *
     * @param string $sql SQL query to execute
     *
     * @return CDatabase_Result
     */
    abstract public function query($sql);

    /**
     * Closing connection.
     *
     * @return void
     */
    abstract public function close();

    /**
     * Builds a DELETE query.
     *
     * @param string $table table name
     * @param array  $where where clause
     *
     * @return string
     */
    public function delete($table, $where) {
        return 'DELETE FROM ' . $this->escapeTable($table) . ' WHERE ' . implode(' ', $where);
    }

    /**
     * Builds an UPDATE query.
     *
     * @param string $table  table name
     * @param array  $values key => value pairs
     * @param array  $where  where clause
     *
     * @return string
     */
    public function update($table, $values, $where) {
        foreach ($values as $key => $val) {
            $valstr[] = $this->escapeColumn($key) . ' = ' . $val;
        }

        return 'UPDATE ' . $this->escapeTable($table) . ' SET ' . implode(', ', $valstr) . ' WHERE ' . implode(' ', $where);
    }

    /**
     * Set the charset using 'SET NAMES <charset>'.
     *
     * @param string $charset character set to use
     */
    public function setCharset($charset) {
        throw new CDatabase_Exception('The method you called, :method, is not supported by this driver', [':method', __FUNCTION__]);
    }

    /**
     * Wrap the tablename in backticks, has support for: table.field syntax.
     *
     * @param string $table table name
     *
     * @return string
     */
    abstract public function escapeTable($table);

    /**
     * Escape a column/field name, has support for special commands.
     *
     * @param string $column column name
     *
     * @return string
     */
    abstract public function escapeColumn($column);

    /**
     * Builds a WHERE portion of a query.
     *
     * @param mixed  $key        key
     * @param string $value      value
     * @param string $type       type
     * @param int    $num_wheres number of where clauses
     * @param bool   $quote      escape the value
     *
     * @return string
     */
    public function where($key, $value, $type, $num_wheres, $quote) {
        $prefix = ($num_wheres == 0) ? '' : $type;

        if ($quote === -1) {
            $value = '';
        } else {
            if ($value === null) {
                if (!$this->hasOperator($key)) {
                    $key .= ' IS';
                }

                $value = ' NULL';
            } elseif (is_bool($value)) {
                if (!$this->hasOperator($key)) {
                    $key .= ' =';
                }

                $value = ($value == true) ? ' 1' : ' 0';
            } else {
                if (!$this->hasOperator($key) and !empty($key)) {
                    $key = $this->escapeColumn($key) . ' =';
                } else {
                    preg_match('/^(.+?)([<>!=]+|\bIS(?:\s+NULL))\s*$/i', $key, $matches);
                    if (isset($matches[1]) and isset($matches[2])) {
                        $key = $this->escapeColumn(trim($matches[1])) . ' ' . trim($matches[2]);
                    }
                }

                $value = ' ' . (($quote == true) ? $this->escape($value) : $value);
            }
        }

        return $prefix . $key . $value;
    }

    /**
     * Builds a LIKE portion of a query.
     *
     * @param mixed  $field     field name
     * @param string $match     value to match with field
     * @param bool   $auto      add wildcards before and after the match
     * @param string $type      clause type (AND or OR)
     * @param int    $num_likes number of likes
     *
     * @return string
     */
    public function like($field, $match, $auto, $type, $num_likes) {
        $prefix = ($num_likes == 0) ? '' : $type;

        $match = $this->escapeStr($match);

        if ($auto === true) {
            // Add the start and end quotes
            $match = '%' . str_replace('%', '\\%', $match) . '%';
        }

        return $prefix . ' ' . $this->escapeColumn($field) . ' LIKE \'' . $match . '\'';
    }

    /**
     * Builds a NOT LIKE portion of a query.
     *
     * @param mixed  $field     field name
     * @param string $match     value to match with field
     * @param mixed  $auto      add wildcards before and after the match
     * @param string $type      clause type (AND or OR)
     * @param int    $num_likes number of likes
     *
     * @return string
     */
    public function notlike($field, $match, $auto, $type, $num_likes) {
        $prefix = ($num_likes == 0) ? '' : $type;

        $match = $this->escapeStr($match);

        if ($auto === true) {
            // Add the start and end quotes
            $match = '%' . $match . '%';
        }

        return $prefix . ' ' . $this->escapeColumn($field) . ' NOT LIKE \'' . $match . '\'';
    }

    /**
     * Builds a REGEX portion of a query.
     *
     * @param string $field      field name
     * @param string $match      value to match with field
     * @param string $type       clause type (AND or OR)
     * @param int    $num_regexs number of regexes
     *
     * @return string
     */
    public function regex($field, $match, $type, $num_regexs) {
        throw new CDatabase_Exception('The method you called, :method, is not supported by this driver', [':method', __FUNCTION__]);
    }

    /**
     * Builds a NOT REGEX portion of a query.
     *
     * @param string $field      field name
     * @param string $match      value to match with field
     * @param string $type       clause type (AND or OR)
     * @param int    $num_regexs number of regexes
     *
     * @return string
     */
    public function notregex($field, $match, $type, $num_regexs) {
        throw new CDatabase_Exception('The method you called, :method, is not supported by this driver', [':method', __FUNCTION__]);
    }

    /**
     * Builds an INSERT query.
     *
     * @param string $table  table name
     * @param array  $keys   keys
     * @param array  $values values
     *
     * @return string
     */
    public function insert($table, $keys, $values) {
        // Escape the column names
        foreach ($keys as $key => $value) {
            $keys[$key] = $this->escapeColumn($value);
        }

        return 'INSERT INTO ' . $this->escapeTable($table) . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
    }

    /**
     * Builds a MERGE portion of a query.
     *
     * @param string $table  table name
     * @param array  $keys   keys
     * @param array  $values values
     *
     * @return string
     */
    public function merge($table, $keys, $values) {
        throw new CDatabase_Exception('The method you called, :method, is not supported by this driver', [':method', __FUNCTION__]);
    }

    /**
     * Builds a LIMIT portion of a query.
     *
     * @param int $limit  limit
     * @param int $offset offset
     *
     * @return string
     */
    abstract public function limit($limit, $offset = 0);

    /**
     * Creates a prepared statement.
     *
     * @param string $sql SQL query
     *
     * @return CDatabase_Stmt
     */
    public function stmtPrepare($sql = '') {
        throw new CDatabase_Exception('The method you called, :method, is not supported by this driver', [':method', __FUNCTION__]);
    }

    /**
     * Determines if the string has an arithmetic operator in it.
     *
     * @param string $str string to check
     *
     * @return bool
     */
    public function hasOperator($str) {
        return (bool) preg_match('/[<>!=]|\sIS(?:\s+NOT\s+)?\b|BETWEEN/i', trim($str));
    }

    /**
     * Escapes any input value.
     *
     * @param mixed $value value to escape
     *
     * @return string
     */
    public function escape($value) {
        if (!$this->dbConfig['escape']) {
            return $value;
        }

        switch (gettype($value)) {
            case 'string':
                $value = '\'' . $this->escapeStr($value) . '\'';

                break;
            case 'boolean':
                $value = (int) $value;

                break;
            case 'double':
                // Convert to non-locale aware float to prevent possible commas
                $value = sprintf('%F', $value);

                break;
            default:
                $value = ($value === null) ? 'NULL' : $value;

                break;
        }

        return (string) $value;
    }

    /**
     * Escapes a string for a query.
     *
     * @param mixed $str value to escape
     *
     * @return string
     */
    abstract public function escapeStr($str);

    /**
     * Lists all tables in the database.
     *
     * @return array
     */
    abstract public function listTables();

    /**
     * Lists all fields in a table.
     *
     * @param string $table table name
     *
     * @return array
     */
    abstract public function listFields($table);

    /**
     * Returns the last database error.
     *
     * @return string
     */
    abstract public function showError();

    /**
     * Returns field data about a table.
     *
     * @param string $table table name
     *
     * @return array
     */
    abstract public function fieldData($table);

    public static function getSqlTypeData() {
        static $sqlTypeData;
        if ($sqlTypeData === null) {
            $sqlTypeData = [
                'tinyint' => ['type' => 'int', 'max' => 127],
                'smallint' => ['type' => 'int', 'max' => 32767],
                'mediumint' => ['type' => 'int', 'max' => 8388607],
                'int' => ['type' => 'int', 'max' => 2147483647],
                'integer' => ['type' => 'int', 'max' => 2147483647],
                'bigint' => ['type' => 'int', 'max' => 9223372036854775807],
                'float' => ['type' => 'float'],
                'float unsigned' => ['type' => 'float', 'min' => 0],
                'boolean' => ['type' => 'boolean'],
                'time' => ['type' => 'string', 'format' => '00:00:00'],
                'time with time zone' => ['type' => 'string'],
                'date' => ['type' => 'string', 'format' => '0000-00-00'],
                'year' => ['type' => 'string', 'format' => '0000'],
                'datetime' => ['type' => 'string', 'format' => '0000-00-00 00:00:00'],
                'timestamp with time zone' => ['type' => 'string'],
                'char' => ['type' => 'string', 'exact' => true],
                'binary' => ['type' => 'string', 'binary' => true, 'exact' => true],
                'varchar' => ['type' => 'string'],
                'varbinary' => ['type' => 'string', 'binary' => true],
                'blob' => ['type' => 'string', 'binary' => true],
                'text' => ['type' => 'string']
            ];

            // DOUBLE
            $sqlTypeData['double'] = $sqlTypeData['double precision'] = $sqlTypeData['decimal'] = $sqlTypeData['real'] = $sqlTypeData['numeric'] = $sqlTypeData['float'];
            $sqlTypeData['double unsigned'] = $sqlTypeData['float unsigned'];

            // BIT
            $sqlTypeData['bit'] = $sqlTypeData['boolean'];

            // TIMESTAMP
            $sqlTypeData['timestamp'] = $sqlTypeData['timestamp without time zone'] = $sqlTypeData['datetime'];

            // ENUM
            $sqlTypeData['enum'] = $sqlTypeData['set'] = $sqlTypeData['varchar'];

            // TEXT
            $sqlTypeData['tinytext'] = $sqlTypeData['mediumtext'] = $sqlTypeData['longtext'] = $sqlTypeData['text'];

            // BLOB
            $sqlTypeData['tsvector'] = $sqlTypeData['tinyblob'] = $sqlTypeData['mediumblob'] = $sqlTypeData['longblob'] = $sqlTypeData['clob'] = $sqlTypeData['bytea'] = $sqlTypeData['blob'];

            // CHARACTER
            $sqlTypeData['character'] = $sqlTypeData['char'];
            $sqlTypeData['character varying'] = $sqlTypeData['varchar'];

            // TIME
            $sqlTypeData['time without time zone'] = $sqlTypeData['time'];
        }

        return $sqlTypeData;
    }

    /**
     * Fetches SQL type information about a field, in a generic format.
     *
     * @param string $str field datatype
     *
     * @return array
     */
    protected function sqlType($str) {
        $sqlTypes = static::getSqlTypeData();

        $str = strtolower(trim($str));

        if (($open = strpos($str, '(')) !== false) {
            // Find closing bracket
            $close = strpos($str, ')', $open) - 1;

            // Find the type without the size
            $type = substr($str, 0, $open);
        } else {
            // No length
            $type = $str;
        }

        empty($sql_types[$type]) and exit('Unknown field type: ' . $type);

        // Fetch the field definition
        $field = $sqlTypes[$type];

        switch ($field['type']) {
            case 'string':
            case 'float':
                if (isset($close)) {
                    // Add the length to the field info
                    $field['length'] = substr($str, $open + 1, $close - $open);
                }

                break;
            case 'int':
                // Add unsigned value
                $field['unsigned'] = (strpos($str, 'unsigned') !== false);

                break;
        }

        return $field;
    }

    /**
     * Clears the internal query cache.
     *
     * @param string $sql SQL query
     */
    public function clearCache($sql = null) {
        if (empty($sql)) {
            $this->query_cache = [];
        } else {
            unset($this->query_cache[$this->queryHash($sql)]);
        }

        CF::log('debug', 'Database cache cleared: ' . get_class($this));
    }

    /**
     * Creates a hash for an SQL query string. Replaces newlines with spaces,
     * trims, and hashes.
     *
     * @param string $sql SQL query
     *
     * @return string
     */
    protected function queryHash($sql) {
        return sha1(str_replace("\n", ' ', trim($sql)));
    }

    public function beginTransaction() {
        $this->query('START TRANSACTION;');
    }

    public function rollback() {
        $this->query('ROLLBACK;');
    }

    public function commit() {
        $this->query('COMMIT;');
    }

    /**
     * @return CDatabase
     */
    public function db() {
        return $this->db;
    }
}

// End Database Driver Interface
