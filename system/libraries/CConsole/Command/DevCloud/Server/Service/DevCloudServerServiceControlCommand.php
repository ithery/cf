<?php

class CConsole_Command_DevCloud_Server_Service_DevCloudServerServiceControlCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Server_Service_ControlCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:server:service:control';
}
