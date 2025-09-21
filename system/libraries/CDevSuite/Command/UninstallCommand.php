<?php

/**
 * Description of UninstallCommand.
 *
 * @author Hery
 */
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CDevSuite_Command_UninstallCommand extends CDevSuite_CommandAbstract {
    public function run(CConsole_Command $cfCommand) {
        CDevSuite::warning('YOU ARE ABOUT TO UNINSTALL Nginx, PHP, Dnsmasq and all DevSuite configs and logs.');
        if (CServer::getOS() == CServer::OS_WINNT) {
            CDevSuite::nginx()->uninstall();
            CDevSuite::phpFpm()->uninstall();
            CDevSuite::acrylic()->uninstall();
        }

        if (CServer::getOS() == CServer::OS_LINUX) {
            $system = CDevSuite::system();
            /** @var CDevSuite_Linux_System $system */
            CDevSuite::nginx()->uninstall();
            CDevSuite::phpFpm()->uninstall();
            CDevSuite::dnsMasq()->uninstall();
            CDevSuite::configuration()->uninstall();
            $system->uninstall();
        }

        if (CServer::getOS() == CServer::OS_DARWIN) {
            $phpFpm = CDevSuite::phpFpm();
            /** @var CDevSuite_Mac_PhpFpm $phpFpm */
            $phpFpm->stopRunning();

            $system = CDevSuite::system();
            /** @var CDevSuite_Mac_System $system */
            CDevSuite::nginx()->stop();

            CDevSuite::info('Removing certificates for all Secured sites...');
            CDevSuite::site()->unsecureAll();
            CDevSuite::info('Removing Nginx and configs...');
            CDevSuite::nginx()->uninstall();
            CDevSuite::info('Removing Dnsmasq and configs...');
            CDevSuite::dnsMasq()->uninstall();
            CDevSuite::info('Removing DevSuite configs and customizations...');
            CDevSuite::configuration()->uninstall();
            CDevSuite::info('Removing PHP versions and configs...');
            CDevSuite::phpFpm()->uninstall();
            CDevSuite::info('Attempting to unlink DevSuite from bin path...');
            $system->unlinkFromUsersBin();
            CDevSuite::info('Removing sudoers entries...');
            CDevSuite::brew()->removeSudoersEntry();
            $system->removeSudoersEntry();
        }

        CDevSuite::devCloud()->uninstall();
        CDevSuite::info('DevSuite has been uninstalled.');
    }
}
