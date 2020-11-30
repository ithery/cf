<?php

/**
 * Description of DevSuiteLinkCommand
 *
 * @author Hery
 */
class CConsole_Command_DevSuite_DevSuiteLinkCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_LinkCommand::class;
    protected $signature = 'devsuite:link';

}
