<?php

class CConsole_Command_DevCloud_Server_Vhost_DevCloudServerVhostListCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Server_Vhost_ListCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:server:vhost:list';
}
