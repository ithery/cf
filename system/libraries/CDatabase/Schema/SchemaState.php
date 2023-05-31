<?php

use Symfony\Component\Process\Process;

abstract class CDatabase_Schema_SchemaState {
    /**
     * The connection instance.
     *
     * @var \CDatabase_Connection
     */
    protected $connection;

    /**
     * The name of the application's migration table.
     *
     * @var string
     */
    protected $migrationTable = 'migrations';

    /**
     * The process factory callback.
     *
     * @var callable
     */
    protected $processFactory;

    /**
     * The output callable instance.
     *
     * @var callable
     */
    protected $output;

    /**
     * Create a new dumper instance.
     *
     * @param \CDatabase_Connection $connection
     * @param null|callable         $processFactory
     *
     * @return void
     */
    public function __construct(CDatabase_Connection $connection, callable $processFactory = null) {
        $this->connection = $connection;

        $this->processFactory = $processFactory ?: function (...$arguments) {
            return Process::fromShellCommandline(...$arguments)->setTimeout(null);
        };

        $this->handleOutputUsing(function () {
        });
    }

    /**
     * Dump the database's schema into a file.
     *
     * @param \CDatabase_Connection $connection
     * @param string                $path
     *
     * @return void
     */
    abstract public function dump(CDatabase_Connection $connection, $path);

    /**
     * Load the given schema file into the database.
     *
     * @param string $path
     *
     * @return void
     */
    abstract public function load($path);

    /**
     * Create a new process instance.
     *
     * @param mixed ...$arguments
     *
     * @return \Symfony\Component\Process\Process
     */
    public function makeProcess(...$arguments) {
        return call_user_func($this->processFactory, ...$arguments);
    }

    /**
     * Specify the name of the application's migration table.
     *
     * @param string $table
     *
     * @return $this
     */
    public function withMigrationTable(string $table) {
        $this->migrationTable = $table;

        return $this;
    }

    /**
     * Specify the callback that should be used to handle process output.
     *
     * @param callable $output
     *
     * @return $this
     */
    public function handleOutputUsing(callable $output) {
        $this->output = $output;

        return $this;
    }
}
