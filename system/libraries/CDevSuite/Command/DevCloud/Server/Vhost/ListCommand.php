<?php

/**
 * List a server's webserver virtual hosts (LiteSpeed by default).
 */
class CDevSuite_Command_DevCloud_Server_Vhost_ListCommand extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{serverRemoteId} {--type=}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        try {
            $data = CDevSuite::devCloudApi()->request('server/getVirtualHostList', [
                'serverRemoteId' => $cfCommand->argument('serverRemoteId'),
                'type' => $cfCommand->option('type'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        $cfCommand->line('<info>' . carr::get($data, 'serverName') . '</info> (' . carr::get($data, 'type') . ')');
        $rows = [];
        foreach (carr::get($data, 'virtualHostList', []) as $item) {
            $rows[] = [
                carr::get($item, 'name'),
                carr::get($item, 'domain'),
                carr::get($item, 'vhRoot', carr::get($item, 'root')),
                carr::get($item, 'configPath'),
            ];
        }
        $cfCommand->table(['Name', 'Domain', 'Root', 'Config'], $rows);
    }
}
