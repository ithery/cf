<?php

/**
 * Description of DevSuiteSshCommand.
 */
class CConsole_Command_DevSuite_DevSuiteSshCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_Ssh_Command::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:ssh';
}
