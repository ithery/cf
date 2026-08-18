<?php

/**
 * Description of CompareCommand.
 */
class CDevSuite_Command_Db_CompareCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{--from=} {--to=}';
    }

    /**
     * Compare the "from" database against the "to" database.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $from = $cfCommand->option('from');
        $to = $cfCommand->option('to');
        CDevSuite::db()->existsOrExit($from);
        CDevSuite::db()->existsOrExit($to);
        CDevSuite::db()->compare($from, $to);
    }
}
