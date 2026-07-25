<?php

/**
 * Description of LinksCommand
 */
class CDevSuite_Command_LinksCommand extends CDevSuite_CommandAbstract {
    /**
     * Display a table listing all linked sites.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $links = CDevSuite::site()->links();

        CDevSuite::table(['Site', 'SSL', 'URL', 'Path'], $links->all());
    }
}
