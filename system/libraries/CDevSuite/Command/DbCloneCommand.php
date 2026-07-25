<?php

/**
 * Description of DbCloneCommand.
 */
class CDevSuite_Command_DbCloneCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{--from=} {--to=}';
    }

    /**
     * Clone the "from" database configuration into the "to" database.
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
        CDevSuite::db()->cloneDatabase($from, $to);
    }
}
