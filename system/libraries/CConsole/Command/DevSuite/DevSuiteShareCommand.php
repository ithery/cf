<?php

/**
 * Description of DevSuiteShareCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteShareCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_ShareCommand::class;
    protected $signature = 'devsuite:share';
}
