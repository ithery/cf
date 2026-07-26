<?php

/**
 * Description of InstallCommand.
 */
class CDevSuite_Command_Db_InstallCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '';
    }

    /**
     * Install MariaDB for DevSuite.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        CDevSuite::devCloud()->installMariaDB();
        CDevSuite::db()->mariaDb()->stop();
        CDevSuite::db()->mariaDb()->install();
        CDevSuite::db()->mariaDb()->restart();

        CDevSuite::output(PHP_EOL . '<info>Dev Suite MariaDb installed successfully!</info>');
    }
}
