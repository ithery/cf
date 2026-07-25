<?php

/**
 * Description of DbUninstallCommand.
 */
class CDevSuite_Command_DbUninstallCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '';
    }

    /**
     * Stop and uninstall MariaDB.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        CDevSuite::db()->mariaDb()->stop();
        CDevSuite::db()->mariaDb()->uninstall();

        CDevSuite::output(PHP_EOL . '<info>Dev Suite MariaDb uninstalled successfully!</info>');
    }
}
