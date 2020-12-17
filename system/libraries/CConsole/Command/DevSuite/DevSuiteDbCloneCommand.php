<?php

/**
 * Description of DevSuiteDbCloneCommand
 *
 * @author Hery
 */

class CConsole_Command_DevSuite_DevSuiteDbCloneCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbCloneCommand::class;
    protected $signature = 'devsuite:db:clone';
}
