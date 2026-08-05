<?php

class CConsole_Command_DevCloud_Database_DevCloudDatabaseCreateDevCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Database_CreateDev::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:database:create-dev';
}
