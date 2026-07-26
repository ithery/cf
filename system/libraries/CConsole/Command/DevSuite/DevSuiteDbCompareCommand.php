<?php

/**
 * Description of DevSuiteDbCompareCommand
 */
class CConsole_Command_DevSuite_DevSuiteDbCompareCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_Db_CompareCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:db:compare';
}
