<?php

/**
 * CDevSuite_Command_SshCommand
 *
 * @author Hery
 */
class CDevSuite_Command_SshCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '{name}';
    }

    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');

        CDevSuite::devCloud()->installSSH();
        CDevSuite::ssh()->open($name);
    }
}
