<?php

class CConsole_Command_DevCloud_Server_DevCloudServerWebServerCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Server_WebServerCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:server:webserver';
}
