<?php

defined('SYSPATH') or die('No direct access allowed.');

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
