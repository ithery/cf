<?php

/**
 * Description of UninstallCommand
 *
 * @author Hery
 */
class CDevSuite_Command_UninstallCommand extends CDevSuite_CommandAbstract {

    public function run(CConsole_Command $cfCommand) {

        switch (CServer::getOS()) {
            case CServer::OS_LINUX:


                break;
            case CServer::OS_WINNT:
                CDevSuite::nginx()->uninstall();
                CDevSuite::phpFpm()->uninstall();
                CDevSuite::acrylic()->uninstall();


                break;
            case CServer::OS_DARWIN:
                CDevSuite::nginx()->uninstall();
                CDevSuite::phpFpm()->uninstall();
                CDevSuite::dnsMasq()->uninstall();
                CDevSuite::configuration()->uninstall();
                CDevSuite::configuration()->uninstall();


                break;
            
             Nginx::uninstall();
        PhpFpm::uninstall();
        DnsMasq::uninstall();
        Configuration::uninstall();
        Valet::uninstall();
            default:
                throw new Exception('Dev Suite not available for this OS:' . CServer::getOS());
                break;
        }

        CDevSuite::devCloud()->uninstall();
        CDevSuite::info('Dev Suite has been uninstalled.');
    }

}
