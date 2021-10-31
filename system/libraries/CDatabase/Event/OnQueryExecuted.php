<?php

class CDatabase_Event_OnQueryExecuted {
    /**
     * The SQL query that was executed.
     *
     * @var string
     */
    public $sql;

    /**
     * The array of query bindings.
     *
     * @var array
     */
    public $bindings;

    /**
     * The number of milliseconds it took to execute the query.
     *
     * @var float
     */
    public $time;

    /**
     * The number of milliseconds it took to execute the query.
     *
     * @var int
     */
    public $rowsCount;

    /**
     * Create a new event instance.
     *
     * @param string    $sql
     * @param array     $bindings
     * @param float     $time
     * @param int       $rowsCount
     * @param CDatabase $db
     *
     * @return void
     */
    public function __construct($sql, $bindings, $time, $rowsCount, $db) {
        $this->sql = $sql;
        $this->time = $time;
        $this->bindings = $bindings;
        $this->rowsCount = $rowsCount;
        $this->connection = $db;
        $this->connectionName = $db->getName();
    }
}
