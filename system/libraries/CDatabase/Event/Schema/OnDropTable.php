<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 1:05:06 PM
 */

/**
 * Event used when the SQL query for dropping tables are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnDropTable extends CDatabase_Event_Schema {
    /**
     * @var string|CDatabase_Schema_Table
     */
    private $_table;

    /**
     * @var CDatabase_Platform
     */
    private $_platform;

    /**
     * @var string|null
     */
    private $_sql = null;

    /**
     * @param string|CDatabase_Schema_Table $table
     * @param CDatabase_Platform            $platform
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($table, CDatabase_Platform $platform) {
        if (!$table instanceof CDatabase_Schema_Table && !is_string($table)) {
            throw new \InvalidArgumentException('SchemaDropTableEventArgs expects $table parameter to be string or \Doctrine\DBAL\Schema\Table.');
        }
        $this->_table = $table;
        $this->_platform = $platform;
    }

    /**
     * @return string|CDatabase_Schema_Table
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
     * @param string $sql
     *
     * @return CDatabase_Event_Schema_OnDropTable
     */
    public function setSql($sql) {
        $this->_sql = $sql;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSql() {
        return $this->_sql;
    }
}
