<?php

/**
 * Detect which webserver(s) are installed/running on a server.
 */
class CDevSuite_Command_DevCloud_Server_WebServerCommand extends CDevSuite_CommandAbstract {
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
            $data = CDevSuite::devCloudApi()->request('server/getWebServerInfo', [
                'serverRemoteId' => $cfCommand->argument('serverRemoteId'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        $cfCommand->line('<info>' . carr::get($data, 'serverName') . '</info>');
        $rows = [];
        foreach (carr::get($data, 'webServerList', []) as $item) {
            $rows[] = [
                carr::get($item, 'type'),
                carr::get($item, 'status'),
                carr::get($item, 'version'),
                carr::get($item, 'vhost'),
                implode(', ', (array) carr::get($item, 'port')),
                carr::get($item, 'config'),
            ];
        }
        $cfCommand->table(['Type', 'Status', 'Version', 'Vhosts', 'Ports', 'Config'], $rows);
    }
}
