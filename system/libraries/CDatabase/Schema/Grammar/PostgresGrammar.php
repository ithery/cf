<?php


class CDatabase_Schema_Grammar_PostgresGrammar extends CDatabase_Schema_Grammar {
    /**
     * If this Grammar supports schema changes wrapped in a transaction.
     *
     * @var bool
     */
    protected $transactions = true;

    /**
     * The possible column modifiers.
     *
     * @var string[]
     */
    protected $modifiers = ['Collate', 'Nullable', 'Default', 'VirtualAs', 'StoredAs', 'GeneratedAs', 'Increment'];

    /**
     * The columns available as serials.
     *
     * @var string[]
     */
    protected $serials = ['bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'];

    /**
     * The commands to be executed outside of create or alter command.
     *
     * @var string[]
     */
    protected $fluentCommands = ['AutoIncrementStartingValues', 'Comment'];

    /**
     * Compile a create database command.
     *
     * @param  string  $name
     * @param  \CDatabase_Connection  $connection
     * @return string
     */
    public function compileCreateDatabase($name, $connection) {
        return sprintf(
            'create database %s encoding %s',
            $this->wrapValue($name),
            $this->wrapValue($connection->getConfig('charset')),
        );
    }

    /**
     * Compile a drop database if exists command.
     *
     * @param  string  $name
     * @return string
     */
    public function compileDropDatabaseIfExists($name) {
        return sprintf(
            'drop database if exists %s',
            $this->wrapValue($name)
        );
    }

    /**
     * Compile the query to determine if a table exists.
     *
     * @return string
     */
    public function compileTableExists() {
        return "select * from information_schema.tables where table_catalog = ? and table_schema = ? and table_name = ? and table_type = 'BASE TABLE'";
    }

    /**
     * Compile the query to determine the list of columns.
     *
     * @return string
     */
    public function compileColumnListing() {
        return 'select column_name from information_schema.columns where table_catalog = ? and table_schema = ? and table_name = ?';
    }

    /**
     * Compile a create table command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileCreate(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            '%s table %s (%s)',
            $blueprint->temporary ? 'create temporary' : 'create',
            $this->wrapTable($blueprint),
            implode(', ', $this->getColumns($blueprint))
        );
    }

    /**
     * Compile a column addition command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileAdd(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'alter table %s %s',
            $this->wrapTable($blueprint),
            implode(', ', $this->prefixArray('add column', $this->getColumns($blueprint)))
        );
    }

    /**
     * Compile the auto-incrementing column starting values.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileAutoIncrementStartingValues(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        if ($command->column->autoIncrement
            && $value = $command->column->get('startingValue', $command->column->get('from'))
        ) {
            return 'alter sequence '.$blueprint->getTable().'_'.$command->column->name.'_seq restart with '.$value;
        }
    }

    /**
     * Compile a rename column command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @param  \CDatabase_Connection  $connection
     * @return array|string
     */
    public function compileRenameColumn(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command, CDatabase_Connection $connection) {
        return $connection->usingNativeSchemaOperations()
            ? sprintf(
                'alter table %s rename column %s to %s',
                $this->wrapTable($blueprint),
                $this->wrap($command->from),
                $this->wrap($command->to)
            )
            : parent::compileRenameColumn($blueprint, $command, $connection);
    }

    /**
     * Compile a change column command into a series of SQL statements.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @param  \CDatabase_Connection  $connection
     * @return array|string
     *
     * @throws \RuntimeException
     */
    public function compileChange(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command, CDatabase_Connection $connection) {
        if (! $connection->usingNativeSchemaOperations()) {
            return parent::compileChange($blueprint, $command, $connection);
        }

        $columns = [];

        foreach ($blueprint->getChangedColumns() as $column) {
            $changes = ['type '.$this->getType($column).$this->modifyCollate($blueprint, $column)];

            foreach ($this->modifiers as $modifier) {
                if ($modifier === 'Collate') {
                    continue;
                }

                if (method_exists($this, $method = "modify{$modifier}")) {
                    $constraints = (array) $this->{$method}($blueprint, $column);

                    foreach ($constraints as $constraint) {
                        $changes[] = $constraint;
                    }
                }
            }

            $columns[] = implode(', ', $this->prefixArray('alter column '.$this->wrap($column), $changes));
        }

        return 'alter table '.$this->wrapTable($blueprint).' '.implode(', ', $columns);
    }

