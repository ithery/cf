<?php

/**
 * Description of DevCloudCollectorExceptionCommand.
 */
class CConsole_Command_DevCloud_Collector_DevCloudCollectorExceptionCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Collector_ExceptionCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:exception';
}
