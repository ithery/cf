<?php

/**
 * Description of DependencyBootstrapper
 *
 * @author Hery
 */
class CDevSuite_Bootstrap_WindowsBootstrapper_DependencyChecker extends CDevSuite_Bootstrap_Bootstrapper {
    public function bootstrap() {
        $inTestingEnvironment = strpos($_SERVER['SCRIPT_NAME'], 'phpunit') !== false;

        if (PHP_OS !== 'WINNT' && !$inTestingEnvironment) {
            echo 'DevSuite for Windows only supports the Windows operating system.' . PHP_EOL;

            exit(1);
        }

        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            echo 'DevSuite requires PHP 5.6.0 or later.';

            exit(1);
        }
    }
}
