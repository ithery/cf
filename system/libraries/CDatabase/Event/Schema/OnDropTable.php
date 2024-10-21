<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Event used when the SQL query for dropping tables are generated inside CDatabase_Platform.
 */
class CDatabase_Event_Schema_OnDropTable extends CDatabase_Event_Schema {
    /**
     * @var string|CDatabase_Schema_Table
     */
    private $table;

    /**
     * @var CDatabase_Platform
     */
    private $platform;

    /**
     * @var null|string
     */
    private $sql = null;

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
        $this->table = $table;
        $this->platform = $platform;
    }

    /**
     * @return string|CDatabase_Schema_Table
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @return CDatabase_Platform
     */
    public function getPlatform() {
        return $this->platform;
    }

    /**
     * @param string $sql
     *
     * @return CDatabase_Event_Schema_OnDropTable
     */
    public function setSql($sql) {
        $this->sql = $sql;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSql() {
        return $this->sql;
    }
}
