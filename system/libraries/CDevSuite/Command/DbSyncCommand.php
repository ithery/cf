<?php

/**
 * Description of DbSyncCommand
 *
 * @author Hery
 */
class CDevSuite_Command_DbSyncCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{--from=} {--to=}';
    }

    public function run(CConsole_Command $cfCommand) {
        $from = $cfCommand->option('from');
        $to = $cfCommand->option('to');
        CDevSuite::db()->existsOrExit($from);
        CDevSuite::db()->existsOrExit($to);
        
        CDevSuite::db()->compare($from, $to);

        $choice = $cfCommand->choice('Are you sure execute sql on '.$to.':',['No','Yes'],0);
        if($choice=='Yes') {
            CDevSuite::db()->sync($from,$to);
        } else {
            CDevSuite::info('User cancelled command');
        }
        
        
    }

}

