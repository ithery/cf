<?php

/**
 * Description of ServerListCommand
 *
 * @author Hery
 */

class CDevSuite_Command_SshListCommand extends CDevSuite_CommandAbstract {

    public function run(CConsole_Command $cfCommand) {
        $collection = CDevSuite::ssh()->getTableData();
        
        CDevSuite::table(['Name','Type', 'Database', 'Host', 'Auth'], $collection->all());
    }

}
