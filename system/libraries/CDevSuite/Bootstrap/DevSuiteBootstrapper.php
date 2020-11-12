<?php

/**
 * Description of DevSuiteBootstrapper
 *
 * @author Hery
 */
class CDevSuite_Bootstrap_DevSuiteBootstrapper extends CDevSuite_Bootstrap_Bootstrapper {

    public function bootstrap() {
        if (!isset($_SERVER['HOME']) && isset($_SERVER['USERPROFILE'])) {
            $_SERVER['HOME'] = $_SERVER['USERPROFILE'];
        }
        if (!isset($_SERVER['USER']) && isset($_SERVER['USERNAME'])) {
            $_SERVER['USER'] = $_SERVER['USERNAME'];
        }
        
        if (!isset($_SERVER['HOME'])) {
            $_SERVER['HOME']='';
        }

        /*
         * Relocate config dir to ~/.config/devsuite/ if found in old location.
         */
        if (is_dir(CDevSuite::legacyHomePath()) && !is_dir(CDevSuite::homePath())) {
            CDevSuite::configuration()->createConfigurationDirectory();
        }
    }

}
