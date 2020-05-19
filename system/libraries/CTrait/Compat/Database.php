<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 22, 2019, 2:10:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Database {

    /**
     * 
     * @deprecated since version 1.2
     * @return string
     */
    public function driver_name() {
        return $this->driverName();
    }

    /**
     * 
     * @deprecated
     * @param string $str
     * @return string
     */
    public function escape_like($str) {
        return $this->escapeLike($str);
    }

    /**
     * 
     * @deprecated since version 1.2
     * @return boolean
     */
    public function in_transaction() {
        return $this->inTransaction();
    }

    /**
     * Returns the last query run.
     *
     * @deprecated
     * @return  string SQL
     */
    public function last_query() {
        return $this->lastQuery();
    }

    /**
     * Escapes a string for a query.
     *
     * @param   string  string to escape
     * @return  string
     */
    public function escape_str($str) {
        return $this->escapeStr($str);
    }

    /**
     * Escapes a table name for a query.
     *
     * @param   string  string to escape
     * @return  string
     */
    public function escape_table($table) {
        return $this->escapeTable($table);
    }

    /**
     * Escapes a column name for a query.
     *
     * @param   string  string to escape
     * @return  string
     */
    public function escape_column($table) {
        return $this->escapeColumn($table);
    }

    /**
     * See if a table exists in the database.
     *
     * @param   string   table name
     * @param   boolean  True to attach table prefix
     * @return  boolean
     */
    public function table_exists($table_name, $prefix = TRUE) {
        return $this->tableExists($table_name, $prefix);
    }

    /**
     * Lists all the tables in the current database.
     *
     * @return  array
     */
    public function list_tables() {
        return $this->listTables();
    }

    /**
     * Get the field data for a database table, along with the field's attributes.
     *
     * @param   string  table name
     * @return  array
     * @deprecated
     */
    public function list_fields($table = '') {
        return $this->listFields($table);
    }

}
