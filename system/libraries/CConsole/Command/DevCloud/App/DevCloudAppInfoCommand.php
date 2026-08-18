<?php

/**
 * Description of DevCloudAppInfoCommand. Nested under App/ (unlike
 * DevCloudLoginCommand/DevCloudTeamsCommand) because its underlying
 * CDevSuite_Command_DevCloud_App_Info requires running from inside an
 * application/{app} folder, not just anywhere under the CF directory.
 */
class CConsole_Command_DevCloud_App_DevCloudAppInfoCommand extends CConsole_Command_DevSuiteCommand {
    /**
     * The class name of the devsuite command.
     *
     * @var string
     */
    protected $devSuiteCommandClass = CDevSuite_Command_DevCloud_App_Info::class;

    /**
     * @var string
     */
    protected $signature = 'devcloud:info';
}
