<?php

/**
 * Description of DeployInitCommand
 *
 * @author Hery
 */

class CDevSuite_Command_DeployInitCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{host : The host server to initialize with.}';
    }

    public function run(CConsole_Command $cfCommand) {
        $host = $cfCommand->argument('host');
        if (file_exists(CDevSuite::deploy()->deployFile())) {
            CDevSuite::error('deploy file already exists!');

            return CConsole::FAILURE_EXIT;
        }
        CDevSuite::deploy()->init($host);
        
        CDevSuite::info('Deploy file created on:'.CDevSuite::deploy()->deployFile());

    }

}
