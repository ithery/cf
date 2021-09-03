<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 12:55:34 PM
 */

/**
 * Event used when SQL queries for creating tables are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnAlterTable extends CDatabase_Event_Schema {
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
     * @param CDatabase_Schema_Table_Diff $tableDiff
     * @param CDatabase_Platform          $platform
     */
    public function __construct(CDatabase_Schema_Table_Diff $tableDiff, CDatabase_Platform $platform) {
        $this->_tableDiff = $tableDiff;
        $this->_platform = $platform;
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
     * @return \Doctrine\DBAL\Event\SchemaAlterTableEventArgs
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
