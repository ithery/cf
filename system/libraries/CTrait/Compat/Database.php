<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CDatabase
 */

// @codingStandardsIgnoreStart
trait CTrait_Compat_Database {
    /**
     * @return string
     *
     * @deprecated since version 1.2 use driverName
     */
    public function driver_name() {
        return $this->driverName();
    }

    /**
     * @param string $str
     *
     * @return string
     *
     * @deprecated use escapeLike
     */
    public function escape_like($str) {
        return $this->escapeLike($str);
    }

    /**
     * @return bool
     *
     * @deprecated since version 1.2 use inTransaction
     */
    public function in_transaction() {
        return $this->inTransaction();
    }

    /**
     * Returns the last query run.
     *
     * @return string SQL
     *
     * @deprecated use lastQuery
     */
    public function last_query() {
        return $this->lastQuery();
    }

    /**
     * Escapes a string for a query.
     *
     * @param   string  string to escape
     * @param mixed $str
     *
     * @return string
     *
     * @deprecated use escapeStr
     */
    public function escape_str($str) {
        return $this->escapeStr($str);
    }

    /**
     * Escapes a table name for a query.
     *
     * @param   string  string to escape
     * @param mixed $table
     *
     * @return string
     *
     * @deprecated use escapeTable
     */
    public function escape_table($table) {
        return $this->escapeTable($table);
    }

    /**
     * Escapes a column name for a query.
     *
     * @param   string  string to escape
     * @param mixed $table
     *
     * @return string
     *
     * @deprecated use escapeColumn
     */
    public function escape_column($table) {
        return $this->escapeColumn($table);
    }

    /**
     * See if a table exists in the database.
     *
     * @param   string   table name
     * @param   bool  True to attach table prefix
     * @param mixed $table_name
     * @param mixed $prefix
     *
     * @return bool
     *
     * @deprecated use tableExists
     */
    public function table_exists($table_name, $prefix = true) {
        return $this->tableExists($table_name, $prefix);
    }

    /**
     * Count query records.
     *
     * @param string $table table name
     * @param array  $where where clause
     *
     * @return int
     *
     * @deprecated 1.1
     */
    public function count_records($table = false, $where = null) {
        if (count($this->from) < 1) {
            if ($table == false) {
                throw new CDatabase_Exception('You must set a database table for your query');
            }
            $this->from($table);
        }

        if ($where !== null) {
            $this->where($where);
        }

        $query = $this->select('COUNT(*) AS ' . $this->escapeColumn('records_found'))->get()->result(true);

        return (int) $query->current()->records_found;
    }

    /**
     * Returns table prefix of current configuration.
     *
     * @return string
     *
     * @deprecated 1.1
     */
    public function table_prefix() {
        return $this->config['table_prefix'];
    }

    /**
     * Get the field data for a database table, along with the field's attributes.
     *
     * @param string $table table name
     *
     * @return array
     *
     * @deprecated
     */
    public function field_data($table = '') {
        return $this->fieldData($table);
    }

    /**
     * Count the number of records in the last query, without LIMIT or OFFSET applied.
     *
     * @return int
     *
     * @deprecated 1.1
     */
    public function count_last_query() {
        return $this->countLastQuery();
    }
}
// @codingStandardsIgnoreEnd
