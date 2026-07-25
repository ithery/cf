<?php

/**
 * Description of UnsecureCommand.
 */
class CDevSuite_Command_UnsecureCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{name?} {--all}';
    }

    /**
     * Remove the TLS certificate for the given site, or all secured sites when --all is passed.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $domain = $cfCommand->argument('name') ?: CF::appCode();
        $all = $cfCommand->option('all');
        if ($all) {
            CDevSuite::site()->unsecureAll();

            return;
        }

        $url = ($domain ?: CDevSuite::site()->host(getcwd())) . '.' . CDevSuite::configuration()->read()['tld'];

        CDevSuite::site()->unsecure($url);

        CDevSuite::nginx()->restart();

        CDevSuite::info('The [' . $url . '] site will now serve traffic over HTTP.');
    }
}
