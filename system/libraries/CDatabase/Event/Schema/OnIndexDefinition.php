<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 1:14:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Event used when the portable index definition is generated inside CDatabase_Schema_Manager.
 *
 */
class CDatabase_Event_Schema_OnIndexDefinition extends CDatabase_Event_Schema {

    /**
     * @var CDatabase_Schema_Index|null
     */
    private $_index = null;

    /**
     * Raw index data as fetched from the database.
     *
     * @var array
     */
    private $_tableIndex;

    /**
     * @var string
     */
    private $_table;

    /**
     * @var CDatabase
     */
    private $_connection;

    /**
     * @param array                     $tableIndex
     * @param string                    $table
     * @param CDatabase                 $connection
     */
    public function __construct(array $tableIndex, $table, CDatabase $connection) {
        $this->_tableIndex = $tableIndex;
        $this->_table = $table;
        $this->_connection = $connection;
    }

    /**
     * Allows to clear the index which means the index will be excluded from tables index list.
     *
     * @param null|CDatabase_Schema_Index $index
     *
     * @return CDatabase_Event_Schema_OnIndexDefinition
     */
    public function setIndex(CDatabase_Schema_Index $index = null) {
        $this->_index = $index;
        return $this;
    }

    /**
     * @return CDatabase_Schema_Index|null
     */
    public function getIndex() {
        return $this->_index;
    }

    /**
     * @return array
     */
    public function getTableIndex() {
        return $this->_tableIndex;
    }

    /**
     * @return string
     */
    public function getTable() {
        return $this->_table;
    }

    /**
     * @return CDatabase
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
