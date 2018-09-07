<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 12:47:40 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Event used when SQL queries for adding table columns are generated inside CDatabase_Platform.
 *
 */
class CDatabase_Event_Schema_OnAlterTableAddColumnEventArgs extends CDatabase_Event_Schema {

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
     * @param CDatabase_Schema_Platform     $platform
     */
    public function __construct(CDatabase_Schema_Column $column, CDatabase_Schema_Table_Diff $tableDiff, CDatabase_Schema_Platform $platform) {
        $this->_column = $column;
        $this->_tableDiff = $tableDiff;
        $this->_platform = $platform;
    }

    /**
     * @return \Doctrine\DBAL\Schema\Column
     */
    public function getColumn() {
        return $this->_column;
    }

    /**
     * @return \Doctrine\DBAL\Schema\TableDiff
     */
    public function getTableDiff() {
        return $this->_tableDiff;
    }

    /**
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    public function getPlatform() {
        return $this->_platform;
    }

    /**
     * @param string|array $sql
     *
     * @return \Doctrine\DBAL\Event\SchemaAlterTableAddColumnEventArgs
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
