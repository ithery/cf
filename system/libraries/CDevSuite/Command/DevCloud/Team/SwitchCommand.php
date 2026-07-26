<?php

/**
 * Description of SwitchCommand
 */
class CDevSuite_Command_DevCloud_Team_SwitchCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{teamCode}';
    }

    /**
     * Switch the logged in user's current DevCloud team.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $teamCode = $cfCommand->argument('teamCode');

        try {
            $team = CDevSuite::devCloudApi()->request('team/switch', ['code' => $teamCode], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success('Switched to team ' . carr::get($team, 'name') . ' (' . carr::get($team, 'code') . ')');
    }
}
