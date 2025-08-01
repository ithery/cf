<?php

/**
 * Description of LinkCommand.
 *
 * @author Hery
 */
class CDevSuite_Command_LinkCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '{name?} {--secure}';
    }

    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name') ?: CF::appCode();
        $secure = $cfCommand->option('secure');
        $linkPath = CDevSuite::site()->link(DOCROOT, $name = $name ?: basename(DOCROOT));

        CDevSuite::info('A [' . $name . '] symbolic link has been created in [' . $linkPath . '].');

        if ($secure) {
            $cfCommand->call('devsuite:secure', ['name' => $name]);
        }
    }
}
