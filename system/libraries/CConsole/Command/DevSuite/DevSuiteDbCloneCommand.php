<?php

/**
 * Description of DevSuiteDbCloneCommand
 */

class CConsole_Command_DevSuite_DevSuiteDbCloneCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_Db_CloneCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:db:clone';
}
