<?php

/**
 * Description of MiddlewareBootstrapper
 *
 * @author Hery
 */
class CBootstrap_MiddlewareBootstrapper extends CBootstrap_BootstrapperAbstract {
    /**
     * Bootstrap the given application.
     *
     * @return void
     */
    public function bootstrap() {
        $sessionMiddleware = new CSession_Middleware_SessionMiddleware();
        CMiddleware::manager()->pushMiddleware($sessionMiddleware);
    }
}
