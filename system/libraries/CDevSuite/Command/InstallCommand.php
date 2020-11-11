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
                CDevSuite::phpFpm->install();
                CDevSuite::dnsMasq()->install(CDevSuite::configuration()->read()['domain']);
                CDevSuite::nginx()->restart();
                CDevSuite::symlinkToUsersBin();

                break;
            case CServer::OS_WINNT:
                CDevSuite::nginx()->stop();
                CDevSuite::phpFpm()->stop();
                CDevSuite::acrylic()->stop();

                CDevSuite::configuration()->install();

                CDevSuite::nginx()->install();
                CDevSuite::phpFpm()->install();

                $tld = CDevSuite::configuration()->read()['tld'];
                CDevSuite::acrylic()->install($tld);

                CDevSuite::nginx()->restart();

                break;
            default:
                throw new Exception('Dev Suite not available for this OS:' . CServer::getOS());
                break;
        }
        CDevSuite::output(PHP_EOL . '<info>Dev Suite installed successfully!</info>');
    }

}
