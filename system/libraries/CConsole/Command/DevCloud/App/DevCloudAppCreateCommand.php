<?php

class CConsole_Command_DevCloud_App_DevCloudAppCreateCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_App_Create::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:app:create';
}
