<?php

/**
 * Description of DeployInitCommand.
 */
class CDevSuite_Command_DeployInitCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{host : The host server to initialize with.}';
    }

    /**
     * Create the deploy file for the given host, if one does not already exist.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return int|void Exit code when the deploy file already exists.
     */
    public function run(CConsole_Command $cfCommand) {
        $host = $cfCommand->argument('host');
        if (file_exists(CDevSuite::deploy()->deployFile())) {
            CDevSuite::error('deploy file already exists!');

            return CConsole::FAILURE_EXIT;
        }
        CDevSuite::deploy()->init($host);

        CDevSuite::info('Deploy file created on:' . CDevSuite::deploy()->deployFile());
    }
}
