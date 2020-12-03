<?php

/**
 * Description of DevSuiteSshListCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteSshListCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_SshListCommand::class;
    protected $signature = 'devsuite:ssh:list';

}
