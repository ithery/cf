<?php

/**
 * Description of DevSuiteDbStart
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteDbStartCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbStartCommand::class;

    protected $signature = 'devsuite:db:start';
}
