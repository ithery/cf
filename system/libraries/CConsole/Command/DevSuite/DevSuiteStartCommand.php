<?php

/**
 * Description of DevSuiteStartCommand
 */
class CConsole_Command_DevSuite_DevSuiteStartCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_StartCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:start';
}
