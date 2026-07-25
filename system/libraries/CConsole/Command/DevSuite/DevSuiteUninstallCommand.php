<?php

/**
 * Description of DevSuiteUninstallCommand
 */

class CConsole_Command_DevSuite_DevSuiteUninstallCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_UninstallCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:uninstall';
}
