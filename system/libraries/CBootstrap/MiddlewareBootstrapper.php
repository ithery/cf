<?php

/**
 * Description of MiddlewareBootstrapper.
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
        CMiddleware::manager()->pushMiddleware(CHTTP_Cookie_Middleware_AddQueuedCookiesToResponse::class);
        CMiddleware::manager()->pushMiddleware(CHTTP_Middleware_CleanInput::class);
        if (!c::request()->is('cresenity/auth/ping')) {
            CMiddleware::manager()->pushMiddleware(CSession_Middleware_SessionMiddleware::class);
            CMiddleware::manager()->pushMiddleware(CView_Middleware_ShareErrorsFromSession::class);
        }
    }
}
