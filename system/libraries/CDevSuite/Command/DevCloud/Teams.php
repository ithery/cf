<?php

/**
 * Description of Teams.
 */
class CDevSuite_Command_DevCloud_Teams extends CDevSuite_CommandAbstract {
    /**
     * List the DevCloud teams the logged in user belongs to. Works from
     * anywhere inside the CF directory - not tied to a specific app.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        try {
            $teams = CDevSuite::devCloudApi()->request('team/get');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        if (empty($teams)) {
            CDevSuite::info('You have not joined any team yet.');

            return;
        }

        $rows = [];
        foreach ($teams as $team) {
            $rows[] = [
                carr::get($team, 'code'),
                carr::get($team, 'name'),
                carr::get($team, 'isCurrent') ? '✓' : '',
            ];
        }

        $cfCommand->table(['Code', 'Name', 'Current'], $rows);
    }
}
