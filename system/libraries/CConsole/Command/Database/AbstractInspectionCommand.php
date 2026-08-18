<?php

abstract class CConsole_Command_Database_AbstractInspectionCommand extends CConsole_Command {
    /**
     * Get a human-readable name for the given connection.
     *
     * @param \CDatabase_ConnectionInterface $connection
     * @param string                         $database
     *
     * @return string
     */
    protected function getConnectionName(CDatabase_ConnectionInterface $connection, $database) {
        if ($connection instanceof CDatabase_Connection_Pdo_MySqlConnection && $connection->isMaria()) {
            return 'MariaDB';
        }
        if ($connection instanceof CDatabase_Connection_Pdo_MySqlConnection) {
            return 'MySQL';
        }
        if ($connection instanceof CDatabase_Connection_Pdo_MariaDbConnection) {
            return 'MariaDB';
        }
        if ($connection instanceof CDatabase_Connection_Pdo_PostgresConnection) {
            return 'PostgreSQL';
        }
        if ($connection instanceof CDatabase_Connection_Pdo_SqliteConnection) {
            return 'SQLite';
        }
        if ($connection instanceof CDatabase_Connection_Pdo_SqlServerConnection) {
            return 'SQL Server';
        }

        return $database;
    }

    /**
     * Get the number of open connections for a database.
     *
     * @param \CDatabase_ConnectionInterface $connection
     *
     * @return null|int
     */
    protected function getConnectionCount(CDatabase_ConnectionInterface $connection) {
        $result = null;
        if ($connection instanceof CDatabase_Connection_Pdo_MySqlConnection) {
            $result = $connection->selectOne('show status where variable_name = "threads_connected"');
        }
        if ($connection instanceof CDatabase_Connection_Pdo_PostgresConnection) {
            $result = $connection->selectOne('select count(*) as "Value" from pg_stat_activity');
        }
        if ($connection instanceof CDatabase_Connection_Pdo_SqlServerConnection) {
            $result = $connection->selectOne('select count(*) Value from sys.dm_exec_sessions where status = ?', ['running']);
        }

        if (!$result) {
            return null;
        }

        return carr::wrap((array) $result)['Value'];
    }

    /**
     * Get the connection configuration details for the given connection.
     *
     * @param string $database
     *
     * @return array
     */
    protected function getConfigFromDatabase($database) {
        $database ??= CF::config('database.default');

        return carr::except(CF::config('database.connections.' . $database), ['password']);
    }

    /**
     * Remove the table prefix from a table name, if it exists.
     *
     * @param \CDatabase_ConnectionInterface $connection
     * @param string                         $table
     *
     * @return string
     */
    protected function withoutTablePrefix(CDatabase_ConnectionInterface $connection, string $table) {
        /** @var CDatabase_Connection $connection */
        $prefix = $connection->getTablePrefix();

        return str_starts_with($table, $prefix)
            ? substr($table, strlen($prefix))
            : $table;
    }
}
