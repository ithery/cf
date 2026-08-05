<?php

class CConsole_Command_DevCloud_Server_Command_DevCloudServerCommandListCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Server_Command_ListCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:server:command:list';
}
