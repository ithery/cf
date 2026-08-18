<?php

defined('SYSPATH') or die('No direct access allowed.');
/**
 * Class: Database_PdoSqlite_Driver
 *  Provides specific database items for Sqlite.
 *
 * Connection string should be, eg: "pdosqlite://path/to/database.db"
 */
class CDatabase_Driver_PDO_Sqlite extends CDatabase_Driver {
    /**
     * Database connection link.
     */
    protected $link;

    protected $dbConfig;

    protected $db;

    /**
     * Constructor: __construct
     *  Sets up the config for the class.
     *
     * Parameters:
     *  config - database configuration
     *
     * @param mixed $config
     */
    public function __construct(CDatabase $db, $config) {
        $this->db = $db;
        $this->dbConfig = $config;
    }

    public function connect() {
        // Import the connect variables
        extract($this->dbConfig['connection']);
        if (!isset($socket)) {
            $socket = '';
        }
        if (!isset($user)) {
            $user = '';
        }
        if (!isset($pass)) {
            $pass = '';
        }

        try {
            $this->link = new PDO('sqlite:' . $socket . $database, $user, $pass, [PDO::ATTR_PERSISTENT => $this->dbConfig['persistent']]);

            $this->link->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
            //$this->link->query('PRAGMA count_changes=1;');

            if ($charset = $this->dbConfig['character_set']) {
                $this->setCharset($charset);
            }
        } catch (PDOException $e) {
            throw CDatabase_Exception::connectionException($e->getMessage());
        }

        // Clear password after successful connect
        $this->dbConfig['connection']['pass'] = null;

        return $this->link;
    }

    public function query($sql) {
        try {
            $sth = $this->link->prepare($sql);
        } catch (PDOException $e) {
            throw CDatabase_Exception::queryException($e->getMessage());
        }

        return new CDatabase_Driver_PDO_Sqlite_Result($sth, $this->link, $this->dbConfig['object'], $sql);
    }

    public function setCharset($charset) {
        $this->link->query('PRAGMA encoding = ' . $this->escapeStr($charset));
    }

    public function escapeTable($table) {
        if (!$this->dbConfig['escape']) {
            return $table;
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

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return string
     */
    public function limit($limit, $offset = 0) {
        return 'LIMIT ' . $offset . ', ' . $limit;
    }

    public function escapeStr($str) {
        if (!$this->dbConfig['escape']) {
            return $str;
        }

        $res = str_replace("'", "''", $str);

        return $res;
    }

    public function listTables() {
        $sql = "SELECT `name` FROM `sqlite_master` WHERE `type`='table' ORDER BY `name`;";

        try {
            $result = $this->query($sql)->result(false, PDO::FETCH_ASSOC);
            $tables = [];
            foreach ($result as $row) {
                $tables[] = current($row);
            }
        } catch (PDOException $e) {
            throw CDatabase_Exception::queryException($e->getMessage());
        }

        return $tables;
    }

    public function showError() {
        $err = $this->link->errorInfo();

        return isset($err[2]) ? $err[2] : 'Unknown error!';
    }

    public function listFields($table, $query = false) {
        static $tables;
        if (is_object($query)) {
            if (empty($tables[$table])) {
                $tables[$table] = [];

                foreach ($query->result() as $row) {
                    $tables[$table][] = $row->name;
                }
            }

            return $tables[$table];
        } else {
            $result = $this->link->query('PRAGMA table_info(' . $this->escapeTable($table) . ')');

            foreach ($result as $row) {
                $tables[$table][$row['name']] = $this->sqlType($row['type']);
            }

            return $tables[$table];
        }
    }

    public function fieldData($table) {
        CF::log('error', 'This method is under developing');
    }

    /**
     * Version number query string.
     *
     * @return string
     */
    public function version() {
        return $this->link->getAttribute(constant('PDO::ATTR_SERVER_VERSION'));
    }

    public function close() {
    }
}
