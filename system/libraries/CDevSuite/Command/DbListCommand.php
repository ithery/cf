<?php

/**
 * Description of DbListCommand.
 */
class CDevSuite_Command_DbListCommand extends CDevSuite_CommandAbstract {
    /**
     * Display a table listing all configured database connections.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $collection = CDevSuite::db()->getTableData();

        CDevSuite::table(['Name', 'Type', 'Database', 'Host'], $collection->all());
    }
}
