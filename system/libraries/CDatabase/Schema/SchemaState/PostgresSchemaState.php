<?php

class CDatabase_Schema_SchemaState_PostgresSchemaState extends CDatabase_Schema_SchemaState {
    /**
     * Dump the database's schema into a file.
     *
     * @param \CDatabase_Connection $connection
     * @param string                $path
     *
     * @return void
     */
    public function dump(CDatabase_Connection $connection, $path) {
        $commands = c::collect([
            $this->baseDumpCommand() . ' --schema-only > ' . $path,
            $this->baseDumpCommand() . ' -t ' . $this->migrationTable . ' --data-only >> ' . $path,
        ]);

        $commands->map(function ($command, $path) {
            $this->makeProcess($command)->mustRun($this->output, array_merge($this->baseVariables($this->connection->getConfig()), [
                'CF_LOAD_PATH' => $path,
            ]));
        });
    }

    /**
     * Load the given schema file into the database.
     *
     * @param string $path
     *
     * @return void
     */
    public function load($path) {
        $command = 'pg_restore --no-owner --no-acl --clean --if-exists --host="${:CF_LOAD_HOST}" --port="${:CF_LOAD_PORT}" --username="${:CF_LOAD_USER}" --dbname="${:CF_LOAD_DATABASE}" "${:CF_LOAD_PATH}"';

        if (str_ends_with($path, '.sql')) {
            $command = 'psql --file="${:CF_LOAD_PATH}" --host="${:CF_LOAD_HOST}" --port="${:CF_LOAD_PORT}" --username="${:CF_LOAD_USER}" --dbname="${:CF_LOAD_DATABASE}"';
        }

        $process = $this->makeProcess($command);

        $process->mustRun(null, array_merge($this->baseVariables($this->connection->getConfig()), [
            'CF_LOAD_PATH' => $path,
        ]));
    }

    /**
     * Get the base dump command arguments for PostgreSQL as a string.
     *
     * @return string
     */
    protected function baseDumpCommand() {
        return 'pg_dump --no-owner --no-acl --host="${:CF_LOAD_HOST}" --port="${:CF_LOAD_PORT}" --username="${:CF_LOAD_USER}" --dbname="${:CF_LOAD_DATABASE}"';
    }

    /**
     * Get the base variables for a dump / load command.
     *
     * @param array $config
     *
     * @return array
     */
    protected function baseVariables(array $config) {
        if (!isset($config['host'])) {
            $config['host'] = '';
        }

        return [
            'CF_LOAD_HOST' => is_array($config['host']) ? $config['host'][0] : $config['host'],
            'CF_LOAD_PORT' => $config['port'],
            'CF_LOAD_USER' => $config['username'],
            'PGPASSWORD' => $config['password'],
            'CF_LOAD_DATABASE' => $config['database'],
        ];
    }
}
