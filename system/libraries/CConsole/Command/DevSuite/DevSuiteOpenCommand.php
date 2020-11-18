<?php

/**
 * Description of DevSuiteOpenCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteOpenCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_OpenCommand::class;
    protected $signature = 'devsuite:open';

}
