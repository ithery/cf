<?php

/**
 * Description of StopCommand
 */

class CDevSuite_Command_StopCommand extends CDevSuite_CommandAbstract {

    /**
     * Stop the DevSuite services (PHP-FPM, nginx, and Acrylic on Windows).
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        CDevSuite::phpFpm()->stop();
        CDevSuite::nginx()->stop();

        if (CServer::getOS() == CServer::OS_WINNT) {
            CDevSuite::acrylic()->stop();
        } else {
            if(CServer::getOS() != CServer::OS_DARWIN) {
                //CDevSuite::dnsMasq()->stop();
            }
        }



        CDevSuite::info('DevSuite services have been stopped.');
    }

}
