<?php

class CConsole_Command_Database_ShowCommand extends CConsole_Command_Database_AbstractInspectionCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:show {--database= : The database connection}
                {--json : Output the database information as JSON}
                {--counts : Show the table row count <bg=red;options=bold> Note: This can be slow on large databases </>}
                {--views : Show the database views <bg=red;options=bold> Note: This can be slow on large databases </>}
                {--types : Show the user defined types}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display information about the given database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $connections = CDatabase::manager();
        $connection = $connections->connection($database = $this->input->getOption('database'));

        $schema = $connection->getSchemaBuilder();

        $data = [
            'platform' => [
                'config' => $this->getConfigFromDatabase($database),
                'name' => $this->getConnectionName($connection, $database),
                'version' => $connection->getServerVersion(),
                'open_connections' => $this->getConnectionCount($connection),
            ],
            'tables' => $this->tables($connection, $schema),
        ];

        if ($this->option('views')) {
            $data['views'] = $this->views($connection, $schema);
        }

        if ($this->option('types')) {
            $data['types'] = $this->types($connection, $schema);
        }

        $this->display($data);

        return 0;
    }

    /**
     * Get information regarding the tables within the database.
     *
     * @param \CDatabase_ConnectionInterface $connection
     * @param \CDatabase_Schema_Builder      $schema
     *
     * @return \CCollection
     */
    protected function tables(CDatabase_ConnectionInterface $connection, CDatabase_Schema_Builder $schema) {
        return c::collect($schema->getTables())->map(fn ($table) => [
            'table' => $table['name'],
            'schema' => $table['schema'],
            'size' => $table['size'],
            'rows' => $this->option('counts') ? $connection->table($table['name'])->count() : null,
            'engine' => $table['engine'],
            'collation' => $table['collation'],
            'comment' => $table['comment'],
        ]);
    }

    /**
     * Get information regarding the views within the database.
     *
     * @param \CDatabase_ConnectionInterface $connection
     * @param \CDatabase_Schema_Builder      $schema
     *
     * @return \CCollection
     */
    protected function views(CDatabase_ConnectionInterface $connection, CDatabase_Schema_Builder $schema) {
        return c::collect($schema->getViews())
            ->reject(fn ($view) => c::str($view['name'])->startsWith(['pg_catalog', 'information_schema', 'spt_']))
            ->map(fn ($view) => [
                'view' => $view['name'],
                'schema' => $view['schema'],
                'rows' => $connection->table($view->getName())->count(),
            ]);
    }

    /**
     * Get information regarding the user-defined types within the database.
     *
     * @param \CDatabase_ConnectionInterface $connection
     * @param \CDatabase_Schema_Builder      $schema
     *
     * @return \CCollection
     */
    protected function types(CDatabase_ConnectionInterface $connection, CDatabase_Schema_Builder $schema) {
        return c::collect($schema->getTypes())
            ->map(fn ($type) => [
                'name' => $type['name'],
                'schema' => $type['schema'],
                'type' => $type['type'],
                'category' => $type['category'],
            ]);
    }

    /**
     * Render the database information.
     *
     * @param array $data
     *
     * @return void
     */
    protected function display(array $data) {
        $this->option('json') ? $this->displayJson($data) : $this->displayForCli($data);
    }

    /**
     * Render the database information as JSON.
     *
     * @param array $data
     *
     * @return void
     */
    protected function displayJson(array $data) {
        $this->output->writeln(json_encode($data));
    }

    /**
     * Render the database information formatted for the CLI.
     *
     * @param array $data
     *
     * @return void
     */
    protected function displayForCli(array $data) {
        $platform = $data['platform'];
        $tables = $data['tables'];
        $views = $data['views'] ?? null;
        $types = $data['types'] ?? null;

        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>' . $platform['name'] . '</>', $platform['version']);
        $this->components->twoColumnDetail('Database', carr::get($platform['config'], 'database'));
        $this->components->twoColumnDetail('Host', carr::get($platform['config'], 'host'));
        $this->components->twoColumnDetail('Port', carr::get($platform['config'], 'port'));
        $this->components->twoColumnDetail('Username', carr::get($platform['config'], 'username'));
        $this->components->twoColumnDetail('URL', carr::get($platform['config'], 'url'));
        $this->components->twoColumnDetail('Open Connections', $platform['open_connections']);
        $this->components->twoColumnDetail('Tables', $tables->count());

        if ($tableSizeSum = $tables->sum('size')) {
            $this->components->twoColumnDetail('Total Size', cnum::fileSize($tableSizeSum, 2));
        }

        $this->newLine();

        if ($tables->isNotEmpty()) {
            $hasSchema = !is_null($tables->first()['schema']);

            $this->components->twoColumnDetail(
                ($hasSchema ? '<fg=green;options=bold>Schema</> <fg=gray;options=bold>/</> ' : '') . '<fg=green;options=bold>Table</>',
                'Size' . ($this->option('counts') ? ' <fg=gray;options=bold>/</> <fg=yellow;options=bold>Rows</>' : '')
            );

            $tables->each(function ($table) {
                if ($tableSize = $table['size']) {
                    $tableSize = cnum::fileSize($tableSize, 2);
                }

                $this->components->twoColumnDetail(
                    ($table['schema'] ? $table['schema'] . ' <fg=gray;options=bold>/</> ' : '') . $table['table'] . ($this->output->isVerbose() ? ' <fg=gray>' . $table['engine'] . '</>' : null),
                    ($tableSize ?: '—') . ($this->option('counts') ? ' <fg=gray;options=bold>/</> <fg=yellow;options=bold>' . cnum::format($table['rows']) . '</>' : '')
                );

                if ($this->output->isVerbose()) {
                    if ($table['comment']) {
                        $this->components->bulletList([
                            $table['comment'],
                        ]);
                    }
                }
            });

            $this->newLine();
        }

        if ($views && $views->isNotEmpty()) {
            $hasSchema = !is_null($views->first()['schema']);

            $this->components->twoColumnDetail(
                ($hasSchema ? '<fg=green;options=bold>Schema</> <fg=gray;options=bold>/</> ' : '') . '<fg=green;options=bold>View</>',
                '<fg=green;options=bold>Rows</>'
            );

            $views->each(fn ($view) => $this->components->twoColumnDetail(
                ($view['schema'] ? $view['schema'] . ' <fg=gray;options=bold>/</> ' : '') . $view['view'],
                cnum::format($view['rows'])
            ));

            $this->newLine();
        }

        if ($types && $types->isNotEmpty()) {
            $hasSchema = !is_null($types->first()['schema']);

            $this->components->twoColumnDetail(
                ($hasSchema ? '<fg=green;options=bold>Schema</> <fg=gray;options=bold>/</> ' : '') . '<fg=green;options=bold>Type</>',
                '<fg=green;options=bold>Type</> <fg=gray;options=bold>/</> <fg=green;options=bold>Category</>'
            );

            $types->each(fn ($type) => $this->components->twoColumnDetail(
                ($type['schema'] ? $type['schema'] . ' <fg=gray;options=bold>/</> ' : '') . $type['name'],
                $type['type'] . ' <fg=gray;options=bold>/</> ' . $type['category']
            ));

            $this->newLine();
        }
    }
}
