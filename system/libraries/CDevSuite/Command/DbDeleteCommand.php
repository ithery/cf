<?php

/**
 * Description of DbDeleteCommand
 *
 * @author Hery
 */
class CDevSuite_Command_DbDeleteCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{name}';
    }

    public function run(CConsole_Command $cfCommand) {

        $name = $cfCommand->argument('name');

        CDevSuite::db()->existsOrExit($name);
        CDevSuite::db()->delete($name);
        CDevSuite::info('A [' . $name . '] database configuration succesfully deleted.');
    }

}
