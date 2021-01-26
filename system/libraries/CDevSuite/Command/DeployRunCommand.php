<?php

/**
 * Description of DeployRunCommand
 *
 * @author Hery
 */
class CDevSuite_Command_DeployRunCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{task} {--continue} {--pretend} {--path= : The path to the deploy.blade.php file} {--conf=deploy.blade.php : The name of the Envoy file}';
    }

    public function run(CConsole_Command $cfCommand) {
        $task = $cfCommand->argument('task');
        $continue = $cfCommand->option('continue');
        $pretending = $cfCommand->option('pretend');
        CDevSuite::deploy()->deployFileExistsOrExit();

        $deployer = CDevSuite::deploy()->run($task,$continue,$pretending);
    }

}
