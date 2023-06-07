<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Event used when SQL queries for creating tables are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnCreateTable extends CDatabase_Event_Schema {
    /**
     * @var CDatabase_Schema_Table
     */
    private $table;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $options;

    /**
     * @var CDatabase_Platform
     */
    private $platform;

    /**
     * @var array
     */
    private $sql = [];

    /**
     * @param CDatabase_Schema_Table $table
     * @param array                  $columns
     * @param array                  $options
     * @param CDatabase_Platform     $platform
     */
    public function __construct(CDatabase_Schema_Table $table, array $columns, array $options, CDatabase_Platform $platform) {
        $this->table = $table;
        $this->columns = $columns;
        $this->options = $options;
        $this->platform = $platform;
    }

    /**
     * @return CDatabase_Schema_Table
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return CDatabase_Platform
     */
    public function getPlatform() {
        return $this->platform;
    }

    /**
     * @param string|array $sql
     *
     * @return CDatabase_Event_Schema_OnCreateTable
     */
    public function addSql($sql) {
        if (is_array($sql)) {
            $this->sql = array_merge($this->sql, $sql);
        } else {
            $this->sql[] = $sql;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSql() {
        return $this->sql;
    }
}
