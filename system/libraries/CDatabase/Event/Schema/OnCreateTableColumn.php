<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 1:11:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Event used when SQL queries for creating table columns are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnCreateTableColumn extends CDatabase_Event_Schema {

    /**
     * @var CDatabase_Schema_Column
     */
    private $_column;

    /**
     * @var CDatabase_Schema_Table
     */
    private $_table;

    /**
     * @var CDatabase_Platform
     */
    private $_platform;

    /**
     * @var array
     */
    private $_sql = [];

    /**
     * @param CDatabase_Schema_Column               $column
     * @param CDatabase_Schema_Table                $table
     * @param CDatabase_Platform             $platform
     */
    public function __construct(CDatabase_Schema_Column $column, CDatabase_Schema_Table $table, CDatabase_Platform $platform) {
        $this->_column = $column;
        $this->_table = $table;
        $this->_platform = $platform;
    }

    /**
     * @return CDatabase_Schema_Column
     */
    public function getColumn() {
        return $this->_column;
    }

    /**
     * @return CDatabase_Schema_Table
     */
    public function getTable() {
        return $this->_table;
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
     * @return CDatabase_Event_Schema_OnCreateTableColumn
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
