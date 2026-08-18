<?php

class CConsole_Command_DevCloud_App_DevCloudAppScaffoldCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_App_Scaffold::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:app:scaffold';
}
