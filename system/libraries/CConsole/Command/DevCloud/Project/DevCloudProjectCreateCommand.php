<?php

class CConsole_Command_DevCloud_Project_DevCloudProjectCreateCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Project_Create::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:project:create';
}
