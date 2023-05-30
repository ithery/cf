<?php

class CDatabase_Connection_SqliteConnection extends CDatabase_Connection {
    /**
     * Create a new database connection instance.
     *
     * @param \PDO|\Closure $pdo
     * @param string        $database
     * @param string        $tablePrefix
     * @param array         $config
     *
     * @return void
     */
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = []) {
        parent::__construct($pdo, $database, $tablePrefix, $config);

        $enableForeignKeyConstraints = $this->getForeignKeyConstraintsConfigurationValue();

        if ($enableForeignKeyConstraints === null) {
            return;
        }

        $enableForeignKeyConstraints
            ? $this->getSchemaBuilder()->enableForeignKeyConstraints()
            : $this->getSchemaBuilder()->disableForeignKeyConstraints();
    }

    /**
     * Escape a binary value for safe SQL embedding.
     *
     * @param string $value
     *
     * @return string
     */
    protected function escapeBinary($value) {
        $hex = bin2hex($value);

        return "x'{$hex}'";
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \CDatabase_Query_Grammar_Sqlite
     */
    protected function getDefaultQueryGrammar() {
        ($grammar = new CDatabase_Query_Grammar_Sqlite())->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \CDatabase_Schema_Builder_SqliteBuilder
     */
    public function getSchemaBuilder() {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new CDatabase_Schema_Builder_SqliteBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \CDatabase_Schema_Grammar_Sqlite
     */
    protected function getDefaultSchemaGrammar() {
        ($grammar = new CDatabase_Schema_Grammar_Sqlite())->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get the schema state for the connection.
     *
     * @param null|callable $processFactory
     *
     * @throws \RuntimeException
     */
    public function getSchemaState(callable $processFactory = null) {
        // return new SqliteSchemaState($this, $files, $processFactory);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \CDatabase_Query_Processor_Sqlite
     */
    protected function getDefaultPostProcessor() {
        return new CDatabase_Query_Processor_Sqlite();
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Illuminate\Database\PDO\SQLiteDriver
     */
    protected function getDoctrineDriver() {
        // return new SQLiteDriver();
    }

    /**
     * Get the database connection foreign key constraints configuration option.
     *
     * @return null|bool
     */
    protected function getForeignKeyConstraintsConfigurationValue() {
        return $this->getConfig('foreign_key_constraints');
    }
}
