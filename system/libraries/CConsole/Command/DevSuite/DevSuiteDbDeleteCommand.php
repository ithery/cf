<?php

/**
 * Description of DevSuiteDbDeleteCommand
 */
class CConsole_Command_DevSuite_DevSuiteDbDeleteCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_Db_DeleteCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:db:delete';
}
