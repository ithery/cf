<?php

/**
 * Description of DbInstallCommand
 *
 * @author Hery
 */
class CDevSuite_Command_DbStartCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '';
    }

    public function run(CConsole_Command $cfCommand) {
        CDevSuite::db()->mariaDb()->restart();
    }
}
