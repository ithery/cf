<?php

/**
 * Description of DevSuiteTldCommand
 *
 * @author Hery
 */

class CConsole_Command_DevSuite_DevSuiteTldCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_TldCommand::class;
    protected $signature = 'devsuite:tld';

}
