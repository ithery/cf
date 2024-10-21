<?php

class CDatabase_Schema_Builder {
    /**
     * The default string length for migrations.
     *
     * @var int
     */
    public static $defaultStringLength = 255;

    /**
     * The default relationship morph key type.
     *
     * @var string
     */
    public static $defaultMorphKeyType = 'int';

    /**
     * Indicates whether Doctrine DBAL usage will be prevented if possible when dropping, renaming, and modifying columns.
     *
     * @var bool
     */
    public static $alwaysUsesNativeSchemaOperationsIfPossible = false;

    /**
     * The database connection instance.
     *
     * @var \CDatabase_Connection
     */
    protected $connection;

    /**
     * The schema grammar instance.
     *
     * @var \CDatabase_Schema_Grammar
     */
    protected $grammar;

    /**
     * The Blueprint resolver callback.
     *
     * @var \Closure
     */
    protected $resolver;

    /**
     * Create a new database Schema manager.
     *
     * @param \CDatabase_Connection $connection
     *
     * @return void
     */
    public function __construct(CDatabase_Connection $connection) {
        $this->connection = $connection;

        $this->grammar = $connection->getSchemaGrammar();
    }

    /**
     * Set the default string length for migrations.
     *
     * @param int $length
     *
     * @return void
     */
    public static function defaultStringLength($length) {
        static::$defaultStringLength = $length;
    }

    /**
     * Set the default morph key type for migrations.
     *
     * @param string $type
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public static function defaultMorphKeyType(string $type) {
        if (!in_array($type, ['int', 'uuid'])) {
            throw new InvalidArgumentException("Morph key type must be 'int' or 'uuid'.");
        }

        static::$defaultMorphKeyType = $type;
    }

    /**
     * Set the default morph key type for migrations to UUIDs.
     *
     * @return void
     */
    public static function morphUsingUuids() {
        return static::defaultMorphKeyType('uuid');
    }

    /**
     * Set the default morph key type for migrations to ULIDs.
     *
     * @return void
     */
    public static function morphUsingUlids() {
        return static::defaultMorphKeyType('ulid');
    }

    /**
     * Attempt to use native schema operations for dropping, renaming, and modifying columns, even if Doctrine DBAL is installed.
     *
     * @param bool $value
     *
     * @return void
     */
    public static function useNativeSchemaOperationsIfPossible(bool $value = true) {
        static::$alwaysUsesNativeSchemaOperationsIfPossible = $value;
    }

    /**
     * Create a database in the schema.
     *
     * @param string $name
     *
     * @throws \LogicException
     *
     * @return bool
     */
    public function createDatabase($name) {
        throw new LogicException('This database driver does not support creating databases.');
    }

    /**
     * Drop a database from the schema if the database exists.
     *
     * @param string $name
     *
     * @throws \LogicException
     *
     * @return bool
     */
    public function dropDatabaseIfExists($name) {
        throw new LogicException('This database driver does not support dropping databases.');
    }

