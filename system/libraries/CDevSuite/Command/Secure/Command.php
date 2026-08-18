<?php

/**
 * Description of Command.
 */
class CDevSuite_Command_Secure_Command extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{name?}';
    }

    /**
     * Secure the given (or current directory's) site with a fresh TLS certificate.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $domain = $cfCommand->argument('name') ?: CF::appCode();
        $url = ($domain ?: CDevSuite::site()->host(getcwd())) . '.' . CDevSuite::configuration()->read()['tld'];

        CDevSuite::site()->secure($url);

        CDevSuite::nginx()->restart();

        CDevSuite::info('The [' . $url . '] site has been secured with a fresh TLS certificate.');
    }
}
