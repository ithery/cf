<?php

/**
 * Description of StartCommand.
 */
class CDevSuite_Command_Db_StartCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '';
    }

    /**
     * Restart the MariaDB service.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        CDevSuite::db()->mariaDb()->restart();
    }
}
