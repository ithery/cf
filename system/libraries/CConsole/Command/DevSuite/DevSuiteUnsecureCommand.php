<?php

class CConsole_Command_DevSuite_DevSuiteUnsecureCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_UnsecureCommand::class;
    protected $signature = 'devsuite:unsecure';
}
