<?php

class CDatabase_Event_StatementPrepared {
    /**
     * The database connection instance.
     *
     * @var \CDatabase_Connection
     */
    public $connection;

    /**
     * The PDO statement.
     *
     * @var \PDOStatement
     */
    public $statement;

    /**
     * Create a new event instance.
     *
     * @param \CDatabase_Connection $connection
     * @param \PDOStatement         $statement
     *
     * @return void
     */
    public function __construct($connection, $statement) {
        $this->statement = $statement;
        $this->connection = $connection;
    }
}
