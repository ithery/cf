<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:09:14 AM
 */
abstract class CDatabase_Schema_Manager {
    /**
     * Holds instance of the connection for this schema manager.
     *
     * @var CDatabase
     */
    protected $db;

    /**
     * Holds instance of the database platform used for this schema manager.
     *
     * @var CDatabase_Platform
     */
    protected $platform;

    protected $isSuperUser;

    /**
     * Constructor. Accepts the Connection instance to manage the schema for.
     *
     * @param CDatabase               $conn
     * @param CDatabase_Platform|null $platform
     */
    public function __construct(CDatabase $conn, CDatabase_Platform $platform = null) {
        $this->db = $conn;
        $this->platform = $platform ? $platform : $this->db->getDatabasePlatform();
    }

    /**
     * Returns the associated platform.
     *
     * @return CDatabase_Platform
     */
    public function getDatabasePlatform() {
        return $this->platform;
    }

    /**
     * Tries any method on the schema manager. Normally a method throws an
     * exception when your DBMS doesn't support it or if an error occurs.
     * This method allows you to try and method on your SchemaManager
     * instance and will return false if it does not work or is not supported.
     *
     * <code>
     * $result = $sm->tryMethod('dropView', 'view_name');
     * </code>
     *
     * @return mixed
     */
    public function tryMethod() {
        $args = func_get_args();
        $method = $args[0];
        unset($args[0]);
        $args = array_values($args);

        try {
            return call_user_func_array([$this, $method], $args);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Lists the available databases for this connection.
     *
     * @return array
     */
    public function listDatabases() {
        $sql = $this->platform->getListDatabasesSQL();

        $databases = $this->db->fetchAll($sql);

        return $this->getPortableDatabasesList($databases);
    }

    /**
     * Returns a list of all namespaces in the current database.
     *
     * @return array
     */
    public function listNamespaceNames() {
        $sql = $this->platform->getListNamespacesSQL();

        $namespaces = $this->db->fetchAll($sql);

        return $this->getPortableNamespacesList($namespaces);
    }

    /**
     * Lists the available sequences for this connection.
     *
     * @param string|null $database
     *
     * @return \Doctrine\DBAL\Schema\Sequence[]
     */
    public function listSequences($database = null) {
        if ($database === null) {
            $database = $this->db->getDatabase();
        }
        $sql = $this->platform->getListSequencesSQL($database);

        $sequences = $this->db->fetchAll($sql);

        return $this->filterAssetNames($this->getPortableSequencesList($sequences));
    }

    /**
     * Lists the columns for a given table.
     *
     * In contrast to other libraries and to the old version of Doctrine,
     * this column definition does try to contain the 'primary' field for
     * the reason that it is not portable across different RDBMS. Use
     * {@see listTableIndexes($tableName)} to retrieve the primary key
     * of a table. We're a RDBMS specifies more details these are held
     * in the platformDetails array.
     *
     * @param string      $table    the name of the table
     * @param string|null $database
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function listTableColumns($table, $database = null) {
        if (!$database) {
            $database = $this->db->getDatabase();
        }

        $sql = $this->platform->getListTableColumnsSQL($table, $database);

        $tableColumns = $this->db->fetchAll($sql);

        return $this->getPortableTableColumnList($table, $database, $tableColumns);
    }

    /**
     * Lists the indexes for a given table returning an array of Index instances.
     *
     * Keys of the portable indexes list are all lower-cased.
     *
     * @param string $table the name of the table
     *
     * @return \Doctrine\DBAL\Schema\Index[]
     */
    public function listTableIndexes($table) {
        $sql = $this->platform->getListTableIndexesSQL($table, $this->db->getDatabase());

        $tableIndexes = $this->db->fetchAll($sql);

        return $this->getPortableTableIndexesList($tableIndexes, $table);
    }

    /**
     * Returns true if all the given tables exist.
     *
     * @param array $tableNames
     *
     * @return bool
     */
    public function tablesExist($tableNames) {
        $tableNames = array_map('strtolower', (array) $tableNames);

        return count($tableNames) == count(\array_intersect($tableNames, array_map('strtolower', $this->listTableNames())));
    }

    /**
     * Returns a list of all tables in the current database.
     *
     * @return array
     */
    public function listTableNames() {
        $sql = $this->platform->getListTablesSQL();

        $tables = $this->db->fetchAll($sql);
        $tableNames = $this->getPortableTablesList($tables);

        return $this->filterAssetNames($tableNames);
    }

    /**
     * Filters asset names if they are configured to return only a subset of all
     * the found elements.
     *
     * @param array $assetNames
     *
     * @return array
     */
    protected function filterAssetNames($assetNames) {
        $filterExpr = $this->getFilterSchemaAssetsExpression();
        if (!$filterExpr) {
            return $assetNames;
        }

        return array_values(
            array_filter($assetNames, function ($assetName) use ($filterExpr) {
                $assetName = ($assetName instanceof CDatabase_AbstractAsset) ? $assetName->getName() : $assetName;

                return preg_match($filterExpr, $assetName);
            })
        );
    }

    /**
     * @return string|null
     */
    protected function getFilterSchemaAssetsExpression() {
        return $this->db->getConfiguration()->getFilterSchemaAssetsExpression();
    }

    /**
     * Lists the tables for this connection.
     *
     * @return \Doctrine\DBAL\Schema\Table[]
     */
    public function listTables() {
        $tableNames = $this->listTableNames();

        $tables = [];
        foreach ($tableNames as $tableName) {
            $tables[] = $this->listTableDetails($tableName);
        }

        return $tables;
    }

    /**
     * @param string $tableName
     *
     * @return \Doctrine\DBAL\Schema\Table
     */
    public function listTableDetails($tableName) {
        $columns = $this->listTableColumns($tableName);
        $foreignKeys = [];
        if ($this->platform->supportsForeignKeyConstraints()) {
            $foreignKeys = $this->listTableForeignKeys($tableName);
        }
        $indexes = $this->listTableIndexes($tableName);

        return new CDatabase_Schema_Table($tableName, $columns, $indexes, $foreignKeys, false, []);
    }

    /**
     * Lists the views this connection has.
     *
     * @return \Doctrine\DBAL\Schema\View[]
     */
    public function listViews() {
        $database = $this->db->getDatabase();
        $sql = $this->platform->getListViewsSQL($database);
        $views = $this->db->fetchAll($sql);

        return $this->getPortableViewsList($views);
    }

    /**
     * Lists the foreign keys for the given table.
     *
     * @param string      $table    the name of the table
     * @param string|null $database
     *
     * @return \Doctrine\DBAL\Schema\ForeignKeyConstraint[]
     */
    public function listTableForeignKeys($table, $database = null) {
        if ($database === null) {
            $database = $this->db->getDatabase();
        }
        $sql = $this->platform->getListTableForeignKeysSQL($table, $database);
        $tableForeignKeys = $this->db->fetchAll($sql);

        return $this->getPortableTableForeignKeysList($tableForeignKeys);
    }

    /* drop*() Methods */

    /**
     * Drops a database.
     *
     * NOTE: You can not drop the database this SchemaManager is currently connected to.
     *
     * @param string $database the name of the database to drop
     *
     * @return void
     */
    public function dropDatabase($database) {
        $this->execSql($this->platform->getDropDatabaseSQL($database));
    }

    /**
     * Drops the given table.
     *
     * @param string $tableName the name of the table to drop
     *
     * @return void
     */
    public function dropTable($tableName) {
        $this->execSql($this->platform->getDropTableSQL($tableName));
    }

    /**
     * Drops the index from the given table.
     *
     * @param \CDatabase_Schema_Index|string $index the name of the index
     * @param \CDatabase_Schema_Table|string $table the name of the table
     *
     * @return void
     */
    public function dropIndex($index, $table) {
        if ($index instanceof CDatabase_Schema_Index) {
            $index = $index->getQuotedName($this->platform);
        }

        $this->execSql($this->platform->getDropIndexSQL($index, $table));
    }

    /**
     * Drops the constraint from the given table.
     *
     * @param \CDatabase_Schema_Constraint   $constraint
     * @param \CDatabase_Schema_Table|string $table      the name of the table
     *
     * @return void
     */
    public function dropConstraint(CDatabase_Schema_Constraint $constraint, $table) {
        $this->execSql($this->platform->getDropConstraintSQL($constraint, $table));
    }

    /**
     * Drops a foreign key from a table.
     *
     * @param \Doctrine\DBAL\Schema\ForeignKeyConstraint|string $foreignKey the name of the foreign key
     * @param \Doctrine\DBAL\Schema\Table|string                $table      the name of the table with the foreign key
     *
     * @return void
     */
    public function dropForeignKey($foreignKey, $table) {
        $this->execSql($this->platform->getDropForeignKeySQL($foreignKey, $table));
    }

    /**
     * Drops a sequence with a given name.
     *
     * @param string $name the name of the sequence to drop
     *
     * @return void
     */
    public function dropSequence($name) {
        $this->execSql($this->platform->getDropSequenceSQL($name));
    }

    /**
     * Drops a view.
     *
     * @param string $name the name of the view
     *
     * @return void
     */
    public function dropView($name) {
        $this->execSql($this->platform->getDropViewSQL($name));
    }

    /* create*() Methods */

    /**
     * Creates a new database.
     *
     * @param string $database the name of the database to create
     *
     * @return void
     */
    public function createDatabase($database) {
        $this->execSql($this->platform->getCreateDatabaseSQL($database));
    }

    /**
     * Creates a new table.
     *
     * @param \CDatabase_Schema_Table $table
     *
     * @return void
     */
    public function createTable(CDatabase_Schema_Table $table) {
        $createFlags = CDatabase_Platform::CREATE_INDEXES | CDatabase_Platform::CREATE_FOREIGNKEYS;
        $this->execSql($this->platform->getCreateTableSQL($table, $createFlags));
    }

    /**
     * Creates a new sequence.
     *
     * @param \CDatabase_Schema_Sequence $sequence
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\ConnectionException if something fails at database level
     */
    public function createSequence($sequence) {
        $this->execSql($this->platform->getCreateSequenceSQL($sequence));
    }

    /**
     * Creates a constraint on a table.
     *
     * @param \CDatabase_Schema_Constraint   $constraint
     * @param \CDatabase_Schema_Table|string $table
     *
     * @return void
     */
    public function createConstraint(CDatabase_Schema_Constraint $constraint, $table) {
        $this->execSql($this->platform->getCreateConstraintSQL($constraint, $table));
    }

    /**
     * Creates a new index on a table.
     *
     * @param \CDatabase_Schema_Index        $index
     * @param \CDatabase_Schema_Table|string $table the name of the table on which the index is to be created
     *
     * @return void
     */
    public function createIndex(CDatabase_Schema_Index $index, $table) {
        $this->execSql($this->platform->getCreateIndexSQL($index, $table));
    }

    /**
     * Creates a new foreign key.
     *
     * @param \CDatabase_Schema_ForeignKeyConstraint $foreignKey the ForeignKey instance
     * @param \CDatabase_Schema_Table|string         $table      the name of the table on which the foreign key is to be created
     *
     * @return void
     */
    public function createForeignKey(CDatabase_Schema_ForeignKeyConstraint $foreignKey, $table) {
        $this->execSql($this->platform->getCreateForeignKeySQL($foreignKey, $table));
    }

    /**
     * Creates a new view.
     *
     * @param \CDatabase_Schema_View $view
     *
     * @return void
     */
    public function createView(CDatabase_Schema_View $view) {
        $this->execSql($this->platform->getCreateViewSQL($view->getQuotedName($this->platform), $view->getSql()));
    }

    /* dropAndCreate*() Methods */

    /**
     * Drops and creates a constraint.
     *
     * @param \CDatabase_Schema_Constraint   $constraint
     * @param \CDatabase_Schema_Table|string $table
     *
     * @return void
     *
     * @see dropConstraint()
     * @see createConstraint()
     */
    public function dropAndCreateConstraint(CDatabase_Schema_Constraint $constraint, $table) {
        $this->tryMethod('dropConstraint', $constraint, $table);
        $this->createConstraint($constraint, $table);
    }

    /**
     * Drops and creates a new index on a table.
     *
     * @param \CDatabase_Schema_Index        $index
     * @param \CDatabase_Schema_Table|string $table the name of the table on which the index is to be created
     *
     * @return void
     */
    public function dropAndCreateIndex(CDatabase_Schema_Index $index, $table) {
        $this->tryMethod('dropIndex', $index->getQuotedName($this->platform), $table);
        $this->createIndex($index, $table);
    }

    /**
     * Drops and creates a new foreign key.
     *
     * @param \CDatabase_Schema_ForeignKeyConstraint $foreignKey an associative array that defines properties of the foreign key to be created
     * @param \CDatabase_Schema_Table|string         $table      the name of the table on which the foreign key is to be created
     *
     * @return void
     */
    public function dropAndCreateForeignKey(CDatabase_Schema_ForeignKeyConstraint $foreignKey, $table) {
        $this->tryMethod('dropForeignKey', $foreignKey, $table);
        $this->createForeignKey($foreignKey, $table);
    }

    /**
     * Drops and create a new sequence.
     *
     * @param \CDatabase_Schema_Sequence $sequence
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\ConnectionException if something fails at database level
     */
    public function dropAndCreateSequence(CDatabase_Schema_Sequence $sequence) {
        $this->tryMethod('dropSequence', $sequence->getQuotedName($this->platform));
        $this->createSequence($sequence);
    }

    /**
     * Drops and creates a new table.
     *
     * @param \CDatabase_Schema_Table $table
     *
     * @return void
     */
    public function dropAndCreateTable(CDatabase_Schema_Table $table) {
        $this->tryMethod('dropTable', $table->getQuotedName($this->platform));
        $this->createTable($table);
    }

    /**
     * Drops and creates a new database.
     *
     * @param string $database the name of the database to create
     *
     * @return void
     */
    public function dropAndCreateDatabase($database) {
        $this->tryMethod('dropDatabase', $database);
        $this->createDatabase($database);
    }

    /**
     * Drops and creates a new view.
     *
     * @param \CDatabase_Schema_View $view
     *
     * @return void
     */
    public function dropAndCreateView(CDatabase_Schema_View $view) {
        $this->tryMethod('dropView', $view->getQuotedName($this->platform));
        $this->createView($view);
    }

    /* alterTable() Methods */

    /**
     * Alters an existing tables schema.
     *
     * @param \CDatabase_Schema_Table_Diff $tableDiff
     *
     * @return void
     */
    public function alterTable(CDatabase_Schema_Table_Diff $tableDiff) {
        $queries = $this->platform->getAlterTableSQL($tableDiff);
        if (is_array($queries) && count($queries)) {
            foreach ($queries as $ddlQuery) {
                $this->execSql($ddlQuery);
            }
        }
    }

    /**
     * Renames a given table to another name.
     *
     * @param string $name    the current name of the table
     * @param string $newName the new name of the table
     *
     * @return void
     */
    public function renameTable($name, $newName) {
        $tableDiff = new CDatabase_Schema_Table_Diff($name);
        $tableDiff->newName = $newName;
        $this->alterTable($tableDiff);
    }

    /**
     * Methods for filtering return values of list*() methods to convert
     * the native DBMS data definition to a portable Doctrine definition
     *
     * @param mixed $databases
     */

    /**
     * @param array $databases
     *
     * @return array
     */
    protected function getPortableDatabasesList($databases) {
        $list = [];
        foreach ($databases as $value) {
            if ($value = $this->getPortableDatabaseDefinition($value)) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * Converts a list of namespace names from the native DBMS data definition to a portable Doctrine definition.
     *
     * @param array $namespaces the list of namespace names in the native DBMS data definition
     *
     * @return array
     */
    protected function getPortableNamespacesList(array $namespaces) {
        $namespacesList = [];

        foreach ($namespaces as $namespace) {
            $namespacesList[] = $this->getPortableNamespaceDefinition($namespace);
        }

        return $namespacesList;
    }

    /**
     * @param array $database
     *
     * @return mixed
     */
    protected function getPortableDatabaseDefinition($database) {
        return $database;
    }

    /**
     * Converts a namespace definition from the native DBMS data definition to a portable Doctrine definition.
     *
     * @param array $namespace the native DBMS namespace definition
     *
     * @return mixed
     */
    protected function getPortableNamespaceDefinition(array $namespace) {
        return $namespace;
    }

    /**
     * @param array $functions
     *
     * @return array
     */
    protected function getPortableFunctionsList($functions) {
        $list = [];
        foreach ($functions as $value) {
            if ($value = $this->getPortableFunctionDefinition($value)) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * @param array $function
     *
     * @return mixed
     */
    protected function getPortableFunctionDefinition($function) {
        return $function;
    }

    /**
     * @param array $triggers
     *
     * @return array
     */
    protected function getPortableTriggersList($triggers) {
        $list = [];
        foreach ($triggers as $value) {
            if ($value = $this->getPortableTriggerDefinition($value)) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * @param array $trigger
     *
     * @return mixed
     */
    protected function getPortableTriggerDefinition($trigger) {
        return $trigger;
    }

    /**
     * @param array $sequences
     *
     * @return array
     */
    protected function getPortableSequencesList($sequences) {
        $list = [];
        foreach ($sequences as $value) {
            if ($value = $this->getPortableSequenceDefinition($value)) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * @param array $sequence
     *
     * @return \CDatabase_Schema_Sequence
     *
     * @throws \CDatabase_Exception
     */
    protected function getPortableSequenceDefinition($sequence) {
        throw CDatabase_Exception::notSupported('Sequences');
    }

    /**
     * Independent of the database the keys of the column list result are lowercased.
     *
     * The name of the created column instance however is kept in its case.
     *
     * @param string $table        the name of the table
     * @param string $database
     * @param array  $tableColumns
     *
     * @return array
     */
    protected function getPortableTableColumnList($table, $database, $tableColumns) {
        $eventDispatcher = $this->platform->getEventDispatcher();

        $list = [];
        foreach ($tableColumns as $tableColumn) {
            $column = null;
            $defaultPrevented = false;

            if (null !== $eventDispatcher) {
                $eventArgs = new CDatabase_Event_Schema_OnColumnDefinition($tableColumn, $table, $database, $this->db);
                $eventDispatcher->dispatch($eventArgs);
                $defaultPrevented = $eventArgs->isDefaultPrevented();
                $column = $eventArgs->getColumn();
            }

            if (!$defaultPrevented) {
                $column = $this->getPortableTableColumnDefinition($tableColumn);
            }
            if ($column) {
                $name = strtolower($column->getQuotedName($this->platform));
                $list[$name] = $column;
            }
        }

        return $list;
    }

    /**
     * Gets Table Column Definition.
     *
     * @param array $tableColumn
     *
     * @return \CDatabase_Schema_Column
     */
    abstract protected function getPortableTableColumnDefinition($tableColumn);

    /**
     * Aggregates and groups the index results according to the required data result.
     *
     * @param array       $tableIndexRows
     * @param string|null $tableName
     *
     * @return array
     */
    protected function getPortableTableIndexesList($tableIndexRows, $tableName = null) {
        $result = [];
        foreach ($tableIndexRows as $tableIndex) {
            $indexName = $keyName = $tableIndex['key_name'];
            if ($tableIndex['primary']) {
                $keyName = 'primary';
            }
            $keyName = strtolower($keyName);

            if (!isset($result[$keyName])) {
                $result[$keyName] = [
                    'name' => $indexName,
                    'columns' => [$tableIndex['column_name']],
                    'unique' => $tableIndex['non_unique'] ? false : true,
                    'primary' => $tableIndex['primary'],
                    'flags' => isset($tableIndex['flags']) ? $tableIndex['flags'] : [],
                    'options' => isset($tableIndex['where']) ? ['where' => $tableIndex['where']] : [],
                ];
            } else {
                $result[$keyName]['columns'][] = $tableIndex['column_name'];
            }
        }

        $eventDispatcher = $this->platform->getEventDispatcher();

        $indexes = [];
        foreach ($result as $indexKey => $data) {
            $index = null;
            $defaultPrevented = false;

            if (null !== $eventDispatcher) {
                $eventArgs = new CDatabase_Event_Schema_OnIndexDefinition($data, $tableName, $this->db);
                $eventDispatcher->dispatch($eventArgs);

                $defaultPrevented = $eventArgs->isDefaultPrevented();
                $index = $eventArgs->getIndex();
            }

            if (!$defaultPrevented) {
                $index = new CDatabase_Schema_Index($data['name'], $data['columns'], $data['unique'], $data['primary'], $data['flags'], $data['options']);
            }

            if ($index) {
                $indexes[$indexKey] = $index;
            }
        }

        return $indexes;
    }

    /**
     * @param array $tables
     *
     * @return array
     */
    protected function getPortableTablesList($tables) {
        $list = [];
        foreach ($tables as $value) {
            if ($value = $this->getPortableTableDefinition($value)) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * @param array $table
     *
     * @return array
     */
    protected function getPortableTableDefinition($table) {
        return $table;
    }

    /**
     * @param array $users
     *
     * @return array
     */
    protected function getPortableUsersList($users) {
        $list = [];
        foreach ($users as $value) {
            if ($value = $this->getPortableUserDefinition($value)) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * @param array $user
     *
     * @return mixed
     */
    protected function getPortableUserDefinition($user) {
        return $user;
    }

    /**
     * @param array $views
     *
     * @return array
     */
    protected function getPortableViewsList($views) {
        $list = [];
        foreach ($views as $value) {
            if ($view = $this->getPortableViewDefinition($value)) {
                $viewName = strtolower($view->getQuotedName($this->platform));
                $list[$viewName] = $view;
            }
        }

        return $list;
    }

    /**
     * @param array $view
     *
     * @return mixed
     */
    protected function getPortableViewDefinition($view) {
        return false;
    }

    /**
     * @param array $tableForeignKeys
     *
     * @return array
     */
    protected function getPortableTableForeignKeysList($tableForeignKeys) {
        $list = [];
        foreach ($tableForeignKeys as $value) {
            if ($value = $this->getPortableTableForeignKeyDefinition($value)) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * @param array $tableForeignKey
     *
     * @return mixed
     */
    protected function getPortableTableForeignKeyDefinition($tableForeignKey) {
        return $tableForeignKey;
    }

    /**
     * @param array|string $sql
     *
     * @return void
     */
    protected function execSql($sql) {
        foreach ((array) $sql as $query) {
            $this->db->query($query);
        }
    }

    /**
     * Creates a schema instance for the current database.
     *
     * @return CDatabase_Schema
     */
    public function createSchema() {
        $namespaces = [];

        if ($this->platform->supportsSchemas()) {
            $namespaces = $this->listNamespaceNames();
        }

        $sequences = [];

        if ($this->platform->supportsSequences()) {
            $sequences = $this->listSequences();
        }

        $tables = $this->listTables();

        return new CDatabase_Schema($tables, $sequences, $this->createSchemaConfig(), $namespaces);
    }

    /**
     * Creates the configuration for this schema.
     *
     * @return \CDatabase_Schema_Config
     */
    public function createSchemaConfig() {
        $schemaConfig = new CDatabase_Schema_Config();
        $schemaConfig->setMaxIdentifierLength($this->platform->getMaxIdentifierLength());

        $searchPaths = $this->getSchemaSearchPaths();

        if (isset($searchPaths[0])) {
            $schemaConfig->setName($searchPaths[0]);
        }

        $params = $this->db->config();
        if (!isset($params['defaultTableOptions'])) {
            $params['defaultTableOptions'] = [];
        }
        if (!isset($params['defaultTableOptions']['charset']) && isset($params['charset'])) {
            $params['defaultTableOptions']['charset'] = $params['charset'];
        }
        $schemaConfig->setDefaultTableOptions($params['defaultTableOptions']);

        return $schemaConfig;
    }

    /**
     * The search path for namespaces in the currently connected database.
     *
     * The first entry is usually the default namespace in the Schema. All
     * further namespaces contain tables/sequences which can also be addressed
     * with a short, not full-qualified name.
     *
     * For databases that don't support subschema/namespaces this method
     * returns the name of the currently connected database.
     *
     * @return array
     */
    public function getSchemaSearchPaths() {
        return [$this->db->getDatabase()];
    }

    /**
     * Given a table comment this method tries to extract a typehint for Doctrine Type, or returns
     * the type given as default.
     *
     * @param string $comment
     * @param string $currentType
     *
     * @return string
     */
    public function extractDoctrineTypeFromComment($comment, $currentType) {
        if (preg_match("(\(DC2Type:(((?!\)).)+)\))", $comment, $match)) {
            $currentType = $match[1];
        }

        return $currentType;
    }

    /**
     * @param string $comment
     * @param string $type
     *
     * @return string
     */
    public function removeDoctrineTypeFromComment($comment, $type) {
        return str_replace('(DC2Type:' . $type . ')', '', $comment);
    }

    public function getDatabaseRowCount() {
        throw CDatabase_Exception::notSupported('getDatabaseRowCount');
    }

    public function getDatabaseSize() {
        throw CDatabase_Exception::notSupported('getDatabaseSize');
    }

    public function isSuperUser() {
        throw CDatabase_Exception::notSupported('isSuperUser');
    }
}
