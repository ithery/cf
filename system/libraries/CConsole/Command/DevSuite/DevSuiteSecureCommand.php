<?php

/**
 * Description of DevSuiteSecureCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteSecureCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_SecureCommand::class;
    protected $signature = 'devsuite:secure';

}
