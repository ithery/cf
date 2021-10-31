<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 12:53:13 PM
 */

/**
 * Event used when SQL queries for changing table columns are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnAlterTableChangeColumn extends CDatabase_Event_Schema {
    /**
     * @var CDatabase_Schema_Column_Diff
     */
    private $columnDiff;

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
     * @param CDatabase_Schema_Column_Diff $columnDiff
     * @param CDatabase_Schema_Table_Diff  $tableDiff
     * @param CDatabase_Platform           $platform
     */
    public function __construct(CDatabase_Schema_Column_Diff $columnDiff, CDatabase_Schema_Table_Diff $tableDiff, CDatabase_Platform $platform) {
        $this->columnDiff = $columnDiff;
        $this->tableDiff = $tableDiff;
        $this->platform = $platform;
    }

    /**
     * @return CDatabase_Schema_Column_Diff
     */
    public function getColumnDiff() {
        return $this->columnDiff;
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
     * @return \Doctrine\DBAL\Event\SchemaAlterTableChangeColumnEventArgs
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
