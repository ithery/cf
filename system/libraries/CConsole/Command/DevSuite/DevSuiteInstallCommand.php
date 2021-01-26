<?php

/**
 * Description of DevSuiteInstallCommand
 *
 * @author Hery
 */
use Symfony\Component\Console\Input\InputArgument;

class CConsole_Command_DevSuite_DevSuiteInstallCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_InstallCommand::class;

    protected $signature = 'devsuite:install';
}
