<?php

/**
 * Description of DbCloneCommand
 *
 * @author Hery
 */

class CDevSuite_Command_DbCloneCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{--from=} {--to=}';
    }

    public function run(CConsole_Command $cfCommand) {
        $from = $cfCommand->option('from');
        $to = $cfCommand->option('to');
        CDevSuite::db()->existsOrExit($from);
        CDevSuite::db()->existsOrExit($to);
        CDevSuite::db()->cloneDatabase($from, $to);
    }

}