    /**
     * Compile a primary key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compilePrimary(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $columns = $this->columnize($command->columns);

        return 'alter table '.$this->wrapTable($blueprint)." add primary key ({$columns})";
    }

    /**
     * Compile a unique key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileUnique(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $sql = sprintf(
            'alter table %s add constraint %s unique (%s)',
            $this->wrapTable($blueprint),
            $this->wrap($command->index),
            $this->columnize($command->columns)
        );

        if (! is_null($command->deferrable)) {
            $sql .= $command->deferrable ? ' deferrable' : ' not deferrable';
        }

        if ($command->deferrable && ! is_null($command->initiallyImmediate)) {
            $sql .= $command->initiallyImmediate ? ' initially immediate' : ' initially deferred';
        }

        return $sql;
    }

    /**
     * Compile a plain index key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'create index %s on %s%s (%s)',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $command->algorithm ? ' using '.$command->algorithm : '',
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a fulltext index key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     *
     * @throws \RuntimeException
     */
    public function compileFulltext(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $language = $command->language ?: 'english';

        $columns = array_map(function ($column) use ($language) {
            return "to_tsvector({$this->quoteString($language)}, {$this->wrap($column)})";
        }, $command->columns);

        return sprintf(
            'create index %s on %s using gin ((%s))',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            implode(' || ', $columns)
        );
    }

    /**
     * Compile a spatial index key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileSpatialIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $command->algorithm = 'gist';

        return $this->compileIndex($blueprint, $command);
    }

    /**
     * Compile a foreign key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileForeign(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $sql = parent::compileForeign($blueprint, $command);

        if (! is_null($command->deferrable)) {
            $sql .= $command->deferrable ? ' deferrable' : ' not deferrable';
        }

        if ($command->deferrable && ! is_null($command->initiallyImmediate)) {
            $sql .= $command->initiallyImmediate ? ' initially immediate' : ' initially deferred';
        }

        if (! is_null($command->notValid)) {
            $sql .= ' not valid';
        }

        return $sql;
    }

    /**
     * Compile a drop table command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDrop(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return 'drop table '.$this->wrapTable($blueprint);
    }

    /**
     * Compile a drop table (if exists) command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDropIfExists(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return 'drop table if exists '.$this->wrapTable($blueprint);
    }

    /**
     * Compile the SQL needed to drop all tables.
     *
     * @param  array  $tables
     * @return string
     */
    public function compileDropAllTables($tables) {
        return 'drop table '.implode(',', $this->escapeNames($tables)).' cascade';
    }

    /**
     * Compile the SQL needed to drop all views.
     *
     * @param  array  $views
     * @return string
     */
    public function compileDropAllViews($views) {
        return 'drop view '.implode(',', $this->escapeNames($views)).' cascade';
    }

    /**
     * Compile the SQL needed to drop all types.
     *
     * @param  array  $types
     * @return string
     */
    public function compileDropAllTypes($types) {
        return 'drop type '.implode(',', $this->escapeNames($types)).' cascade';
    }

    /**
     * Compile the SQL needed to retrieve all table names.
     *
     * @param  string|array  $searchPath
     * @return string
     */
    public function compileGetAllTables($searchPath) {
        return "select tablename, concat('\"', schemaname, '\".\"', tablename, '\"') as qualifiedname from pg_catalog.pg_tables where schemaname in ('".implode("','", (array) $searchPath)."')";
    }

    /**
     * Compile the SQL needed to retrieve all view names.
     *
     * @param  string|array  $searchPath
     * @return string
     */
    public function compileGetAllViews($searchPath) {
        return "select viewname, concat('\"', schemaname, '\".\"', viewname, '\"') as qualifiedname from pg_catalog.pg_views where schemaname in ('".implode("','", (array) $searchPath)."')";
    }

    /**
     * Compile the SQL needed to retrieve all type names.
     *
     * @return string
     */
    public function compileGetAllTypes() {
        return 'select distinct pg_type.typname from pg_type inner join pg_enum on pg_enum.enumtypid = pg_type.oid';
    }

