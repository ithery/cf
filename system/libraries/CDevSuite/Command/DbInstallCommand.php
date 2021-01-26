<?php

/**
 * Description of DbInstallCommand
 *
 * @author Hery
 */
class CDevSuite_Command_DbInstallCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '';
    }

    public function run(CConsole_Command $cfCommand) {

        CDevSuite::devCloud()->installMariaDB();
        CDevSuite::db()->mariaDb()->stop();
        CDevSuite::db()->mariaDb()->install();
        CDevSuite::db()->mariaDb()->restart();

        CDevSuite::output(PHP_EOL . '<info>Dev Suite MariaDb installed successfully!</info>');
    }

}
