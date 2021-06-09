<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 1:00:30 PM
 */

/**
 * Event used when SQL queries for renaming table columns are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnAlterTableRenameColumn extends CDatabase_Event_Schema {
    /**
     * @var string
     */
    private $_oldColumnName;

    /**
     * @var CDatabase_Schema_Column
     */
    private $_column;

    /**
     * @var CDatabase_Schema_Table_Diff
     */
    private $_tableDiff;

    /**
     * @var CDatabase_Platform
     */
    private $_platform;

    /**
     * @var array
     */
    private $_sql = [];

    /**
     * @param string                      $oldColumnName
     * @param CDatabase_Schema_Column     $column
     * @param CDatabase_Schema_Table_Diff $tableDiff
     * @param CDatabase_Platform          $platform
     */
    public function __construct($oldColumnName, CDatabase_Schema_Column $column, CDatabase_Schema_Table_Diff $tableDiff, CDatabase_Platform $platform) {
        $this->_oldColumnName = $oldColumnName;
        $this->_column = $column;
        $this->_tableDiff = $tableDiff;
        $this->_platform = $platform;
    }

    /**
     * @return string
     */
    public function getOldColumnName() {
        return $this->_oldColumnName;
    }

    /**
     * @return CDatabase_Schema_Column
     */
    public function getColumn() {
        return $this->_column;
    }

    /**
     * @return CDatabase_Schema_Table_Diff
     */
    public function getTableDiff() {
        return $this->_tableDiff;
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
     * @return CDatabase_Event_Schema_OnAlterTableRenameColumn
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