    /**
     * Compile a drop column command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDropColumn(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $columns = $this->prefixArray('drop column', $this->wrapArray($command->columns));

        return 'alter table '.$this->wrapTable($blueprint).' '.implode(', ', $columns);
    }

    /**
     * Compile a drop primary key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDropPrimary(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $index = $this->wrap("{$blueprint->getTable()}_pkey");

        return 'alter table '.$this->wrapTable($blueprint)." drop constraint {$index}";
    }

    /**
     * Compile a drop unique key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDropUnique(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $index = $this->wrap($command->index);

        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
    }

    /**
     * Compile a drop index command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDropIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return "drop index {$this->wrap($command->index)}";
    }

    /**
     * Compile a drop fulltext index command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDropFullText(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return $this->compileDropIndex($blueprint, $command);
    }

    /**
     * Compile a drop spatial index command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDropSpatialIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return $this->compileDropIndex($blueprint, $command);
    }

    /**
     * Compile a drop foreign key command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileDropForeign(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $index = $this->wrap($command->index);

        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
    }

    /**
     * Compile a rename table command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileRename(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $from = $this->wrapTable($blueprint);

        return "alter table {$from} rename to ".$this->wrapTable($command->to);
    }

    /**
     * Compile a rename index command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileRenameIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'alter index %s rename to %s',
            $this->wrap($command->from),
            $this->wrap($command->to)
        );
    }

    /**
     * Compile the command to enable foreign key constraints.
     *
     * @return string
     */
    public function compileEnableForeignKeyConstraints() {
        return 'SET CONSTRAINTS ALL IMMEDIATE;';
    }

    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints() {
        return 'SET CONSTRAINTS ALL DEFERRED;';
    }