    /**
     * Determine if the given table exists.
     *
     * @param string $table
     *
     * @return bool
     */
    public function hasTable($table) {
        $table = $this->connection->getTablePrefix() . $table;

        foreach ($this->getTables() as $value) {
            if (strtolower($table) === strtolower($value['name'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the given view exists.
     *
     * @param string $view
     *
     * @return bool
     */
    public function hasView($view) {
        $view = $this->connection->getTablePrefix() . $view;

        foreach ($this->getViews() as $value) {
            if (strtolower($view) === strtolower($value['name'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the tables that belong to the database.
     *
     * @return array
     */
    public function getTables() {
        $grammar = $this->grammar;

        return $this->connection->getPostProcessor()->processTables(
            $this->connection->selectFromWriteConnection($this->grammar->compileTables())
        );
    }

    /**
     * Get the names of the tables that belong to the database.
     *
     * @return array
     */
    public function getTableListing() {
        return array_column($this->getTables(), 'name');
    }

    /**
     * Get the views that belong to the database.
     *
     * @return array
     */
    public function getViews() {
        return $this->connection->getPostProcessor()->processViews(
            $this->connection->selectFromWriteConnection($this->grammar->compileViews())
        );
    }

    /**
     * Get the user-defined types that belong to the database.
     *
     * @return array
     */
    public function getTypes() {
        throw new LogicException('This database driver does not support user-defined types.');
    }

    /**
     * Determine if the given table has a given column.
     *
     * @param string $table
     * @param string $column
     *
     * @return bool
     */
    public function hasColumn($table, $column) {
        return in_array(
            strtolower($column),
            array_map('strtolower', $this->getColumnListing($table))
        );
    }

    /**
     * Determine if the given table has given columns.
     *
     * @param string $table
     * @param array  $columns
     *
     * @return bool
     */
    public function hasColumns($table, array $columns) {
        $tableColumns = array_map('strtolower', $this->getColumnListing($table));

        foreach ($columns as $column) {
            if (!in_array(strtolower($column), $tableColumns)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the data type for the given column name.
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    public function getColumnType($table, $column) {
        $table = $this->connection->getTablePrefix() . $table;

        return $this->connection->getDoctrineColumn($table, $column)->getType()->getName();
    }

    /**
     * Get the column listing for a given table.
     *
     * @param string $table
     *
     * @return array
     */
    public function getColumnListing($table) {
        return array_column($this->getColumns($table), 'name');
    }

    /**
     * Get the columns for a given table.
     *
     * @param string $table
     *
     * @return array
     */
    public function getColumns($table) {
        $table = $this->connection->getTablePrefix() . $table;

        return $this->connection->getPostProcessor()->processColumns(
            $this->connection->selectFromWriteConnection($this->grammar->compileColumns($table))
        );
    }

    /**
     * Modify a table on the schema.
     *
     * @param string   $table
     * @param \Closure $callback
     *
     * @return void
     */
    public function table($table, Closure $callback) {
        $this->build($this->createBlueprint($table, $callback));
    }

    /**
     * Create a new table on the schema.
     *
     * @param string   $table
     * @param \Closure $callback
     *
     * @return void
     */
    public function create($table, Closure $callback) {
        $this->build(c::tap($this->createBlueprint($table), function ($blueprint) use ($callback) {
            $blueprint->create();

            $callback($blueprint);
        }));
    }

    /**
     * Drop a table from the schema.
     *
     * @param string $table
     *
     * @return void
     */
    public function drop($table) {
        $this->build(c::tap($this->createBlueprint($table), function ($blueprint) {
            $blueprint->drop();
        }));
    }

    /**
     * Drop a table from the schema if it exists.
     *
     * @param string $table
     *
     * @return void
     */
    public function dropIfExists($table) {
        $this->build(c::tap($this->createBlueprint($table), function ($blueprint) {
            $blueprint->dropIfExists();
        }));
    }

    /**
     * Drop columns from a table schema.
     *
     * @param string       $table
     * @param string|array $columns
     *
     * @return void
     */
    public function dropColumns($table, $columns) {
        $this->table($table, function (CDatabase_Schema_Blueprint $blueprint) use ($columns) {
            $blueprint->dropColumn($columns);
        });
    }

    /**
     * Drop all tables from the database.
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function dropAllTables() {
        throw new LogicException('This database driver does not support dropping all tables.');
    }

    /**
     * Drop all views from the database.
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function dropAllViews() {
        throw new LogicException('This database driver does not support dropping all views.');
    }

    /**
     * Drop all types from the database.
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function dropAllTypes() {
        throw new LogicException('This database driver does not support dropping all types.');
    }

    /**
     * Get all of the table names for the database.
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function getAllTables() {
        throw new LogicException('This database driver does not support getting all tables.');
    }

    /**
     * Rename a table on the schema.
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    public function rename($from, $to) {
        $this->build(c::tap($this->createBlueprint($from), function ($blueprint) use ($to) {
            $blueprint->rename($to);
        }));
    }

    /**
     * Enable foreign key constraints.
     *
     * @return bool
     */
    public function enableForeignKeyConstraints() {
        return $this->connection->statement(
            $this->grammar->compileEnableForeignKeyConstraints()
        );
    }

    /**
     * Disable foreign key constraints.
     *
     * @return bool
     */
    public function disableForeignKeyConstraints() {
        return $this->connection->statement(
            $this->grammar->compileDisableForeignKeyConstraints()
        );
    }

    /**
     * Execute the blueprint to build / modify the table.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     *
     * @return void
     */
    protected function build(CDatabase_Schema_Blueprint $blueprint) {
        $blueprint->build($this->connection, $this->grammar);
    }

    /**
     * Create a new command set with a Closure.
     *
     * @param string        $table
     * @param null|\Closure $callback
     *
     * @return \CDatabase_Schema_Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null) {
        $prefix = $this->connection->getConfig('prefix_indexes')
                    ? $this->connection->getConfig('prefix')
                    : '';

        if (isset($this->resolver)) {
            return call_user_func($this->resolver, $table, $callback, $prefix);
        }

        return CContainer::getInstance()->make(CDatabase_Schema_Blueprint::class, compact('table', 'callback', 'prefix'));
    }

    /**
     * Register a custom Doctrine mapping type.
     *
     * @param string $class
     * @param string $name
     * @param string $type
     *
     * @return void
     */
    public function registerCustomDoctrineType($class, $name, $type) {
        $this->connection->registerDoctrineType($class, $name, $type);
    }

    /**
     * Get the database connection instance.
     *
     * @return \CDatabase_Connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Set the database connection instance.
     *
     * @param \CDatabase_Connection $connection
     *
     * @return $this
     */
    public function setConnection(CDatabase_Connection $connection) {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the Schema Blueprint resolver callback.
     *
     * @param \Closure $resolver
     *
     * @return void
     */
    public function blueprintResolver(Closure $resolver) {
        $this->resolver = $resolver;
    }
}
