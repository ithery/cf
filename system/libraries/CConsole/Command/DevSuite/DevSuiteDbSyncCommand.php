<?php

/**
 * Description of DevSuiteDbSyncCommand
 *
 * @author Hery
 */

class CConsole_Command_DevSuite_DevSuiteDbSyncCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbSyncCommand::class;
    protected $signature = 'devsuite:db:sync';

}