    /**
     * Compile a comment command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileComment(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        if (! is_null($comment = $command->column->comment) || $command->column->change) {
            return sprintf(
                'comment on column %s.%s is %s',
                $this->wrapTable($blueprint),
                $this->wrap($command->column->name),
                is_null($comment) ? 'NULL' : "'".str_replace("'", "''", $comment)."'"
            );
        }
    }

    /**
     * Compile a table comment command.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $command
     * @return string
     */
    public function compileTableComment(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'comment on table %s is %s',
            $this->wrapTable($blueprint),
            "'".str_replace("'", "''", $command->comment)."'"
        );
    }

    /**
     * Quote-escape the given tables, views, or types.
     *
     * @param  array  $names
     * @return array
     */
    public function escapeNames($names) {
        return array_map(static function ($name) {
            return '"'.c::collect(explode('.', $name))
                ->map(function ($segment) {
                    return trim($segment, '\'"');
                })
                ->implode('"."').'"';
        }, $names);
    }

    /**
     * Create the column definition for a char type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeChar(CBase_Fluent $column)
    {
        if ($column->length) {
            return "char({$column->length})";
        }

        return 'char';
    }

    /**
     * Create the column definition for a string type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeString(CBase_Fluent $column)
    {
        if ($column->length) {
            return "varchar({$column->length})";
        }

        return 'varchar';
    }

    /**
     * Create the column definition for a tiny text type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeTinyText(CBase_Fluent $column)
    {
        return 'varchar(255)';
    }

    /**
     * Create the column definition for a text type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeText(CBase_Fluent $column)
    {
        return 'text';
    }

    /**
     * Create the column definition for a medium text type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeMediumText(CBase_Fluent $column)
    {
        return 'text';
    }

    /**
     * Create the column definition for a long text type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeLongText(CBase_Fluent $column)
    {
        return 'text';
    }

    /**
     * Create the column definition for an integer type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeInteger(CBase_Fluent $column)
    {
        return $column->autoIncrement && is_null($column->generatedAs) ? 'serial' : 'integer';
    }

    /**
     * Create the column definition for a big integer type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeBigInteger(CBase_Fluent $column)
    {
        return $column->autoIncrement && is_null($column->generatedAs) ? 'bigserial' : 'bigint';
    }

    /**
     * Create the column definition for a medium integer type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeMediumInteger(CBase_Fluent $column)
    {
        return $this->typeInteger($column);
    }

    /**
     * Create the column definition for a tiny integer type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeTinyInteger(CBase_Fluent $column)
    {
        return $this->typeSmallInteger($column);
    }

    /**
     * Create the column definition for a small integer type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeSmallInteger(CBase_Fluent $column)
    {
        return $column->autoIncrement && is_null($column->generatedAs) ? 'smallserial' : 'smallint';
    }

    /**
     * Create the column definition for a float type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeFloat(CBase_Fluent $column)
    {
        return $this->typeDouble($column);
    }

    /**
     * Create the column definition for a double type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeDouble(CBase_Fluent $column)
    {
        return 'double precision';
    }

    /**
     * Create the column definition for a real type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeReal(CBase_Fluent $column)
    {
        return 'real';
    }

    /**
     * Create the column definition for a decimal type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeDecimal(CBase_Fluent $column)
    {
        return "decimal({$column->total}, {$column->places})";
    }

    /**
     * Create the column definition for a boolean type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeBoolean(CBase_Fluent $column)
    {
        return 'boolean';
    }

    /**
     * Create the column definition for an enumeration type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeEnum(CBase_Fluent $column)
    {
        return sprintf(
            'varchar(255) check ("%s" in (%s))',
            $column->name,
            $this->quoteString($column->allowed)
        );
    }

    /**
     * Create the column definition for a json type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeJson(CBase_Fluent $column)
    {
        return 'json';
    }

    /**
     * Create the column definition for a jsonb type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeJsonb(CBase_Fluent $column)
    {
        return 'jsonb';
    }

    /**
     * Create the column definition for a date type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeDate(CBase_Fluent $column)
    {
        return 'date';
    }

    /**
     * Create the column definition for a date-time type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeDateTime(CBase_Fluent $column)
    {
        return $this->typeTimestamp($column);
    }

    /**
     * Create the column definition for a date-time (with time zone) type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeDateTimeTz(CBase_Fluent $column)
    {
        return $this->typeTimestampTz($column);
    }

    /**
     * Create the column definition for a time type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeTime(CBase_Fluent $column)
    {
        return 'time'.(is_null($column->precision) ? '' : "($column->precision)").' without time zone';
    }

    /**
     * Create the column definition for a time (with time zone) type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeTimeTz(CBase_Fluent $column)
    {
        return 'time'.(is_null($column->precision) ? '' : "($column->precision)").' with time zone';
    }

    /**
     * Create the column definition for a timestamp type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeTimestamp(CBase_Fluent $column)
    {
        if ($column->useCurrent) {
            $column->default(new CDatabase_Query_Expression('CURRENT_TIMESTAMP'));
        }

        return 'timestamp'.(is_null($column->precision) ? '' : "($column->precision)").' without time zone';
    }

    /**
     * Create the column definition for a timestamp (with time zone) type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeTimestampTz(CBase_Fluent $column)
    {
        if ($column->useCurrent) {
            $column->default(new CDatabase_Query_Expression('CURRENT_TIMESTAMP'));
        }

        return 'timestamp'.(is_null($column->precision) ? '' : "($column->precision)").' with time zone';
    }

    /**
     * Create the column definition for a year type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeYear(CBase_Fluent $column)
    {
        return $this->typeInteger($column);
    }

    /**
     * Create the column definition for a binary type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeBinary(CBase_Fluent $column)
    {
        return 'bytea';
    }

    /**
     * Create the column definition for a uuid type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeUuid(CBase_Fluent $column)
    {
        return 'uuid';
    }

    /**
     * Create the column definition for an IP address type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeIpAddress(CBase_Fluent $column)
    {
        return 'inet';
    }

    /**
     * Create the column definition for a MAC address type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeMacAddress(CBase_Fluent $column)
    {
        return 'macaddr';
    }

    /**
     * Create the column definition for a spatial Geometry type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeGeometry(CBase_Fluent $column)
    {
        return $this->formatPostGisType('geometry', $column);
    }

    /**
     * Create the column definition for a spatial Point type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typePoint(CBase_Fluent $column)
    {
        return $this->formatPostGisType('point', $column);
    }

    /**
     * Create the column definition for a spatial LineString type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeLineString(CBase_Fluent $column)
    {
        return $this->formatPostGisType('linestring', $column);
    }

    /**
     * Create the column definition for a spatial Polygon type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typePolygon(CBase_Fluent $column)
    {
        return $this->formatPostGisType('polygon', $column);
    }

    /**
     * Create the column definition for a spatial GeometryCollection type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeGeometryCollection(CBase_Fluent $column)
    {
        return $this->formatPostGisType('geometrycollection', $column);
    }

    /**
     * Create the column definition for a spatial MultiPoint type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeMultiPoint(CBase_Fluent $column)
    {
        return $this->formatPostGisType('multipoint', $column);
    }

    /**
     * Create the column definition for a spatial MultiLineString type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    public function typeMultiLineString(CBase_Fluent $column)
    {
        return $this->formatPostGisType('multilinestring', $column);
    }

    /**
     * Create the column definition for a spatial MultiPolygon type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeMultiPolygon(CBase_Fluent $column)
    {
        return $this->formatPostGisType('multipolygon', $column);
    }

    /**
     * Create the column definition for a spatial MultiPolygonZ type.
     *
     * @param  \CBase_Fluent  $column
     * @return string
     */
    protected function typeMultiPolygonZ(CBase_Fluent $column)
    {
        return $this->formatPostGisType('multipolygonz', $column);
    }

    /**
     * Format the column definition for a PostGIS spatial type.
     *
     * @param  string  $type
     * @param  \CBase_Fluent  $column
     * @return string
     */
    private function formatPostGisType($type, CBase_Fluent $column)
    {
        if ($column->isGeometry === null) {
            return sprintf('geography(%s, %s)', $type, $column->projection ?? '4326');
        }

        if ($column->projection !== null) {
            return sprintf('geometry(%s, %s)', $type, $column->projection);
        }

        return "geometry({$type})";
    }

    /**
     * Get the SQL for a collation column modifier.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $column
     * @return string|null
     */
    protected function modifyCollate(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column)
    {
        if (! is_null($column->collation)) {
            return ' collate '.$this->wrapValue($column->collation);
        }
    }

    /**
     * Get the SQL for a nullable column modifier.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $column
     * @return string|null
     */
    protected function modifyNullable(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column)
    {
        if ($column->change) {
            return $column->nullable ? 'drop not null' : 'set not null';
        }

        return $column->nullable ? ' null' : ' not null';
    }

    /**
     * Get the SQL for a default column modifier.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $column
     * @return string|null
     */
    protected function modifyDefault(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column)
    {
        if ($column->change) {
            return is_null($column->default) ? 'drop default' : 'set default '.$this->getDefaultValue($column->default);
        }

        if (! is_null($column->default)) {
            return ' default '.$this->getDefaultValue($column->default);
        }
    }

    /**
     * Get the SQL for an auto-increment column modifier.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $column
     * @return string|null
     */
    protected function modifyIncrement(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column)
    {
        if (! $column->change
            && (in_array($column->type, $this->serials) || ($column->generatedAs !== null))
            && $column->autoIncrement
        ) {
            return ' primary key';
        }
    }

    /**
     * Get the SQL for a generated virtual column modifier.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $column
     * @return string|null
     */
    protected function modifyVirtualAs(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column)
    {
        if ($column->change) {
            if (array_key_exists('virtualAs', $column->getAttributes())) {
                return is_null($column->virtualAs)
                    ? 'drop expression if exists'
                    : throw new LogicException('This database driver does not support modifying generated columns.');
            }

            return null;
        }

        if (! is_null($column->virtualAs)) {
            return " generated always as ({$column->virtualAs})";
        }
    }

    /**
     * Get the SQL for a generated stored column modifier.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $column
     * @return string|null
     */
    protected function modifyStoredAs(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if ($column->change) {
            if (array_key_exists('storedAs', $column->getAttributes())) {
                return is_null($column->storedAs)
                    ? 'drop expression if exists'
                    : throw new LogicException('This database driver does not support modifying generated columns.');
            }

            return null;
        }

        if (! is_null($column->storedAs)) {
            return " generated always as ({$column->storedAs}) stored";
        }
    }

    /**
     * Get the SQL for an identity column modifier.
     *
     * @param  \CDatabase_Schema_Blueprint  $blueprint
     * @param  \CBase_Fluent  $column
     * @return string|array|null
     */
    protected function modifyGeneratedAs(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column)
    {
        $sql = null;

        if (! is_null($column->generatedAs)) {
            $sql = sprintf(
                ' generated %s as identity%s',
                $column->always ? 'always' : 'by default',
                ! is_bool($column->generatedAs) && ! empty($column->generatedAs) ? " ({$column->generatedAs})" : ''
            );
        }

        if ($column->change) {
            $changes = ['drop identity if exists'];

            if (! is_null($sql)) {
                $changes[] = 'add '.$sql;
            }

            return $changes;
        }

        return $sql;
    }
}
