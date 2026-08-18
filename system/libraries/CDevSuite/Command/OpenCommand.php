<?php

/**
 * Description of OpenCommand.
 */
class CDevSuite_Command_OpenCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{name?}';
    }

    /**
     * Open the given (or current directory's) site in the default browser.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $domain = $cfCommand->argument('name') ?: CF::appCode();
        $url = 'http://' . ($domain ? $domain : CDevSuite::site()->host(getcwd()))
        . '.' . CDevSuite::configuration()->read()['tld'];

        switch (CServer::getOS()) {
            case CServer::OS_LINUX:
                CDevSuite::commandLine()->runAsUser('xdg-open ' . escapeshellarg($url));

                break;
            case CServer::OS_DARWIN:
                CDevSuite::commandLine()->runAsUser('open ' . escapeshellarg($url));

                break;
            case CServer::OS_WINNT:
                passthru("start {$url}");

                break;
        }
    }
}
