<?php

use Symfony\Component\Process\Process;

class CDatabase_Schema_SchemaState_MySqlSchemaState extends CDatabase_Schema_SchemaState {
    /**
     * Dump the database's schema into a file.
     *
     * @param \CDatabase_Connection $connection
     * @param string                $path
     *
     * @return void
     */
    public function dump(CDatabase_Connection $connection, $path) {
        $this->executeDumpProcess($this->makeProcess(
            $this->baseDumpCommand() . ' --routines --result-file="${:CF_LOAD_PATH}" --no-data'
        ), $this->output, array_merge($this->baseVariables($this->connection->getConfig()), [
            'CF_LOAD_PATH' => $path,
        ]));

        $this->removeAutoIncrementingState($path);

        $this->appendMigrationData($path);
    }

    /**
     * Remove the auto-incrementing state from the given schema dump.
     *
     * @param string $path
     *
     * @return void
     */
    protected function removeAutoIncrementingState(string $path) {
        CFile::put($path, preg_replace(
            '/\s+AUTO_INCREMENT=[0-9]+/iu',
            '',
            CFile::get($path)
        ));
    }

    /**
     * Append the migration data to the schema dump.
     *
     * @param string $path
     *
     * @return void
     */
    protected function appendMigrationData(string $path) {
        $process = $this->executeDumpProcess($this->makeProcess(
            $this->baseDumpCommand() . ' ' . $this->migrationTable . ' --no-create-info --skip-extended-insert --skip-routines --compact'
        ), null, array_merge($this->baseVariables($this->connection->getConfig()), [

        ]));

        CFile::append($path, $process->getOutput());
    }

    /**
     * Load the given schema file into the database.
     *
     * @param string $path
     *
     * @return void
     */
    public function load($path) {
        $command = 'mysql ' . $this->connectionString() . ' --database="${:CF_LOAD_DATABASE}" < "${:CF_LOAD_PATH}"';

        $process = $this->makeProcess($command)->setTimeout(null);

        $process->mustRun(null, array_merge($this->baseVariables($this->connection->getConfig()), [
            'CF_LOAD_PATH' => $path,
        ]));
    }

    /**
     * Get the base dump command arguments for MySQL as a string.
     *
     * @return string
     */
    protected function baseDumpCommand() {
        $command = 'mysqldump ' . $this->connectionString() . ' --no-tablespaces --skip-add-locks --skip-comments --skip-set-charset --tz-utc --column-statistics=0';

        $connection = $this->connection;
        /** @var CDatabase_Connection_Pdo_MySqlConnection $connection */
        if (!$connection->isMaria()) {
            $command .= ' --set-gtid-purged=OFF';
        }

        return $command . ' "${:CF_LOAD_DATABASE}"';
    }

    /**
     * Generate a basic connection string (--socket, --host, --port, --user, --password) for the database.
     *
     * @return string
     */
    protected function connectionString() {
        $value = ' --user="${:CF_LOAD_USER}" --password="${:CF_LOAD_PASSWORD}"';

        $config = $this->connection->getConfig();

        $value .= $config['unix_socket'] ?? false
                        ? ' --socket="${:CF_LOAD_SOCKET}"'
                        : ' --host="${:CF_LOAD_HOST}" --port="${:CF_LOAD_PORT}"';

        if (isset($config['options'][\PDO::MYSQL_ATTR_SSL_CA])) {
            $value .= ' --ssl-ca="${:CF_LOAD_SSL_CA}"';
        }

        return $value;
    }

    /**
     * Get the base variables for a dump / load command.
     *
     * @param array $config
     *
     * @return array
     */
    protected function baseVariables(array $config) {
        $config['host'] ??= '';

        return [
            'CF_LOAD_SOCKET' => $config['unix_socket'] ?? '',
            'CF_LOAD_HOST' => is_array($config['host']) ? $config['host'][0] : $config['host'],
            'CF_LOAD_PORT' => $config['port'] ?? '',
            'CF_LOAD_USER' => $config['username'],
            'CF_LOAD_PASSWORD' => $config['password'] ?? '',
            'CF_LOAD_DATABASE' => $config['database'],
            'CF_LOAD_SSL_CA' => $config['options'][\PDO::MYSQL_ATTR_SSL_CA] ?? '',
        ];
    }

    /**
     * Execute the given dump process.
     *
     * @param \Symfony\Component\Process\Process $process
     * @param callable                           $output
     * @param array                              $variables
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function executeDumpProcess(Process $process, $output, array $variables) {
        try {
            $process->setTimeout(null)->mustRun($output, $variables);
        } catch (Exception $e) {
            if (cstr::contains($e->getMessage(), ['column-statistics', 'column_statistics'])) {
                return $this->executeDumpProcess(Process::fromShellCommandLine(
                    str_replace(' --column-statistics=0', '', $process->getCommandLine())
                ), $output, $variables);
            }

            if (str_contains($e->getMessage(), 'set-gtid-purged')) {
                return $this->executeDumpProcess(Process::fromShellCommandLine(
                    str_replace(' --set-gtid-purged=OFF', '', $process->getCommandLine())
                ), $output, $variables);
            }

            throw $e;
        }

        return $process;
    }
}
