<?php

/**
 * Description of DevSuiteUnlinkCommand
 */
class CConsole_Command_DevSuite_DevSuiteUnlinkCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_Link_UnlinkCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:unlink';

    /**
     * @var string
     */
    protected $description = 'Remove the specified Devsuite link';
}
