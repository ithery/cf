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
    public $connectionName;

    /**
     * The database connection instance.
     *
     * @var CDatabase_Connection
     */
    public $connection;

    /* database */

    /**
     * Create a new event instance.
     *
     * @param CDatabase_Connection $connection
     *
     * @return void
     */
    public function __construct($connection) {
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }

    public static function dispatch(...$args) {
        return CEvent::dispatch(...$args);
    }
}
