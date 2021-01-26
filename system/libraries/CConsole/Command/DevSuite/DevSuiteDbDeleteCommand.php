<?php

/**
 * Description of DevSuiteDbDeleteCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteDbDeleteCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbDeleteCommand::class;
    protected $signature = 'devsuite:db:delete';
}
