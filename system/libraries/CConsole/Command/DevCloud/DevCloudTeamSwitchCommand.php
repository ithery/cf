<?php

/**
 * Description of DevCloudTeamSwitchCommand
 */
class CConsole_Command_DevCloud_DevCloudTeamSwitchCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Team_SwitchCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:team:switch';
}
