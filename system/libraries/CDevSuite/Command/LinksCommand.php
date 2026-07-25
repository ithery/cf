<?php

/**
 * Description of LinksCommand
 */
class CDevSuite_Command_LinksCommand extends CDevSuite_CommandAbstract {
    public function run(CConsole_Command $cfCommand) {
        $links = CDevSuite::site()->links();

        CDevSuite::table(['Site', 'SSL', 'URL', 'Path'], $links->all());
    }
}
