<?php

/**
 * Description of DevCloudCollectorExceptionListCommand.
 */
class CConsole_Command_DevCloud_Collector_DevCloudCollectorExceptionListCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_Collector_ExceptionListCommand::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:exceptions';
}
