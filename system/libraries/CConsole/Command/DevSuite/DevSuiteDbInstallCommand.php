<?php

/**
 * Description of DevSuiteDbInstall
 */
class CConsole_Command_DevSuite_DevSuiteDbInstallCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbInstallCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:db:install';
}
