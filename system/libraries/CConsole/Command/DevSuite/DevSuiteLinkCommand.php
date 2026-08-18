<?php

/**
 * Description of DevSuiteLinkCommand
 */
class CConsole_Command_DevSuite_DevSuiteLinkCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_Link_Command::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:link';
}
