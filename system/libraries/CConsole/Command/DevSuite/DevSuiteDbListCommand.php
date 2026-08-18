<?php

/**
 * Description of DBSuiteDBListCommand
 */
class CConsole_Command_DevSuite_DevSuiteDbListCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_Db_ListCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:db:list';
}
