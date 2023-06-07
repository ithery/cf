<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Event used when the portable index definition is generated inside CDatabase_Schema_Manager.
 */
class CDatabase_Event_Schema_OnIndexDefinition extends CDatabase_Event_Schema {
    /**
     * @var null|CDatabase_Schema_Index
     */
    private $index = null;

    /**
     * Raw index data as fetched from the database.
     *
     * @var array
     */
    private $tableIndex;

    /**
     * @var string
     */
    private $table;

    /**
     * @var CDatabase_Connection
     */
    private $connection;

    /**
     * @param array                $tableIndex
     * @param string               $table
     * @param CDatabase_Connection $connection
     */
    public function __construct(array $tableIndex, $table, CDatabase_Connection $connection) {
        $this->tableIndex = $tableIndex;
        $this->table = $table;
        $this->connection = $connection;
    }

    /**
     * Allows to clear the index which means the index will be excluded from tables index list.
     *
     * @param null|CDatabase_Schema_Index $index
     *
     * @return CDatabase_Event_Schema_OnIndexDefinition
     */
    public function setIndex(CDatabase_Schema_Index $index = null) {
        $this->index = $index;

        return $this;
    }

    /**
     * @return null|CDatabase_Schema_Index
     */
    public function getIndex() {
        return $this->index;
    }

    /**
     * @return array
     */
    public function getTableIndex() {
        return $this->tableIndex;
    }

    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @return CDatabase
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
