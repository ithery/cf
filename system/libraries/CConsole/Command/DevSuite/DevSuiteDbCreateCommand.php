<?php

/**
 * Description of DevSuiteDBCreateCommand
 */
class CConsole_Command_DevSuite_DevSuiteDbCreateCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbCreateCommand::class;
    protected $signature = 'devsuite:db:create';
}
