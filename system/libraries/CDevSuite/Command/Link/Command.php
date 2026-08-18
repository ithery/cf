<?php

/**
 * Description of Command.
 */
class CDevSuite_Command_Link_Command extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{name?} {--secure}';
    }

    /**
     * Create a symbolic link from the current directory to the DevSuite sites path.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
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
