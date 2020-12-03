<?php

/**
 * Description of DeployRunCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteDeployRunCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * Command line options that should not be gathered dynamically.
     *
     * @var array
     */
    protected $ignoreOptions = [
        '--continue',
        '--pretend',
        '--help',
        '--quiet',
        '--version',
        '--asci',
        '--no-asci',
        '--no-interactions',
        '--verbose',
    ];

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DeployRunCommand::class;
    protected $signature = 'devsuite:deploy:run';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this->ignoreValidationErrors();

    }

}
