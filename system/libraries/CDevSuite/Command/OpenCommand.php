<?php

/**
 * Description of OpenCommand
 *
 * @author Hery
 */
class CDevSuite_Command_OpenCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{name}';
    }

    public function run(CConsole_Command $cfCommand) {
        $domain = $cfCommand->argument('name');
        switch (CServer::getOS()) {
            case CServer::OS_LINUX:
            case CServer::OS_DARWIN:
                $url = 'http://' . ($domain ? $domain : CDevSuite::site()->host(getcwd())) . '.' . CDevSuite::configuration()->read()['tld'];
                CDevSuite::commandLine()->runAsUser("open ".escapeshellarg($url));
                break;
            case CServer::OS_WINNT:
                $url = 'http://' . ($domain ? $domain : CDevSuite::site()->host(getcwd())) . '.' . CDevSuite::configuration()->read()['tld'];

                passthru("start $url");
                break;
        }
    }

}
