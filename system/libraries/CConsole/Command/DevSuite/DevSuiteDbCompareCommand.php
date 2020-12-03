<?php

/**
 * Description of DevSuiteDbCompareCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteDbCompareCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbCompareCommand::class;
    protected $signature = 'devsuite:db:compare';

}
