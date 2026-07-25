<?php

/**
 * Description of SshListCommand
 */
class CDevSuite_Command_SshListCommand extends CDevSuite_CommandAbstract {
    /**
     * Display a table listing all configured SSH server connections.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $collection = CDevSuite::ssh()->getTableData();

        CDevSuite::table(['Name', 'Host', 'Type', 'User', 'Password'], $collection->all());
    }
}
