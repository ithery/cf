<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Event used when SQL queries for renaming table columns are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnAlterTableRenameColumn extends CDatabase_Event_Schema {
    /**
     * @var string
     */
    private $oldColumnName;

    /**
     * @var CDatabase_Schema_Column
     */
    private $column;

    /**
     * @var CDatabase_Schema_Table_Diff
     */
    private $tableDiff;

    /**
     * @var CDatabase_Platform
     */
    private $platform;

    /**
     * @var array
     */
    private $sql = [];

    /**
     * @param string                      $oldColumnName
     * @param CDatabase_Schema_Column     $column
     * @param CDatabase_Schema_Table_Diff $tableDiff
     * @param CDatabase_Platform          $platform
     */
    public function __construct($oldColumnName, CDatabase_Schema_Column $column, CDatabase_Schema_Table_Diff $tableDiff, CDatabase_Platform $platform) {
        $this->oldColumnName = $oldColumnName;
        $this->column = $column;
        $this->tableDiff = $tableDiff;
        $this->platform = $platform;
    }

    /**
     * @return string
     */
    public function getOldColumnName() {
        return $this->oldColumnName;
    }

    /**
     * @return CDatabase_Schema_Column
     */
    public function getColumn() {
        return $this->column;
    }

    /**
     * @return CDatabase_Schema_Table_Diff
     */
    public function getTableDiff() {
        return $this->tableDiff;
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
     * @return CDatabase_Event_Schema_OnAlterTableRenameColumn
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
