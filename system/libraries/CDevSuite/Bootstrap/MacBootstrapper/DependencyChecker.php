<?php

/**
 * Description of DependencyChecker
 *
 * @author Hery
 */
class CDevSuite_Bootstrap_MacBootstrapper_DependencyChecker extends CDevSuite_Bootstrap_Bootstrapper {

    public function bootstrap() {
        /**
         * Check the system's compatibility with DevSuite.
         */
        $inTestingEnvironment = strpos($_SERVER['SCRIPT_NAME'], 'phpunit') !== false;

        if (PHP_OS !== 'Darwin' && !$inTestingEnvironment) {
            echo 'DevSuite only supports the Mac operating system.' . PHP_EOL;

            exit(1);
        }

        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            echo "DevSuite requires PHP 5.6 or later.";

            exit(1);
        }

        if (exec('which brew') == '' && !$inTestingEnvironment) {
            echo 'DevSuite requires Homebrew to be installed on your Mac.';

            exit(1);
        }
    }

}
