<?php

/**
 * Description of DevSuiteSshListCommand
 */
class CConsole_Command_DevSuite_DevSuiteSshListCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_SshListCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:ssh:list';
}
