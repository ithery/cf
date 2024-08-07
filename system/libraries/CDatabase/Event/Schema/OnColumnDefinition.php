<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Event used when the portable column definition is generated inside CDatabase_Schema_Manager.
 */
class CDatabase_Event_Schema_OnColumnDefinition extends CDatabase_Event_Schema {
    /**
     * @var null|CDatabase_Schema_Column
     */
    private $column = null;

    /**
     * Raw column data as fetched from the database.
     *
     * @var array
     */
    private $tableColumn;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $database;

    /**
     * @var CDatabase_Connection
     */
    private $connection;

    /**
     * @param array                $tableColumn
     * @param string               $table
     * @param string               $database
     * @param CDatabase_Connection $connection
     */
    public function __construct(array $tableColumn, $table, $database, CDatabase_Connection $connection) {
        $this->tableColumn = $tableColumn;
        $this->table = $table;
        $this->database = $database;
        $this->connection = $connection;
    }

    /**
     * Allows to clear the column which means the column will be excluded from
     * tables column list.
     *
     * @param null|CDatabase_Schema_Column $column
     *
     * @return CDatabase_Event_Schema_OnColumnDefinition
     */
    public function setColumn(CDatabase_Schema_Column $column = null) {
        $this->column = $column;

        return $this;
    }

    /**
     * @return null|CDatabase_Schema_Column
     */
    public function getColumn() {
        return $this->column;
    }

    /**
     * @return array
     */
    public function getTableColumn() {
        return $this->tableColumn;
    }

    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getDatabase() {
        return $this->database;
    }

    /**
     * @return \CDatabase
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * @return CDatabase_Platform
     */
    public function getDatabasePlatform() {
        return $this->connection->getDatabasePlatform();
    }
}
