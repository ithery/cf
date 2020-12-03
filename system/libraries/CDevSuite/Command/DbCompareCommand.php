<?php

/**
 * Description of DbCompareCommand
 *
 * @author Hery
 */

class CDevSuite_Command_DbCompareCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{--from=} {--to=}';
    }

    public function run(CConsole_Command $cfCommand) {
        $from = $cfCommand->option('from');
        $to = $cfCommand->option('to');
        
        CDevSuite::db()->compare($from, $to);
            
        
    }

}
