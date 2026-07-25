<?php

/**
 * Description of DbInstallCommand.
 */
class CDevSuite_Command_DbStartCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '';
    }

    public function run(CConsole_Command $cfCommand) {
        CDevSuite::db()->mariaDb()->restart();
    }
}
