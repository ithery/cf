<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 1:03:05 PM
 */

/**
 * Event used when SQL queries for creating tables are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnCreateTable extends CDatabase_Event_Schema {
    /**
     * @var CDatabase_Schema_Table
     */
    private $_table;

    /**
     * @var array
     */
    private $_columns;

    /**
     * @var array
     */
    private $_options;

    /**
     * @var CDatabase_Platform
     */
    private $_platform;

    /**
     * @var array
     */
    private $_sql = [];

    /**
     * @param CDatabase_Schema_Table $table
     * @param array                  $columns
     * @param array                  $options
     * @param CDatabase_Platform     $platform
     */
    public function __construct(CDatabase_Schema_Table $table, array $columns, array $options, CDatabase_Platform $platform) {
        $this->_table = $table;
        $this->_columns = $columns;
        $this->_options = $options;
        $this->_platform = $platform;
    }

    /**
     * @return CDatabase_Schema_Table
     */
    public function getTable() {
        return $this->_table;
    }

    /**
     * @return array
     */
    public function getColumns() {
        return $this->_columns;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * @return CDatabase_Platform
     */
    public function getPlatform() {
        return $this->_platform;
    }

    /**
     * @param string|array $sql
     *
     * @return CDatabase_Event_Schema_OnCreateTable
     */
    public function addSql($sql) {
        if (is_array($sql)) {
            $this->_sql = array_merge($this->_sql, $sql);
        } else {
            $this->_sql[] = $sql;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getSql() {
        return $this->_sql;
    }
}
