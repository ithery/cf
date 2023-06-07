<?php

/**
 * Description of SecureCommand.
 *
 * @author Hery
 */
class CDevSuite_Command_SecureCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '{name?}';
    }

    public function run(CConsole_Command $cfCommand) {
        $domain = $cfCommand->argument('name') ?: CF::appCode();
        $url = ($domain ?: CDevSuite::site()->host(getcwd())) . '.' . CDevSuite::configuration()->read()['tld'];

        CDevSuite::site()->secure($url);

        CDevSuite::nginx()->restart();

        CDevSuite::info('The [' . $url . '] site has been secured with a fresh TLS certificate.');
    }
}
