<?php

/**
 * Description of DevSuiteSecureCommand
 */
class CConsole_Command_DevSuite_DevSuiteSecureCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_SecureCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:secure';

    /**
     * @var string
     */
    protected $description = 'Secure the given domain with a trusted TLS certificate';
}
