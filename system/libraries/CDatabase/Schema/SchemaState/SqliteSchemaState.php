<?php

class CDatabase_Schema_SchemaState_SqliteSchemaState extends CDatabase_Schema_SchemaState {
    /**
     * Dump the database's schema into a file.
     *
     * @param \CDatabase_Connection $connection
     * @param string                $path
     *
     * @return void
     */
    public function dump(CDatabase_Connection $connection, $path) {
        c::with($process = $this->makeProcess(
            $this->baseCommand() . ' .schema'
        ))->setTimeout(null)->mustRun(null, array_merge($this->baseVariables($this->connection->getConfig()), [

        ]));

        $migrations = c::collect(preg_split("/\r\n|\n|\r/", $process->getOutput()))->filter(function ($line) {
            return stripos($line, 'sqlite_sequence') === false
                   && strlen($line) > 0;
        })->all();

        CFile::put($path, implode(PHP_EOL, $migrations) . PHP_EOL);

        $this->appendMigrationData($path);
    }

    /**
     * Append the migration data to the schema dump.
     *
     * @param string $path
     *
     * @return void
     */
    protected function appendMigrationData(string $path) {
        c::with($process = $this->makeProcess(
            $this->baseCommand() . ' ".dump \'' . $this->migrationTable . '\'"'
        ))->mustRun(null, array_merge($this->baseVariables($this->connection->getConfig()), [

        ]));

        $migrations = c::collect(preg_split("/\r\n|\n|\r/", $process->getOutput()))->filter(function ($line) {
            return preg_match('/^\s*(--|INSERT\s)/iu', $line) === 1
                   && strlen($line) > 0;
        })->all();

        CFile::append($path, implode(PHP_EOL, $migrations) . PHP_EOL);
    }

    /**
     * Load the given schema file into the database.
     *
     * @param string $path
     *
     * @return void
     */
    public function load($path) {
        if ($this->connection->getDatabaseName() === ':memory:') {
            $this->connection->getPdo()->exec(CFile::get($path));

            return;
        }

        $process = $this->makeProcess($this->baseCommand() . ' < "${:CF_LOAD_PATH}"');

        $process->mustRun(null, array_merge($this->baseVariables($this->connection->getConfig()), [
            'CF_LOAD_PATH' => $path,
        ]));
    }

    /**
     * Get the base sqlite command arguments as a string.
     *
     * @return string
     */
    protected function baseCommand() {
        return 'sqlite3 "${:CF_LOAD_DATABASE}"';
    }

    /**
     * Get the base variables for a dump / load command.
     *
     * @param array $config
     *
     * @return array
     */
    protected function baseVariables(array $config) {
        return [
            'CF_LOAD_DATABASE' => $config['database'],
        ];
    }
}
