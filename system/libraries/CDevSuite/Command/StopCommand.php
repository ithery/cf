<?php

/**
 * Description of StopCommand
 *
 * @author Hery
 */

class CDevSuite_Command_StopCommand extends CDevSuite_CommandAbstract {

    public function run(CConsole_Command $cfCommand) {
        CDevSuite::phpFpm()->stop();
        CDevSuite::nginx()->stop();

        if (CServer::getOS() == CServer::OS_WINNT) {
            CDevSuite::acrylic()->stop();
        } else {
            if(CServer::getOS() != CServer::OS_DARWIN) {
                CDevSuite::dnsMasq()->stop();
            }
        }



        CDevSuite::info('DevSuite services have been stopped.');
    }

}
