<?php

/**
 * Description of UninstallCommand
 *
 * @author Hery
 */
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CDevSuite_Command_UninstallCommand extends CDevSuite_CommandAbstract {

    public function run(CConsole_Command $cfCommand) {

        CDevSuite::warning('YOU ARE ABOUT TO UNINSTALL Nginx, PHP, Dnsmasq and all DevSuite configs and logs.');
        switch (CServer::getOS()) {
            case CServer::OS_WINNT:
                CDevSuite::nginx()->uninstall();
                CDevSuite::phpFpm()->uninstall();
                CDevSuite::acrylic()->uninstall();


                break;
            case CServer::OS_LINUX:
                CDevSuite::nginx()->uninstall();
                CDevSuite::phpFpm()->uninstall();
                CDevSuite::dnsMasq()->uninstall();
                CDevSuite::configuration()->uninstall();
                CDevSuite::system()->uninstall();

                break;

            case CServer::OS_DARWIN:

                CDevSuite::phpFpm()->stopRunning();
                CDevSuite::nginx()->stop();

                CDevSuite::info('Removing certificates for all Secured sites...');
                CDevSuite::site()->unsecureAll();
                CDevSuite::info('Removing Nginx and configs...');
                CDevSuite::nginx()->uninstall();
                CDevSuite::info('Removing Dnsmasq and configs...');
                CDevSuite::dnsMasq()->uninstall();
                CDevSuite::info('Removing Valet configs and customizations...');
                CDevSuite::configuration()->uninstall();
                CDevSuite::info('Removing PHP versions and configs...');
                CDevSuite::phpFpm()->uninstall();
                CDevSuite::info('Attempting to unlink Valet from bin path...');
                CDevSuite::system()->unlinkFromUsersBin();
                CDevSuite::info('Removing sudoers entries...');
                CDevSuite::brew()->removeSudoersEntry();
                CDevSuite::system()->removeSudoersEntry();
                break;

            default:
                throw new Exception('Dev Suite not available for this OS:' . CServer::getOS());
                break;
        }

        CDevSuite::devCloud()->uninstall();
        CDevSuite::info('DevSuite has been uninstalled.');
    }

}
