<?php

/**
 * Show a server's live system/resource info and registered services'
 * status.
 */
class CDevSuite_Command_DevCloud_Server_StatusCommand extends CDevSuite_CommandAbstract {
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
            $data = CDevSuite::devCloudApi()->request('server/getStatus', [
                'serverRemoteId' => $cfCommand->argument('serverRemoteId'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        $system = carr::get($data, 'system', []);
        $memory = carr::get($data, 'memory.physical', []);
        $storage = carr::get($data, 'storage', []);

        $cfCommand->line('<info>' . carr::get($data, 'serverName') . '</info> (' . carr::get($data, 'host') . ')');
        $cfCommand->table(['Field', 'Value'], [
            ['Hostname', carr::get($system, 'hostname')],
            ['Distribution', carr::get($system, 'distribution')],
            ['Kernel', carr::get($system, 'kernel')],
            ['Uptime', carr::get($system, 'uptime')],
            ['Load', is_array(carr::get($system, 'load')) ? implode(', ', carr::get($system, 'load')) : carr::get($system, 'load')],
            ['Memory used', static::bytesToHuman(carr::get($memory, 'used')) . ' / ' . static::bytesToHuman(carr::get($memory, 'total'))],
            ['Storage used', static::bytesToHuman(carr::get($storage, 'used')) . ' / ' . static::bytesToHuman(carr::get($storage, 'total'))],
        ]);

        $cfCommand->line('<info>Services</info>');
        $rows = [];
        foreach (carr::get($data, 'services', []) as $service) {
            $rows[] = [
                carr::get($service, 'service'),
                carr::get($service, 'label'),
                carr::get($service, 'status'),
                carr::get($service, 'since'),
            ];
        }
        $cfCommand->table(['Service', 'Label', 'Status', 'Since'], $rows);
    }

    /**
     * @param null|int $bytes
     *
     * @return string
     */
    protected static function bytesToHuman($bytes) {
        $bytes = (float) $bytes;
        if ($bytes <= 0) {
            return '-';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), 1) . ' ' . $units[$i];
    }
}
