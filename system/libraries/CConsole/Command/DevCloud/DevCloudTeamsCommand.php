<?php

/**
 * Description of DevCloudTeamsCommand
 */
class CConsole_Command_DevCloud_DevCloudTeamsCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Teams::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:teams';
}
