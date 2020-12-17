<?php

/**
 * Description of DevSuiteStopCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteStopCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_StopCommand::class;
    protected $signature = 'devsuite:stop';
}
