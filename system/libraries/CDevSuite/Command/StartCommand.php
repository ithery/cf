<?php

/**
 * Description of StartCommand
 */
class CDevSuite_Command_StartCommand extends CDevSuite_CommandAbstract {
    /**
     * Restart the DevSuite services (PHP-FPM, nginx, and Acrylic on Windows).
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        CDevSuite::phpFpm()->restart();
        CDevSuite::nginx()->restart();

        if (CServer::getOS() == CServer::OS_WINNT) {
            CDevSuite::acrylic()->restart();
        } else {
            //CDevSuite::dnsMasq()->restart();
        }

        CDevSuite::info('DevSuite services have been started.');
    }
}
