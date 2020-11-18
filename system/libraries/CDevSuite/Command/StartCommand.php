<?php

/**
 * Description of StartCommand
 *
 * @author Hery
 */
class CDevSuite_Command_StartCommand extends CDevSuite_CommandAbstract {

    public function run(CConsole_Command $cfCommand) {
        CDevSuite::phpFpm()->restart();
        CDevSuite::nginx()->restart();
        
        if(CServer::getOS() == CServer::OS_WINNT) {
            CDevSuite::acrylic()->restart();
        } else {
            CDevSuite::dnsMasq()->restart();
        }
        
        

        CDevSuite::info('DevSuite services have been started.');
    }

}
