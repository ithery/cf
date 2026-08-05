<?php

/**
 * Run an already-registered `server_remote_command` against its server -
 * the CLI equivalent of the "Execute" button at `server/remote/ssh`.
 * Confirms before running since this executes on a real, possibly
 * production, server.
 */
class CDevSuite_Command_DevCloud_Server_Command_RunCommand extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{serverRemoteId} {serverRemoteCommandId} {--timeout=} {--force}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $serverRemoteId = $cfCommand->argument('serverRemoteId');
        $serverRemoteCommandId = $cfCommand->argument('serverRemoteCommandId');

        if (!$cfCommand->option('force')) {
            $confirmed = $cfCommand->confirm(
                'This runs a stored command on a real server (id ' . $serverRemoteId . '). Continue?',
                false
            );
            if (!$confirmed) {
                $cfCommand->line('Aborted.');

                return;
            }
        }

        try {
            $data = CDevSuite::devCloudApi()->request('server/executeCommand', [
                'serverRemoteId' => $serverRemoteId,
                'serverRemoteCommandId' => $serverRemoteCommandId,
                'timeout' => $cfCommand->option('timeout'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        $server = carr::get($data, 'username') . '@' . carr::get($data, 'host');
        CDevSuite::success('Ran "' . carr::get($data, 'commandName') . '" on ' . carr::get($data, 'serverName'));
        foreach (carr::get($data, 'results', []) as $result) {
            $cfCommand->line('');
            $cfCommand->line('<comment>' . $server . '></comment> ' . carr::get($result, 'command'));
            $cfCommand->line((string) carr::get($result, 'output'));
        }
    }
}
