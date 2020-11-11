<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Command_InstallCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        switch (CServer::getOS()) {
            case CServer::OS_LINUX:
                return '{--ignore-selinux}';
            default:
                return '';
        }
    }

    public function run(CConsole_Command $cfCommand) {
        switch (CServer::getOS()) {
            case CServer::OS_LINUX:
                passthru(dirname(__FILE__) . '/scripts/update.sh'); // Clean up cruft

                $ignoreSELinux = $cfCommand->option('ignore-selinux');
                
                CDevSuite::linuxRequirements()->setIgnoreSELinux($ignoreSELinux)->check();
                CDevSuite::configuration()->install();
                CDevSuite::nginx()->install();
                PhpFpm::install();
                DnsMasq::install(Configuration::read()['domain']);
                Nginx::restart();
                Valet::symlinkToUsersBin();

                output(PHP_EOL . '<info>Valet installed successfully!</info>');
                break;
            default:
                break;
        }
    }

}
