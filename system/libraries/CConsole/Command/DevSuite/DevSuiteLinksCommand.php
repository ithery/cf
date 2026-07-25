<?php

/**
 * Description of DevSuiteLinksCommand
 */
class CConsole_Command_DevSuite_DevSuiteLinksCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_LinksCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devsuite:links';

    /**
     * @var string
     */
    protected $description = 'Display all of the registered Devsuite links';
}
