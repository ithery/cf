<?php

/**
 * Description of DevSuiteDbSyncCommand
 */

class CConsole_Command_DevSuite_DevSuiteDbSyncCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbSyncCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:db:sync';
}
