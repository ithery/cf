<?php

/**
 * Description of DeleteCommand.
 */
class CDevSuite_Command_Db_DeleteCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{name}';
    }

    /**
     * Delete the named database configuration.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');

        CDevSuite::db()->existsOrExit($name);
        CDevSuite::db()->delete($name);
        CDevSuite::info('A [' . $name . '] database configuration succesfully deleted.');
    }
}
