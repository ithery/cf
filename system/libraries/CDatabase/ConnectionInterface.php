<?php

interface CDatabase_ConnectionInterface {
    /**
     * Begin a fluent query against a database table.
     *
     * @param \Closure|CDatabase_Query_Builder|string $table
     * @param null|string                             $as
     *
     * @return \CDatabase_Query_Builder
     */
    public function table($table, $as = null);

    /**
     * Get a new raw query expression.
     *
     * @param mixed $value
     *
     * @return \CDatabase_Query_Builder
     */
    public function raw($value);

    /**
     * Run a select statement and return a single result.
     *
     * @param string $query
     * @param array  $bindings
     * @param bool   $useReadPdo
     *
     * @return mixed
     */
    public function selectOne($query, $bindings = [], $useReadPdo = true);

    /**
     * Run a select statement against the database.
     *
     * @param string $query
     * @param array  $bindings
     * @param bool   $useReadPdo
     *
     * @return array
     */
    public function select($query, $bindings = [], $useReadPdo = true);

    /**
     * Run a select statement against the database and returns a generator.
     *
     * @param string $query
     * @param array  $bindings
     * @param bool   $useReadPdo
     *
     * @return \Generator
     */
    public function cursor($query, $bindings = [], $useReadPdo = true);

    /**
     * Run an insert statement against the database.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return bool
     */
    public function insertWithQuery($query, $bindings = []);

    /**
     * Compiles an insert string and runs the query.
     *
     * @param string $table table name
     * @param array  $set   array of key/value pairs to insert
     *
     * @return bool
     */
    public function insert($table, $set);

    /**
     * Run an update statement against the database.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function updateWithQuery($query, $bindings = []);

    /**
     * Compiles an update string and runs the query.
     *
     * @param string $table table name
     * @param array  $set   associative array of update values
     * @param array  $where where clause
     *
     * @return int
     */
    public function update($table = '', $set = null, $where = null);

    /**
     * Run a delete statement against the database.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function deleteWithQuery($query, $bindings = []);

    /**
     * Compiles a delete string and runs the query.
     *
     * @param string $table table name
     * @param array  $where where clause
     *
     * @return int
     */
    public function delete($table = '', $where = []);

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return bool
     */
    public function statement($query, $bindings = []);

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function affectingStatement($query, $bindings = []);

    /**
     * Run a raw, unprepared query against the PDO connection.
     *
     * @param string $query
     *
     * @return bool
     */
    public function unprepared($query);

    /**
     * Prepare the query bindings for execution.
     *
     * @param array $bindings
     *
     * @return array
     */
    public function prepareBindings(array $bindings);

    /**
     * Execute a Closure within a transaction.
     *
     * @param \Closure $callback
     * @param int      $attempts
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function transaction(Closure $callback, $attempts = 1);

    /**
     * Start a new database transaction.
     *
     * @return void
     */
    public function beginTransaction();

    /**
     * Commit the active database transaction.
     *
     * @return void
     */
    public function commit();

    /**
     * Rollback the active database transaction.
     *
     * @return void
     */
    public function rollBack();

    /**
     * Get the number of active transactions.
     *
     * @return int
     */
    public function transactionLevel();

    /**
     * Execute the given callback in "dry run" mode.
     *
     * @param \Closure $callback
     *
     * @return array
     */
    public function pretend(Closure $callback);

    /**
     * Get the name of the connected database.
     *
     * @return string
     */
    public function getDatabaseName();

    /**
     * Combine a SQL statement with the bind values. Used for safe queries.
     *
     * @param string $sql   query to bind to the values
     * @param array  $binds array of values to bind to the query
     *
     * @return string
     */
    public function compileBinds($sql, array $binds = []);
}
