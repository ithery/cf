<?php

/**
 * Description of DBSuiteDBListCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteDbListCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbListCommand::class;
    protected $signature = 'devsuite:db:list';
}
