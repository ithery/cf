<?php

class CConsole_Command_DevSuite_DevSuiteUnlinkCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_UnlinkCommand::class;
    protected $signature = 'devsuite:unlink';
}
