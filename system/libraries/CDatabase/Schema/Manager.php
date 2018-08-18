<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:09:14 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDatabase_Schema_Manager {

    /**
     * Holds instance of the connection for this schema manager.
     *
     * @var CDatabase
     */
    protected $db;

    /**
     * Holds instance of the database platform used for this schema manager.
     *
     * @var CDatabase_Platform
     */
    protected $_platform;

    /**
     * Constructor. Accepts the Connection instance to manage the schema for.
     *
     * @param \Doctrine\DBAL\Connection                      $conn
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform|null $platform
     */
    public function __construct(CDatabase $conn, CDatabase_Platform $platform = null) {
        $this->db = $conn;
        $this->platform = $platform ? $platform : $this->db->getDatabasePlatform();
    }

    /**
     * Returns the associated platform.
     *
     * @return CDatabase_Platform
     */
    public function getDatabasePlatform() {
        return $this->platform;
    }

    /**
     * Lists the available databases for this connection.
     *
     * @return array
     */
    public function listDatabases() {
        $sql = $this->platform->getListDatabasesSQL();

        $databases = $this->db->fetchAll($sql);

        return $this->_getPortableDatabasesList($databases);
    }

    /**
     * Methods for filtering return values of list*() methods to convert
     * the native DBMS data definition to a portable Doctrine definition
     */

    /**
     * @param array $databases
     *
     * @return array
     */
    protected function _getPortableDatabasesList($databases) {
        $list = [];
        foreach ($databases as $value) {
            if ($value = $this->_getPortableDatabaseDefinition($value)) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * @param array $database
     *
     * @return mixed
     */
    protected function _getPortableDatabaseDefinition($database) {
        return $database;
    }

}
