<?php

/**
 * List devcloud's registered remote servers.
 */
class CDevSuite_Command_DevCloud_Server_ListCommand extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{--page=1} {--perPage=50}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        try {
            $data = CDevSuite::devCloudApi()->request('server/getRemoteList', [
                'page' => $cfCommand->option('page'),
                'perPage' => $cfCommand->option('perPage'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        $rows = [];
        foreach (carr::get($data, 'items', []) as $item) {
            $rows[] = [
                carr::get($item, 'serverRemoteId'),
                carr::get($item, 'name'),
                carr::get($item, 'host'),
                carr::get($item, 'ipAddress'),
            ];
        }
        $cfCommand->table(['ID', 'Name', 'Host', 'IP'], $rows);
        $cfCommand->line(
            'Page ' . carr::get($data, 'currentPage') . '/' . carr::get($data, 'lastPage')
            . ' (' . carr::get($data, 'total') . ' total)'
        );
    }
}
