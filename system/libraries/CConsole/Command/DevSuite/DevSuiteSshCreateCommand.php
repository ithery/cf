<?php

/**
 * Description of DevSuiteSshCreateCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteSshCreateCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_SshCreateCommand::class;
    protected $signature = 'devsuite:ssh:create';

}
