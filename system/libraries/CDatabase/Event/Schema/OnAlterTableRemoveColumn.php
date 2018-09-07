<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 12:58:11 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Event Arguments used when SQL queries for removing table columns are generated inside Doctrine\DBAL\Platform\*Platform.
 *
 */
class CDatabase_Event_Schema_OnAlterTableRemoveColumn extends CDatabase_Event_Schema {

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
     * @param CDatabase_Schema_Column       $column
     * @param CDatabase_Schema_Table_Diff   $tableDiff
     * @param CDatabase_Platform            $platform
     */
    public function __construct(CDatabase_Schema_Column $column, CDatabase_Schema_Table_Diff $tableDiff, CDatabase_Platform $platform) {
        $this->_column = $column;
        $this->_tableDiff = $tableDiff;
        $this->_platform = $platform;
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
     * @return CDatabase_Event_Schema_OnAlterTableRemoveColumn
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
