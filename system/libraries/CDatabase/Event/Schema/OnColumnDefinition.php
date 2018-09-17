<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 1:07:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Event used when the portable column definition is generated inside CDatabase_Schema_Manager.
 */
class CDatabase_Event_Schema_OnColumnDefinition extends CDatabase_Event_Schema {

    /**
     * @var CDatabase_Schema_Column|null
     */
    private $_column = null;

    /**
     * Raw column data as fetched from the database.
     *
     * @var array
     */
    private $_tableColumn;

    /**
     * @var string
     */
    private $_table;

    /**
     * @var string
     */
    private $_database;

    /**
     * @var CDatabase
     */
    private $_connection;

    /**
     * @param array                     $tableColumn
     * @param string                    $table
     * @param string                    $database
     * @param CDatabase $connection
     */
    public function __construct(array $tableColumn, $table, $database, CDatabase $connection) {
        $this->_tableColumn = $tableColumn;
        $this->_table = $table;
        $this->_database = $database;
        $this->_connection = $connection;
    }

    /**
     * Allows to clear the column which means the column will be excluded from
     * tables column list.
     *
     * @param null|CDatabase_Schema_Column $column
     *
     * @return CDatabase_Event_Schema_OnColumnDefinition
     */
    public function setColumn(Column $column = null) {
        $this->_column = $column;
        return $this;
    }

    /**
     * @return CDatabase_Schema_Column|null
     */
    public function getColumn() {
        return $this->_column;
    }

    /**
     * @return array
     */
    public function getTableColumn() {
        return $this->_tableColumn;
    }

    /**
     * @return string
     */
    public function getTable() {
        return $this->_table;
    }

    /**
     * @return string
     */
    public function getDatabase() {
        return $this->_database;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection() {
        return $this->_connection;
    }

    /**
     * @return CDatabase_Platform
     */
    public function getDatabasePlatform() {
        return $this->_connection->getDatabasePlatform();
    }

}
