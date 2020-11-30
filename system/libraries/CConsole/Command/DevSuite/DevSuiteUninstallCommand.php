<?php

/**
 * Description of DevSuiteUninstallCommand
 *
 * @author Hery
 */


class CConsole_Command_DevSuite_DevSuiteUninstallCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_UninstallCommand::class;

    protected $signature = 'devsuite:uninstall';

}
