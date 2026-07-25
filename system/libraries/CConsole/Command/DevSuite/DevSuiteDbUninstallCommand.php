<?php

/**
 * Description of DevSuiteDbInstall
 */
class CConsole_Command_DevSuite_DevSuiteDbUninstallCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DbUninstallCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:db:uninstall';
}
