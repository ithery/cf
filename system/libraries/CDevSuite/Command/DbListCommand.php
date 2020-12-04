<?php

/**
 * Description of DbListCommand
 *
 * @author Hery
 */
class CDevSuite_Command_DbListCommand extends CDevSuite_CommandAbstract {

    public function run(CConsole_Command $cfCommand) {
        $collection = CDevSuite::db()->getTableData();
        
        CDevSuite::table(['Name','Type', 'Database', 'Host'], $collection->all());
    }

}
