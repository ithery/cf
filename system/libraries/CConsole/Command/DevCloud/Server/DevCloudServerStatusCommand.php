<?php

class CConsole_Command_DevCloud_Server_DevCloudServerStatusCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Server_StatusCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:server:status';
}
