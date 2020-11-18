<?php

/**
 * Description of DependencyChecker
 *
 * @author Hery
 */
class CApp_Bootstrapper_DependencyChecker extends CBootstrap_BootstrapperAbstract {

    /**
     * Bootstrap the given application.
     *
     * @return void
     */
    public function bootstrap() {



        if (!function_exists('curl_init')) {
            throw new Exception("PHP curl extension must be installed/enabled to use CApp.");
        }
    }

}
