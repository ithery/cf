<?php

class CConsole_Command_DevCloud_Git_DevCloudGitCreateCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Git_Create::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:git:create';
}
