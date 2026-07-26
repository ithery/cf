<?php

/**
 * Description of DevSuiteDbStart
 */
class CConsole_Command_DevSuite_DevSuiteDbStartCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_Db_StartCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:db:start';
}
