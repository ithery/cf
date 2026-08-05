<?php

/**
 * Create and seed a new schema on an existing devcloud db connection.
 */
class CDevSuite_Command_DevCloud_Database_CreateDev extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{dbConnectionId} {database} {--name=} {--environment=} {--schema=}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        try {
            $data = CDevSuite::devCloudApi()->request('database/createDev', [
                'dbConnectionId' => $cfCommand->argument('dbConnectionId'),
                'database' => $cfCommand->argument('database'),
                'name' => $cfCommand->option('name'),
                'environment' => $cfCommand->option('environment'),
                'schema' => $cfCommand->option('schema'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success('Database created and seeded: ' . carr::get($data, 'database'));
        $cfCommand->line('Db Account ID: ' . carr::get($data, 'dbAccountId') . ', tables: ' . carr::get($data, 'tableCount'));
    }
}
