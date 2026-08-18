<?php

/**
 * Start/stop/restart one of a server's registered services - the CLI
 * equivalent of the buttons at server/remote/info/services. Confirms
 * before running since this changes a real, possibly production, service.
 */
class CDevSuite_Command_DevCloud_Server_Service_ControlCommand extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{serverRemoteId} {serverRemoteServiceId} {action} {--force}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $serverRemoteId = $cfCommand->argument('serverRemoteId');
        $serverRemoteServiceId = $cfCommand->argument('serverRemoteServiceId');
        $action = $cfCommand->argument('action');

        if (!$cfCommand->option('force')) {
            $confirmed = $cfCommand->confirm(
                ucfirst($action) . ' service on server id ' . $serverRemoteId . '? This affects a real, possibly production, service.',
                false
            );
            if (!$confirmed) {
                $cfCommand->line('Aborted.');

                return;
            }
        }

        try {
            $data = CDevSuite::devCloudApi()->request('server/controlService', [
                'serverRemoteId' => $serverRemoteId,
                'serverRemoteServiceId' => $serverRemoteServiceId,
                'action' => $action,
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success(
            ucfirst(carr::get($data, 'action')) . ' ' . carr::get($data, 'service')
            . ' - status now: ' . carr::get($data, 'status')
        );
    }
}
