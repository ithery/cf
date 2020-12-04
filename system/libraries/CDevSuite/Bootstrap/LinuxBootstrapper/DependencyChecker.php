<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Bootstrap_LinuxBootstrapper_DependencyChecker extends CDevSuite_Bootstrap_Bootstrapper {

    public function bootstrap() {
        /**
         * Check the system's compatibility with DevSuite.
         */
        $inTestingEnvironment = strpos($_SERVER['SCRIPT_NAME'], 'phpunit') !== false;

        if (PHP_OS != 'Linux' && !$inTestingEnvironment) {
            echo 'DevSuite only supports Linux.' . PHP_EOL;

            exit(1);
        }

        if (version_compare(PHP_VERSION, '5.6', '<')) {
            echo "DevSuite requires PHP 5.6 or later.";

            exit(1);
        }
    }

}
