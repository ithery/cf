<?php

/**
 * Description of DevSuiteTldCommand
 */

class CConsole_Command_DevSuite_DevSuiteTldCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_TldCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:tld';
}
