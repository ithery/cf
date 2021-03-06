<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 12:19:51 PM
 */
class CDatabase_Event {
    /**
     * The name of the connection.
     *
     * @var string
     */
    public $dbName;

    /**
     * The database connection instance.
     *
     * @var CDatabase
     */
    public $db;

    /* database */

    /**
     * Create a new event instance.
     *
     * @param CDatabase $db
     *
     * @return void
     */
    public function __construct($db) {
        $this->db = $db;
        $this->dbName = $db->getName();
    }

    public static function createOnQueryExecutedEvent($sql, $bindings, $time, $rowsCount, $db) {
        return new CDatabase_Event_OnQueryExecuted($sql, $bindings, $time, $rowsCount, $db);
    }

    public static function dispatch(...$args) {
        return CEvent::dispatch(...$args);
    }
}
