<?php

/**
 * Description of DevSuiteDeployInitCommand
 *
 * @author Hery
 */

class CConsole_Command_DevSuite_DevSuiteDeployInitCommand extends CConsole_Command_DevSuiteCommand {

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
    protected $devSuiteCommandClass = CDevSuite_Command_DeployInitCommand::class;
    protected $signature = 'devsuite:deploy:init';

    protected $description = 'Create a new deployment file in the project directory.';
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this->ignoreValidationErrors();

    }

}
