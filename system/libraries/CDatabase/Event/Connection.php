<?php
abstract class CDatabase_Event_Connection {
    /**
     * The name of the connection.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The database connection instance.
     *
     * @var \CDatabase_Connection
     */
    public $connection;

    /**
     * Create a new event instance.
     *
     * @param \CDatabase_Connection $connection
     *
     * @return void
     */
    public function __construct($connection) {
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }
}
