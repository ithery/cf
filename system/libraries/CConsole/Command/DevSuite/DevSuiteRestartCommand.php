<?php

/**
 * Description of DevSuiteRestartCommand
 */
class CConsole_Command_DevSuite_DevSuiteRestartCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_StartCommand::class;
    protected $signature = 'devsuite:restart';
}
