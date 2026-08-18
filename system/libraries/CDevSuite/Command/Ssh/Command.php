<?php

/**
 * CDevSuite_Command_Ssh_Command
 */
class CDevSuite_Command_Ssh_Command extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{name}';
    }

    /**
     * Open an SSH session to the named server configuration.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');

        CDevSuite::devCloud()->installSSH();
        CDevSuite::ssh()->open($name);
    }
}
