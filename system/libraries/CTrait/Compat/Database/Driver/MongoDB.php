<?php

// @codingStandardsIgnoreStart
trait CTrait_Compat_Database_Driver_MongoDB {
    public function compile_select($database) {
    }

    /**
     * @param mixed $charset
     *
     * @deprecated use setCharset
     */
    public function set_charset($charset) {
        return $this->setCharset($charset);
    }

    /**
     * @deprecated use listTables
     */
    public function list_tables() {
        return $this->listTables();
    }

    /**
     * @param string $table
     *
     * @deprecated use escapeTable
     */
    public function escape_table($table) {
        return $this->escapeTable($table);
    }

    /**
     * @param string $table
     *
     * @deprecated use fieldData
     */
    public function field_data($table) {
        return $this->fieldData($table);
    }

    /**
     * @param string $table
     *
     * @deprecated use listFields
     */
    public function list_fields($table) {
        return $this->listFields($table);
    }

    /**
     * @param string $str
     *
     * @deprecated use escapeStr
     */
    public function escape_str($str) {
        return $this->escapeStr($str);
    }

    /**
     * @param string $column
     *
     * @deprecated use escapeColumn
     */
    public function escape_column($column) {
        return $this->escapeColumn($column);
    }

    /**
     * @deprecated use showError
     */
    public function show_error() {
        return $this->showError();
    }
}
