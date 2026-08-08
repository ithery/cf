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
        return '{teamCode?}';
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

        if (empty($teamCode)) {
            $teamCode = $this->promptForTeamCode($cfCommand);

            if ($teamCode === null) {
                return CConsole::FAILURE_EXIT;
            }
        }

        try {
            $team = CDevSuite::devCloudApi()->request('team/switch', ['code' => $teamCode], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success('Switched to team ' . carr::get($team, 'name') . ' (' . carr::get($team, 'code') . ')');
    }

    /**
     * Ask the user to pick from the teams they belong to.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return null|string the chosen team code, or null on failure
     */
    protected function promptForTeamCode(CConsole_Command $cfCommand) {
        try {
            $teams = CDevSuite::devCloudApi()->request('team/get');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return null;
        }

        if (empty($teams)) {
            CDevSuite::error('You have not joined any team yet.');

            return null;
        }

        $codeByLabel = [];
        foreach ($teams as $team) {
            $label = carr::get($team, 'name') . ' (' . carr::get($team, 'code') . ')';
            if (carr::get($team, 'isCurrent')) {
                $label .= ' (current)';
            }
            $codeByLabel[$label] = carr::get($team, 'code');
        }

        $selectedLabel = $cfCommand->choice('Select a team to switch to', array_keys($codeByLabel));

        return carr::get($codeByLabel, $selectedLabel);
    }
}
