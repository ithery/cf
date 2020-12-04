<?php

/**
 * Description of DevSuiteLinksCommand
 *
 * @author Hery
 */

class CConsole_Command_DevSuite_DevSuiteLinksCommand extends CConsole_Command_DevSuiteCommand {

    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_LinksCommand::class;
    protected $signature = 'devsuite:links';

}
