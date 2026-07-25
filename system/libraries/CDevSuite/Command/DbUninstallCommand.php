<?php

/**
 * Description of DbUninstallCommand.
 */
class CDevSuite_Command_DbUninstallCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '';
    }

    public function run(CConsole_Command $cfCommand) {
        CDevSuite::db()->mariaDb()->stop();
        CDevSuite::db()->mariaDb()->uninstall();

        CDevSuite::output(PHP_EOL . '<info>Dev Suite MariaDb uninstalled successfully!</info>');
    }
}
