<?php

/**
 * List the stored SSH commands (`server_remote_command`) for a devcloud
 * server remote.
 */
class CDevSuite_Command_DevCloud_Server_Command_ListCommand extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{serverRemoteId}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        try {
            $data = CDevSuite::devCloudApi()->request('server/getCommandList', [
                'serverRemoteId' => $cfCommand->argument('serverRemoteId'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        $cfCommand->line('Server: ' . carr::get($data, 'serverName'));
        $rows = [];
        foreach (carr::get($data, 'commandList', []) as $item) {
            $rows[] = [
                carr::get($item, 'serverRemoteCommandId'),
                carr::get($item, 'name'),
                implode(' && ', (array) carr::get($item, 'commands')),
            ];
        }
        $cfCommand->table(['ID', 'Name', 'Commands'], $rows);
    }
}
