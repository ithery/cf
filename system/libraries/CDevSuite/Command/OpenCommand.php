<?php

/**
 * Description of OpenCommand.
 *
 * @author Hery
 */
class CDevSuite_Command_OpenCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '{name?}';
    }

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
